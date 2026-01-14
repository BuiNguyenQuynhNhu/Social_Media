"""
Inference script for generating recommendations in production
Loads trained models and provides recommendation APIs
"""

import torch
import numpy as np
from typing import List, Dict, Tuple
import os
import json

from model import SocialGNN, FriendRecommender, PostRecommender, GroupRecommender
from processor import SocialDataProcessor, PostDataProcessor


class RecommendationEngine:
    """
    Main recommendation engine for production use
    """
    
    def __init__(self, model_dir='models', device='cpu'):
        """
        Initialize the recommendation engine
        
        Args:
            model_dir: Directory containing trained models
            device: 'cpu' or 'cuda'
        """
        self.device = device
        self.model_dir = model_dir
        
        # Models
        self.gnn_model = None
        self.friend_recommender = None
        self.post_recommender = None
        self.group_recommender = None
        
        # Data processors
        self.user_processor = SocialDataProcessor()
        self.post_processor = PostDataProcessor()
        
        # Graph data
        self.graph_data = None
        self.user_embeddings = None
        
        print("✓ Recommendation Engine initialized")
    
    def load_models(self):
        """Load all trained models"""
        gnn_path = os.path.join(self.model_dir, 'social_gnn_best.pt')
        
        if not os.path.exists(gnn_path):
            raise FileNotFoundError(f"GNN model not found at {gnn_path}. Train the model first.")
        
        # Load GNN model
        checkpoint = torch.load(gnn_path, map_location=self.device)
        
        # Initialize model with same architecture
        # Note: In production, save model config with checkpoint
        self.gnn_model = SocialGNN(
            num_node_features=16,  # Should be loaded from config
            hidden_dim=64,
            embedding_dim=32,
            num_heads=4
        ).to(self.device)
        
        self.gnn_model.load_state_dict(checkpoint['model_state_dict'])
        self.gnn_model.eval()
        
        # Initialize recommenders
        self.friend_recommender = FriendRecommender(self.gnn_model)
        self.post_recommender = PostRecommender(user_embedding_dim=32, post_embedding_dim=32)
        self.group_recommender = GroupRecommender(user_embedding_dim=32, group_embedding_dim=32)
        
        print("✓ Models loaded successfully")
    
    def update_graph(self, users: List[Dict], friendships: List[Tuple[int, int]]):
        """
        Update the social graph with latest data
        
        Args:
            users: List of user dictionaries from database
            friendships: List of friendship tuples
        """
        # Process data
        self.graph_data = self.user_processor.create_graph(users, friendships)
        
        # Generate user embeddings
        with torch.no_grad():
            self.user_embeddings = self.gnn_model.get_embeddings(
                self.graph_data.x.to(self.device),
                self.graph_data.edge_index.to(self.device)
            )
        
        print(f"✓ Graph updated with {len(users)} users and {len(friendships)} friendships")
    
    def recommend_friends(self, user_id: int, top_k: int = 10) -> List[Dict]:
        """
        Recommend friends for a user
        
        Args:
            user_id: Database user ID
            top_k: Number of recommendations
            
        Returns:
            List of dictionaries with user_id and score
        """
        if self.graph_data is None:
            raise ValueError("Graph not initialized. Call update_graph() first.")
        
        # Get user index
        user_idx = self.user_processor.get_user_index(user_id)
        if user_idx == -1:
            return []
        
        # Get recommendations
        recommendations = self.friend_recommender.recommend_friends(
            user_idx,
            self.graph_data.x.to(self.device),
            self.graph_data.edge_index.to(self.device),
            top_k=top_k
        )
        
        # Convert to user IDs
        results = []
        for user_idx, score in recommendations:
            actual_user_id = self.user_processor.get_user_id(user_idx)
            if actual_user_id != -1:
                results.append({
                    'user_id': actual_user_id,
                    'score': float(score),
                    'reason': 'Similar interests and mutual connections'
                })
        
        return results
    
    def recommend_posts(self, user_id: int, posts: List[Dict], top_k: int = 10) -> List[Dict]:
        """
        Recommend posts for a user
        
        Args:
            user_id: Database user ID
            posts: List of post dictionaries
            top_k: Number of recommendations
            
        Returns:
            List of dictionaries with post_id and score
        """
        if self.user_embeddings is None:
            raise ValueError("User embeddings not initialized. Call update_graph() first.")
        
        # Get user index and embedding
        user_idx = self.user_processor.get_user_index(user_id)
        if user_idx == -1:
            return []
        
        user_embedding = self.user_embeddings[user_idx].unsqueeze(0)
        
        # Process posts
        post_features = self.post_processor.process_posts(posts)
        
        # Calculate scores for all posts
        scores = []
        for i, post_emb in enumerate(post_features):
            post_emb = post_emb.unsqueeze(0).to(self.device)
            score = self.post_recommender(user_embedding, post_emb)
            scores.append((i, score.item()))
        
        # Sort by score
        scores.sort(key=lambda x: x[1], reverse=True)
        
        # Return top-k
        results = []
        for i, (post_idx, score) in enumerate(scores[:top_k]):
            post_id = self.post_processor.reverse_post_map.get(post_idx)
            if post_id is not None:
                results.append({
                    'post_id': post_id,
                    'score': float(score),
                    'rank': i + 1
                })
        
        return results
    
    def recommend_groups(self, user_id: int, groups: List[Dict], top_k: int = 10) -> List[Dict]:
        """
        Recommend groups for a user
        
        Args:
            user_id: Database user ID
            groups: List of group dictionaries
            top_k: Number of recommendations
            
        Returns:
            List of dictionaries with group_id and score
        """
        if self.user_embeddings is None:
            raise ValueError("User embeddings not initialized. Call update_graph() first.")
        
        # Get user index and embedding
        user_idx = self.user_processor.get_user_index(user_id)
        if user_idx == -1:
            return []
        
        user_embedding = self.user_embeddings[user_idx]
        
        # Create random group embeddings (in production, train group embeddings)
        num_groups = len(groups)
        group_embeddings = torch.randn(num_groups, 32).to(self.device)
        
        # Get recommendations
        recommendations = self.group_recommender.recommend_groups(
            user_embedding,
            group_embeddings,
            top_k=top_k
        )
        
        # Format results
        results = []
        for i, (group_idx, score) in enumerate(recommendations):
            if group_idx < len(groups):
                results.append({
                    'group_id': groups[group_idx]['id'],
                    'score': float(score),
                    'rank': i + 1
                })
        
        return results
    
    def get_user_embedding(self, user_id: int) -> np.ndarray:
        """
        Get the embedding vector for a user
        
        Args:
            user_id: Database user ID
            
        Returns:
            Embedding vector as numpy array
        """
        user_idx = self.user_processor.get_user_index(user_id)
        if user_idx == -1 or self.user_embeddings is None:
            return None
        
        embedding = self.user_embeddings[user_idx].cpu().numpy()
        return embedding


