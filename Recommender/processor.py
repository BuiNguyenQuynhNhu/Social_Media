"""
Data preprocessing utilities for the GNN recommender system
Handles data loading from the social media database and conversion to graph format
"""

import numpy as np
import torch
from torch_geometric.data import Data
from typing import List, Dict, Tuple
import json


class SocialDataProcessor:
    """
    Process social media data for GNN training
    """
    
    def __init__(self):
        self.user_id_map = {}  # Map database IDs to sequential indices
        self.reverse_user_map = {}
        self.feature_dim = None
        
    def extract_user_features(self, user_data: Dict) -> np.ndarray:
        """
        Extract numerical features from user data
        
        Args:
            user_data: Dictionary containing user information
            
        Returns:
            Feature vector as numpy array
        """
        features = []
        
        # Example features (customize based on your database schema)
        # Age (normalized)
        age = user_data.get('age', 25)
        features.append(age / 100.0)
        
        # Account age in days (normalized)
        account_age = user_data.get('account_age_days', 0)
        features.append(min(account_age / 365.0, 10.0))
        
        # Activity metrics (normalized)
        post_count = user_data.get('post_count', 0)
        features.append(min(post_count / 100.0, 10.0))
        
        friend_count = user_data.get('friend_count', 0)
        features.append(min(friend_count / 100.0, 10.0))
        
        comment_count = user_data.get('comment_count', 0)
        features.append(min(comment_count / 100.0, 10.0))
        
        like_count = user_data.get('like_count', 0)
        features.append(min(like_count / 100.0, 10.0))
        
        # Binary features
        is_verified = 1.0 if user_data.get('is_verified', False) else 0.0
        features.append(is_verified)
        
        has_profile_pic = 1.0 if user_data.get('has_profile_pic', False) else 0.0
        features.append(has_profile_pic)
        
        # Categorical features (one-hot encoded)
        # Gender
        gender = user_data.get('gender', 'other')
        features.extend([
            1.0 if gender == 'male' else 0.0,
            1.0 if gender == 'female' else 0.0,
            1.0 if gender == 'other' else 0.0
        ])
        
        # Interest categories (multi-hot encoding)
        interests = user_data.get('interests', [])
        interest_categories = ['technology', 'sports', 'music', 'art', 'food']
        for category in interest_categories:
            features.append(1.0 if category in interests else 0.0)
        
        return np.array(features, dtype=np.float32)
    
    def process_users(self, users: List[Dict]) -> torch.Tensor:
        """
        Process a list of users into a feature matrix
        
        Args:
            users: List of user dictionaries from database
            
        Returns:
            Feature matrix as torch tensor [num_users, feature_dim]
        """
        # Create user ID mapping
        for idx, user in enumerate(users):
            user_id = user['id']
            self.user_id_map[user_id] = idx
            self.reverse_user_map[idx] = user_id
        
        # Extract features for all users
        features = []
        for user in users:
            user_features = self.extract_user_features(user)
            features.append(user_features)
        
        # Convert to tensor
        feature_matrix = torch.tensor(np.array(features), dtype=torch.float32)
        self.feature_dim = feature_matrix.shape[1]
        
        return feature_matrix
    
    def process_friendships(self, friendships: List[Tuple[int, int]]) -> torch.Tensor:
        """
        Process friendships into edge index format
        
        Args:
            friendships: List of tuples (user_id_1, user_id_2)
            
        Returns:
            Edge index tensor [2, num_edges]
        """
        edges = []
        
        for user1_id, user2_id in friendships:
            # Map database IDs to indices
            if user1_id in self.user_id_map and user2_id in self.user_id_map:
                idx1 = self.user_id_map[user1_id]
                idx2 = self.user_id_map[user2_id]
                
                # Add bidirectional edges (undirected graph)
                edges.append([idx1, idx2])
                edges.append([idx2, idx1])
        
        if edges:
            edge_index = torch.tensor(edges, dtype=torch.long).t().contiguous()
        else:
            edge_index = torch.empty((2, 0), dtype=torch.long)
        
        return edge_index
    
    def create_graph(self, users: List[Dict], friendships: List[Tuple[int, int]]) -> Data:
        """
        Create a complete PyTorch Geometric Data object
        
        Args:
            users: List of user dictionaries
            friendships: List of friendship tuples
            
        Returns:
            PyTorch Geometric Data object
        """
        x = self.process_users(users)
        edge_index = self.process_friendships(friendships)
        
        data = Data(x=x, edge_index=edge_index)
        
        return data
    
    def get_user_index(self, user_id: int) -> int:
        """Convert database user ID to graph index"""
        return self.user_id_map.get(user_id, -1)
    
    def get_user_id(self, index: int) -> int:
        """Convert graph index to database user ID"""
        return self.reverse_user_map.get(index, -1)


