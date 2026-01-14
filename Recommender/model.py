"""
Graph Neural Network Recommender System for Social Media
This model uses Graph Convolutional Networks (GCN) to recommend:
- Friend suggestions
- Post recommendations
- Group suggestions
"""

import torch
import torch.nn as nn
import torch.nn.functional as F
from torch_geometric.nn import GCNConv, SAGEConv, GATConv
from torch_geometric.data import Data


class SocialGNN(nn.Module):
    """
    Graph Neural Network for Social Media Recommendations
    Uses Graph Attention Network (GAT) for better performance on social graphs
    """
    
    def __init__(self, num_node_features, hidden_dim=64, embedding_dim=32, num_heads=4):
        """
        Args:
            num_node_features: Number of input features per node (user)
            hidden_dim: Dimension of hidden layers
            embedding_dim: Dimension of final node embeddings
            num_heads: Number of attention heads for GAT
        """
        super(SocialGNN, self).__init__()
        
        # Graph Attention layers
        self.conv1 = GATConv(num_node_features, hidden_dim, heads=num_heads, dropout=0.6)
        self.conv2 = GATConv(hidden_dim * num_heads, embedding_dim, heads=1, concat=False, dropout=0.6)
        
        # Dropout for regularization
        self.dropout = nn.Dropout(p=0.6)
        
    def forward(self, x, edge_index):
        """
        Forward pass through the network
        
        Args:
            x: Node feature matrix [num_nodes, num_features]
            edge_index: Graph connectivity [2, num_edges]
            
        Returns:
            Node embeddings [num_nodes, embedding_dim]
        """
        # First GAT layer with ELU activation
        x = F.elu(self.conv1(x, edge_index))
        x = self.dropout(x)
        
        # Second GAT layer
        x = self.conv2(x, edge_index)
        
        return x
    
    def get_embeddings(self, x, edge_index):
        """Get node embeddings for recommendation"""
        self.eval()
        with torch.no_grad():
            embeddings = self.forward(x, edge_index)
        return embeddings


class FriendRecommender(nn.Module):
    """
    Friend recommendation model using GNN embeddings
    """
    
    def __init__(self, gnn_model):
        super(FriendRecommender, self).__init__()
        self.gnn = gnn_model
        
    def recommend_friends(self, user_id, x, edge_index, top_k=10):
        """
        Recommend friends for a given user
        
        Args:
            user_id: Target user ID
            x: Node features
            edge_index: Graph structure
            top_k: Number of recommendations to return
            
        Returns:
            List of tuples (user_id, score)
        """
        embeddings = self.gnn.get_embeddings(x, edge_index)
        
        # Get user embedding
        user_embedding = embeddings[user_id].unsqueeze(0)
        
        # Calculate similarity scores with all other users
        similarities = F.cosine_similarity(user_embedding, embeddings)
        
        # Get existing friends (edges)
        existing_friends = set()
        for i in range(edge_index.shape[1]):
            if edge_index[0, i] == user_id:
                existing_friends.add(edge_index[1, i].item())
            elif edge_index[1, i] == user_id:
                existing_friends.add(edge_index[0, i].item())
        
        # Filter out self and existing friends
        similarities[user_id] = -float('inf')
        for friend_id in existing_friends:
            similarities[friend_id] = -float('inf')
        
        # Get top-k recommendations
        top_scores, top_indices = torch.topk(similarities, min(top_k, len(similarities)))
        
        recommendations = [
            (idx.item(), score.item()) 
            for idx, score in zip(top_indices, top_scores)
            if score > -float('inf')
        ]
        
        return recommendations