def example_usage():
    """
    Example usage of the recommendation engine
    """
    print("=" * 60)
    print("Recommendation Engine - Inference Example")
    print("=" * 60)
    
    # Initialize engine
    engine = RecommendationEngine(
        model_dir=os.path.join(os.path.dirname(__file__), 'models'),
        device='cpu'
    )
    
    # Load models
    try:
        engine.load_models()
    except FileNotFoundError as e:
        print(f"\n⚠️  {e}")
        print("Please train the models first by running: python train.py")
        return
    
    # Load sample data
    from data_processor import load_sample_data
    users, friendships = load_sample_data()
    
    # Update graph
    print("\nUpdating social graph...")
    engine.update_graph(users, friendships)
    
    # Test friend recommendations
    print("\n[Friend Recommendations]")
    test_user_id = 0  # Database user ID
    friend_recs = engine.recommend_friends(test_user_id, top_k=5)
    
    print(f"\nTop 5 friend recommendations for User {test_user_id}:")
    for rec in friend_recs:
        print(f"  • User {rec['user_id']} (score: {rec['score']:.4f})")
    
    # Test user embedding
    print("\n[User Embedding]")
    embedding = engine.get_user_embedding(test_user_id)
    if embedding is not None:
        print(f"User {test_user_id} embedding shape: {embedding.shape}")
        print(f"Embedding preview: {embedding[:5]}")
    
    print("\n" + "=" * 60)
    print("Inference example completed!")
    print("=" * 60)


if __name__ == "__main__":
    example_usage()
