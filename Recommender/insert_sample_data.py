"""
Insert sample social network data into your database
This creates 100 users and 120 friendships so you can test the GNN recommender
"""

import os
import sys
import pymysql
from datetime import datetime
import hashlib
import random

# Import only the db connection function
sys.path.append(os.path.dirname(__file__))
from db_loader import get_db_connection


def generate_sample_friendships(num_users=100, num_friendships=120):
    """Generate random but realistic friendship network"""
    friendships = set()
    
    # Create a realistic network with some hubs and clusters
    for i in range(num_friendships):
        # Bias towards earlier users being more connected (realistic)
        if random.random() < 0.3:
            user1 = random.randint(0, min(20, num_users-1))
        else:
            user1 = random.randint(0, num_users-1)
        
        user2 = random.randint(0, num_users-1)
        
        if user1 != user2:
            # Add both directions (undirected friendship)
            friendships.add((min(user1, user2), max(user1, user2)))
    
    return list(friendships)


def create_sample_users(num_users=100):
    """Generate sample user data"""
    users = []
    for i in range(num_users):
        users.append({
            'username': f'testuser{i}',
            'email': f'testuser{i}@example.com',
            'fullname': f'Test User {i}',
            'password': hashlib.md5(f'password{i}'.encode()).hexdigest(),
            'bio': f'Sample user {i} for GNN recommender testing',
            'avatar': 'default_avatar.png',
            'created_at': datetime.now()
        })
    return users


def insert_users_to_db(users):
    """Insert users into database (users + profiles tables)"""
    import uuid
    
    conn = get_db_connection()
    user_id_map = {}
    
    try:
        with conn.cursor() as cursor:
            print(f"Inserting {len(users)} users...")
            
            for i, user in enumerate(users):
                # Check if user already exists by email
                cursor.execute(
                    "SELECT id FROM users WHERE email = %s",
                    (user['email'],)
                )
                existing = cursor.fetchone()
                
                if existing:
                    user_id = existing['id']
                    user_id_map[i] = user_id
                    print(f"  User {i} ({user['email']}) already exists")
                else:
                    # Generate UUID for new user
                    user_id = str(uuid.uuid4())
                    
                    # Insert into users table
                    cursor.execute("""
                        INSERT INTO users (id, email, password, avatar, type, created_at)
                        VALUES (%s, %s, %s, %s, %s, %s)
                    """, (
                        user_id,
                        user['email'],
                        user['password'],
                        user['avatar'],
                        False,  # type = false (regular user)
                        user['created_at']
                    ))
                    
                    # Insert into profiles table
                    # Split fullname into firstname/lastname
                    name_parts = user['fullname'].split(' ', 1)
                    firstname = name_parts[0] if len(name_parts) > 0 else 'User'
                    lastname = name_parts[1] if len(name_parts) > 1 else f'{i}'
                    
                    cursor.execute("""
                        INSERT INTO profiles (id, firstname, lastname, gender, birth)
                        VALUES (%s, %s, %s, %s, %s)
                    """, (
                        user_id,
                        firstname,
                        lastname,
                        'other',  # default gender
                        '2000-01-01'  # default birth date
                    ))
                    
                    user_id_map[i] = user_id
                    
            conn.commit()
            print(f"✓ Inserted {len(users)} users successfully")
            
    except Exception as e:
        conn.rollback()
        print(f"✗ Error inserting users: {e}")
        raise
    finally:
        conn.close()
    
    return user_id_map


def insert_friendships_to_db(friendships, user_id_map):
    """Insert friendships into database"""
    import uuid
    
    conn = get_db_connection()
    
    try:
        with conn.cursor() as cursor:
            print(f"\nInserting {len(friendships)} friendships...")
            inserted = 0
            
            for user1_idx, user2_idx in friendships:
                user1_id = user_id_map.get(user1_idx)
                user2_id = user_id_map.get(user2_idx)
                
                if not user1_id or not user2_id:
                    continue
                
                cursor.execute("""
                    SELECT id FROM friendships 
                    WHERE (requester_id = %s AND receiver_id = %s)
                       OR (requester_id = %s AND receiver_id = %s)
                """, (user1_id, user2_id, user2_id, user1_id))
                
                if cursor.fetchone():
                    continue
                
                # Generate UUID for friendship
                friendship_id = str(uuid.uuid4())
                
                cursor.execute("""
                    INSERT INTO friendships (id, requester_id, receiver_id, status, created_at)
                    VALUES (%s, %s, %s, %s, %s)
                """, (friendship_id, user1_id, user2_id, 'accepted', datetime.now()))
                
                inserted += 1
            
            conn.commit()
            print(f"✓ Inserted {inserted} friendships successfully")
            
    except Exception as e:
        conn.rollback()
        print(f"✗ Error inserting friendships: {e}")
        raise
    finally:
        conn.close()


def main():
    print("=" * 70)
    print("INSERT SAMPLE DATA INTO DATABASE")
    print("=" * 70)
    
    print("\n[1] Generating sample data...")
    users = create_sample_users(100)
    friendships = generate_sample_friendships(100, 120)
    print(f"✓ Generated {len(users)} users and {len(friendships)} friendships")
    
    print("\n[2] Inserting users into database...")
    user_id_map = insert_users_to_db(users)
    
    print("\n[3] Inserting friendships into database...")
    insert_friendships_to_db(friendships, user_id_map)
    
    import json
    mapping_file = os.path.join(os.path.dirname(__file__), 'user_id_mapping.json')
    with open(mapping_file, 'w') as f:
        json_map = {str(k): str(v) for k, v in user_id_map.items()}
        json.dump(json_map, f, indent=2)
    print(f"\n✓ User ID mapping saved to: {mapping_file}")
    
    print("\n" + "=" * 70)
    print("SAMPLE DATA INSERTED SUCCESSFULLY!")
    print("=" * 70)
    print("\nYou can now:")
    print("1. Login as testuser0-99 (password: password0-99)")
    print("2. Train model with this data: python train_db.py")
    print("3. Get friend recommendations via the API")
    print("\nExample users:")
    for i in range(5):
        print(f"  - Username: testuser{i}, Password: password{i}")
    print("=" * 70)


if __name__ == "__main__":
    try:
        main()
    except Exception as e:
        print(f"\n✗ Error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