class PostRecommender(nn.Module):
    """
    Post recommendation model using user and post embeddings
    """
    
    def __init__(self, user_embedding_dim=32, post_embedding_dim=32, hidden_dim=64):
        super(PostRecommender, self).__init__()
        
        # MLP for combining user and post features
        self.fc1 = nn.Linear(user_embedding_dim + post_embedding_dim, hidden_dim)
        self.fc2 = nn.Linear(hidden_dim, hidden_dim // 2)
        self.fc3 = nn.Linear(hidden_dim // 2, 1)
        
        self.dropout = nn.Dropout(p=0.3)
        
    def forward(self, user_embedding, post_embedding):
        """
        Predict user's interest in a post
        
        Args:
            user_embedding: User embedding vector
            post_embedding: Post embedding vector
            
        Returns:
            Interest score (0-1)
        """
        # Concatenate user and post embeddings
        combined = torch.cat([user_embedding, post_embedding], dim=-1)
        
        # MLP layers
        x = F.relu(self.fc1(combined))
        x = self.dropout(x)
        x = F.relu(self.fc2(x))
        x = self.dropout(x)
        x = torch.sigmoid(self.fc3(x))
        
        return x.squeeze()


class GroupRecommender(nn.Module):
    """
    Group recommendation model using GNN embeddings
    """
    
    def __init__(self, user_embedding_dim=32, group_embedding_dim=32, hidden_dim=64):
        super(GroupRecommender, self).__init__()
        
        # MLP for combining user and group features
        self.fc1 = nn.Linear(user_embedding_dim + group_embedding_dim, hidden_dim)
        self.fc2 = nn.Linear(hidden_dim, hidden_dim // 2)
        self.fc3 = nn.Linear(hidden_dim // 2, 1)
        
        self.dropout = nn.Dropout(p=0.3)
        
    def forward(self, user_embedding, group_embedding):
        """
        Predict user's interest in a group
        
        Args:
            user_embedding: User embedding vector
            group_embedding: Group embedding vector
            
        Returns:
            Interest score (0-1)
        """
        # Concatenate user and group embeddings
        combined = torch.cat([user_embedding, group_embedding], dim=-1)
        
        # MLP layers
        x = F.relu(self.fc1(combined))
        x = self.dropout(x)
        x = F.relu(self.fc2(x))
        x = self.dropout(x)
        x = torch.sigmoid(self.fc3(x))
        
        return x.squeeze()
    
    def recommend_groups(self, user_embedding, group_embeddings, top_k=10):
        """
        Recommend groups for a user
        
        Args:
            user_embedding: User embedding vector [embedding_dim]
            group_embeddings: Group embedding matrix [num_groups, embedding_dim]
            top_k: Number of recommendations
            
        Returns:
            List of tuples (group_id, score)
        """
        # Expand user embedding to match number of groups
        user_emb_expanded = user_embedding.unsqueeze(0).expand(group_embeddings.shape[0], -1)
        
        # Calculate scores for all groups
        scores = self.forward(user_emb_expanded, group_embeddings)
        
        # Get top-k recommendations
        top_scores, top_indices = torch.topk(scores, min(top_k, len(scores)))
        
        recommendations = [
            (idx.item(), score.item()) 
            for idx, score in zip(top_indices, top_scores)
        ]
        
        return recommendations


def create_social_graph(users, friendships):
    """
    Create a PyTorch Geometric graph from user and friendship data
    
    Args:
        users: List of user dictionaries with features
        friendships: List of tuples (user_id_1, user_id_2)
        
    Returns:
        PyTorch Geometric Data object
    """
    # Create node features (this is a simple example, customize based on your data)
    num_users = len(users)
    feature_dim = 16  # Example: age, gender, interests, activity level, etc.
    
    # Initialize random features (replace with actual user features)
    x = torch.randn(num_users, feature_dim)
    
    # Create edge index from friendships
    edge_list = []
    for user1, user2 in friendships:
        edge_list.append([user1, user2])
        edge_list.append([user2, user1])  # Undirected graph
    
    if edge_list:
        edge_index = torch.tensor(edge_list, dtype=torch.long).t().contiguous()
    else:
        edge_index = torch.empty((2, 0), dtype=torch.long)
    
    # Create PyTorch Geometric Data object
    data = Data(x=x, edge_index=edge_index)
    
    return data
