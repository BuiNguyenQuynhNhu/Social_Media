"""
Train GNN model with data from database
"""

import os
import sys
import torch
from datetime import datetime

# Add current directory to path
sys.path.append(os.path.dirname(__file__))

from model import SocialGNN, FriendRecommender
from processor import SocialDataProcessor
from db_loader import load_all_data
from trainer import GNNTrainer


def main():
    print("=" * 70)
    print("GNN RECOMMENDER - TRAINING WITH DATABASE")
    print("=" * 70)
    
    # Configuration
    device = 'cuda' if torch.cuda.is_available() else 'cpu'
    print(f"\n✓ Using device: {device}")
    
    # Load data from database
    print("\n[1] Loading data from database...")
    try:
        users, friendships = load_all_data()
    except Exception as e:
        print(f"\n✗ Failed to load data from database: {e}")
        print("\nPlease check your database configuration in .env file")
        sys.exit(1)
    
    if not users or not friendships:
        print("\n⚠️  Warning: No data found in database!")
        print("Please ensure you have users and friendships in your database")
        sys.exit(1)
    
    # Process data
    print("\n[2] Processing data...")
    processor = SocialDataProcessor()
    graph_data = processor.create_graph(users, friendships)
    
    print(f"✓ Processed graph:")
    print(f"  - Nodes (users): {graph_data.num_nodes}")
    print(f"  - Edges (friendships): {graph_data.edge_index.shape[1]}")
    print(f"  - Feature dimension: {graph_data.x.shape[1]}")
    
    # Initialize model
    print("\n[3] Initializing GNN model...")
    num_features = graph_data.x.shape[1]
    model = SocialGNN(
        num_node_features=num_features,
        hidden_dim=64,
        embedding_dim=32,
        num_heads=4
    )
    print(f"✓ Model initialized with {sum(p.numel() for p in model.parameters())} parameters")
    
    # Train model
    print("\n[4] Training model...")
    trainer = GNNTrainer(model, device=device, learning_rate=0.01)
    
    save_dir = os.path.join(os.path.dirname(__file__), 'models')
    os.makedirs(save_dir, exist_ok=True)
    
    history = trainer.train(
        graph_data,
        epochs=100,
        save_path=os.path.join(save_dir, 'social_gnn_best.pt'),
        verbose=True
    )
    
    print("\n[5] Training complete!")
    print(f"✓ Final train loss: {history['train_loss'][-1]:.4f}")
    print(f"✓ Final val loss: {history['val_loss'][-1]:.4f}")
    print(f"✓ Model saved to: {os.path.join(save_dir, 'social_gnn_best.pt')}")
    
    # Test recommendations
    print("\n[6] Testing friend recommendations...")
    friend_recommender = FriendRecommender(model)
    
    # Test with first user
    test_user_id = 0
    recommendations = friend_recommender.recommend_friends(
        test_user_id,
        graph_data.x,
        graph_data.edge_index,
        top_k=5
    )
    
    print(f"\nTop 5 friend recommendations for User ID {users[test_user_id]['id']}:")
    for i, (user_idx, score) in enumerate(recommendations, 1):
        actual_user_id = processor.get_user_id(user_idx)
        print(f"  {i}. User ID {actual_user_id} (score: {score:.4f})")
    
    # Save metadata
    metadata = {
        'trained_at': datetime.now().isoformat(),
        'num_users': len(users),
        'num_friendships': len(friendships),
        'device': device,
        'epochs': 100,
        'final_train_loss': history['train_loss'][-1],
        'final_val_loss': history['val_loss'][-1]
    }
    
    metadata_path = os.path.join(save_dir, 'training_metadata.json')
    import json
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=2)
    
    print(f"\n✓ Metadata saved to: {metadata_path}")
    
    print("\n" + "=" * 70)
    print("TRAINING COMPLETE!")
    print("=" * 70)
    print("\nNext steps:")
    print("1. Start the API server: python server.py")
    print("2. Test API: curl http://localhost:5000/health")
    print("3. Integrate with your web application")
    print("=" * 70)


if __name__ == "__main__":
    main()
