"""
Database loader for GNN Recommender
Loads users and friendships from MySQL database
"""

import os
import sys
import pymysql
from datetime import datetime
from dotenv import load_dotenv

# Load .env from parent directory
parent_dir = os.path.dirname(os.path.dirname(__file__))
load_dotenv(os.path.join(parent_dir, '.env'))


def get_db_connection():
    """Get database connection using .env credentials"""
    try:
        # Parse .env file manually for CodeIgniter format
        env_path = os.path.join(os.path.dirname(os.path.dirname(__file__)), '.env')
        
        db_config = {
            'host': 'localhost',
            'user': 'root',
            'password': '',
            'database': 'social_media',
            'port': 3306
        }
        
        # Read .env file and parse CodeIgniter format
        if os.path.exists(env_path):
            print(f"Reading .env from: {env_path}")
            with open(env_path, 'r', encoding='utf-8') as f:
                for line_num, line in enumerate(f, 1):
                    original_line = line
                    line = line.strip()
                    if line and not line.startswith('#'):
                        if '=' in line:
                            key, value = line.split('=', 1)
                            key = key.strip()
                            value = value.strip()
                            
                            # Debug: print key-value pairs related to database
                            if 'database.default.' in key:
                                print(f"Line {line_num}: {key} = '{value}'")
                            
                            if key == 'database.default.hostname':
                                db_config['host'] = value
                            elif key == 'database.default.username':
                                db_config['user'] = value
                            elif key == 'database.default.password':
                                db_config['password'] = value
                            elif key == 'database.default.database':
                                db_config['database'] = value
                            elif key == 'database.default.port':
                                try:
                                    db_config['port'] = int(value)
                                except:
                                    db_config['port'] = 3306
        
        print(f"\nParsed config:")
        print(f"  Host: {db_config['host']}")
        print(f"  User: {db_config['user']}")
        print(f"  Database: {db_config['database']}")
        print(f"  Password length: {len(db_config['password'])} chars")
        
        connection = pymysql.connect(
            host=db_config['host'],
            user=db_config['user'],
            password=db_config['password'],
            database=db_config['database'],
            port=db_config['port'],
            cursorclass=pymysql.cursors.DictCursor
        )
        return connection
    except Exception as e:
        print(f"Database connection error: {e}")
        raise


def load_users_from_db():
    """Load users from database"""
    conn = get_db_connection()
    
    try:
        with conn.cursor() as cursor:
            # Load all users
            cursor.execute("""
                SELECT id, created_at 
                FROM users 
                ORDER BY id
            """)
            db_users = cursor.fetchall()
            
            users = []
            for user in db_users:
                users.append({
                    'id': user['id'],
                    'created_at': user['created_at']
                })
            
            print(f"✓ Loaded {len(users)} users from database")
            return users
            
    finally:
        conn.close()


def load_friendships_from_db():
    """Load friendships from database"""
    conn = get_db_connection()
    
    try:
        with conn.cursor() as cursor:
            # First, check what columns exist in the friendships table
            cursor.execute("SHOW COLUMNS FROM friendships")
            columns = [col['Field'] for col in cursor.fetchall()]
            print(f"Friendships table columns: {columns}")
            
            # Try to find the correct column names
            if 'requester_id' in columns and 'receiver_id' in columns:
                query = "SELECT requester_id as user_id, receiver_id as friend_id FROM friendships WHERE status = 'accepted'"
            elif 'user_id' in columns and 'friend_id' in columns:
                query = "SELECT user_id, friend_id FROM friendships WHERE status = 'accepted'"
            elif 'sender_id' in columns and 'receiver_id' in columns:
                query = "SELECT sender_id as user_id, receiver_id as friend_id FROM friendships WHERE status = 'accepted'"
            elif 'from_user_id' in columns and 'to_user_id' in columns:
                query = "SELECT from_user_id as user_id, to_user_id as friend_id FROM friendships WHERE status = 'accepted'"
            else:
                # Fallback: print error
                print(f"⚠️  Could not detect friendship columns. Found: {columns}")
                return []
            
            print(f"Using query: {query}")
            cursor.execute(query)
            db_friendships = cursor.fetchall()
            
            friendships = [(f['user_id'], f['friend_id']) for f in db_friendships]
            
            print(f"✓ Loaded {len(friendships)} friendships from database")
            return friendships
            
    finally:
        conn.close()


def generate_dummy_friendships(users, num_friendships=50):
    """Generate dummy friendships for testing when database has too few"""
    import random
    
    print(f"\n⚠️  Generating {num_friendships} dummy friendships for testing...")
   
    user_ids = [u['id'] for u in users]
    friendships = set()
    
    # Generate random friendships
    while len(friendships) < min(num_friendships, len(user_ids) * 3):
        user1 = random.choice(user_ids)
        user2 = random.choice(user_ids)
        
        if user1 != user2:
            # Add both directions to make it undirected
            friendships.add((user1, user2))
    
    print(f"✓ Generated {len(friendships)} dummy friendships")
    return list(friendships)


def load_all_data(use_dummy_if_needed=True):
    """Load both users and friendships"""
    print("=" * 70)
    print("Loading data from database...")
    print("=" * 70)
    
    users = load_users_from_db()
    friendships = load_friendships_from_db()
    
    # If too few friendships, generate dummy data for testing
    if use_dummy_if_needed and len(friendships) < 10:
        print(f"\n⚠️  Only {len(friendships)} friendships found.")
        print("Generating dummy friendships for GNN training...")
        friendships = generate_dummy_friendships(users, num_friendships=len(users) * 2)
    
    print("=" * 70)
    print(f"Total: {len(users)} users, {len(friendships)} friendships")
    print("=" * 70)
    
    return users, friendships


if __name__ == "__main__":
    # Test database connection
    try:
        users, friendships = load_all_data()
        print("\n✓ Database connection successful!")
        print(f"\nSample user: {users[0] if users else 'No users'}")
        print(f"Sample friendship: {friendships[0] if friendships else 'No friendships'}")
    except Exception as e:
        print(f"\n✗ Error: {e}")
        sys.exit(1)