class PostDataProcessor:
    """
    Process post data for recommendation
    """
    
    def __init__(self):
        self.post_id_map = {}
        self.reverse_post_map = {}
        
    def extract_post_features(self, post_data: Dict) -> np.ndarray:
        """
        Extract features from post data
        
        Args:
            post_data: Dictionary containing post information
            
        Returns:
            Feature vector as numpy array
        """
        features = []
        
        # Post metrics (normalized)
        like_count = post_data.get('like_count', 0)
        features.append(min(like_count / 100.0, 10.0))
        
        comment_count = post_data.get('comment_count', 0)
        features.append(min(comment_count / 50.0, 10.0))
        
        share_count = post_data.get('share_count', 0)
        features.append(min(share_count / 20.0, 10.0))
        
        # Post age in hours (normalized)
        age_hours = post_data.get('age_hours', 0)
        features.append(min(age_hours / 168.0, 10.0))  # 168 hours = 1 week
        
        # Content type (one-hot)
        content_type = post_data.get('type', 'text')
        features.extend([
            1.0 if content_type == 'text' else 0.0,
            1.0 if content_type == 'image' else 0.0,
            1.0 if content_type == 'video' else 0.0,
            1.0 if content_type == 'link' else 0.0
        ])
        
        # Has media
        has_media = 1.0 if post_data.get('has_media', False) else 0.0
        features.append(has_media)
        
        # Content length (normalized)
        content_length = post_data.get('content_length', 0)
        features.append(min(content_length / 500.0, 10.0))
        
        # Topic categories (multi-hot encoding)
        topics = post_data.get('topics', [])
        topic_categories = ['news', 'personal', 'question', 'event', 'meme']
        for category in topic_categories:
            features.append(1.0 if category in topics else 0.0)
        
        return np.array(features, dtype=np.float32)
    
    def process_posts(self, posts: List[Dict]) -> torch.Tensor:
        """
        Process a list of posts into a feature matrix
        
        Args:
            posts: List of post dictionaries
            
        Returns:
            Feature matrix as torch tensor
        """
        # Create post ID mapping
        for idx, post in enumerate(posts):
            post_id = post['id']
            self.post_id_map[post_id] = idx
            self.reverse_post_map[idx] = post_id
        
        # Extract features
        features = []
        for post in posts:
            post_features = self.extract_post_features(post)
            features.append(post_features)
        
        return torch.tensor(np.array(features), dtype=torch.float32)


class InteractionDataProcessor:
    """
    Process user-post interactions for training
    """
    
    @staticmethod
    def create_training_data(interactions: List[Dict]) -> Tuple[torch.Tensor, torch.Tensor, torch.Tensor]:
        """
        Create training data from user-post interactions
        
        Args:
            interactions: List of interaction dictionaries
                - user_id: int
                - post_id: int
                - interaction_type: str (like, comment, share, view)
                - timestamp: datetime
                
        Returns:
            Tuple of (user_indices, post_indices, labels)
        """
        user_indices = []
        post_indices = []
        labels = []
        
        # Map interaction types to weights
        interaction_weights = {
            'view': 0.1,
            'like': 0.5,
            'comment': 0.8,
            'share': 1.0
        }
        
        for interaction in interactions:
            user_indices.append(interaction['user_idx'])
            post_indices.append(interaction['post_idx'])
            
            interaction_type = interaction.get('interaction_type', 'view')
            weight = interaction_weights.get(interaction_type, 0.1)
            labels.append(weight)
        
        return (
            torch.tensor(user_indices, dtype=torch.long),
            torch.tensor(post_indices, dtype=torch.long),
            torch.tensor(labels, dtype=torch.float32)
        )


def load_sample_data():
    """
    Load sample data for testing (replace with actual database queries)
    """
    # Sample users
    users = [
        {
            'id': i,
            'age': 20 + (i % 30),
            'account_age_days': 100 + i * 10,
            'post_count': i * 5,
            'friend_count': i * 3,
            'comment_count': i * 2,
            'like_count': i * 10,
            'is_verified': i % 5 == 0,
            'has_profile_pic': i % 2 == 0,
            'gender': ['male', 'female', 'other'][i % 3],
            'interests': ['technology', 'sports'] if i % 2 == 0 else ['music', 'art']
        }
        for i in range(100)
    ]
    
    # Sample friendships
    friendships = [
        (i, (i + 1) % 100) for i in range(100)
    ] + [
        (i, (i + 5) % 100) for i in range(0, 100, 5)
    ]
    
    return users, friendships

