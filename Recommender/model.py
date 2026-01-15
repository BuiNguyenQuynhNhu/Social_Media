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
    def __init__(self, num_node_features, hidden_dim=64, embedding_dim=32, num_heads=4):

        super(SocialGNN, self).__init__()
        
        # Graph Attention layers
        self.conv1 = GATConv(num_node_features, hidden_dim, heads=num_heads, dropout=0.6)
        self.conv2 = GATConv(hidden_dim * num_heads, embedding_dim, heads=1, concat=False, dropout=0.6)
        
        # Dropout for regularization
        self.dropout = nn.Dropout(p=0.6)
        
    def forward(self, x, edge_index):

        x = F.elu(self.conv1(x, edge_index))
        x = self.dropout(x)
        
        # Second GAT layer
        x = self.conv2(x, edge_index)
        
        return x
    
    def get_embeddings(self, x, edge_index):
        """Get node embeddings for recommendation"""
        self.eval()
        device = next(self.parameters()).device
        with torch.no_grad():
            embeddings = self.forward(x.to(device), edge_index.to(device))
        return embeddings


class FriendRecommender(nn.Module):
    
    def __init__(self, gnn_model):
        super(FriendRecommender, self).__init__()
        self.gnn = gnn_model
        
    def recommend_friends(self, user_id, x, edge_index, top_k=10):
        embeddings = self.gnn.get_embeddings(x, edge_index)
        device = embeddings.device
        
        # Get user embedding
        user_embedding = embeddings[user_id].unsqueeze(0)
        similarities = F.cosine_similarity(user_embedding, embeddings)
        
        # Get existing friends (edges)
        existing_friends = set()
        edge_index_cpu = edge_index.cpu()
        for i in range(edge_index_cpu.shape[1]):
            if edge_index_cpu[0, i] == user_id:
                existing_friends.add(edge_index_cpu[1, i].item())
            elif edge_index_cpu[1, i] == user_id:
                existing_friends.add(edge_index_cpu[0, i].item())

        similarities[user_id] = -float('inf')
        for friend_id in existing_friends:
            similarities[friend_id] = -float('inf')
        top_scores, top_indices = torch.topk(similarities, min(top_k, len(similarities)))
        
        recommendations = [
            (idx.item(), score.item()) 
            for idx, score in zip(top_indices, top_scores)
            if score > -float('inf')
        ]
        
        return recommendations


class PostRecommender(nn.Module):

    def __init__(self, user_embedding_dim=32, post_embedding_dim=32, hidden_dim=64):
        super(PostRecommender, self).__init__()
        self.fc1 = nn.Linear(user_embedding_dim + post_embedding_dim, hidden_dim)
        self.fc2 = nn.Linear(hidden_dim, hidden_dim // 2)
        self.fc3 = nn.Linear(hidden_dim // 2, 1)
        
        self.dropout = nn.Dropout(p=0.3)
        
    def forward(self, user_embedding, post_embedding):
        combined = torch.cat([user_embedding, post_embedding], dim=-1)
        x = F.relu(self.fc1(combined))
        x = self.dropout(x)
        x = F.relu(self.fc2(x))
        x = self.dropout(x)
        x = torch.sigmoid(self.fc3(x))
        
        return x.squeeze()


class GroupRecommender(nn.Module):

    def __init__(self, user_embedding_dim=32, group_embedding_dim=32, hidden_dim=64):
        super(GroupRecommender, self).__init__()
        self.fc1 = nn.Linear(user_embedding_dim + group_embedding_dim, hidden_dim)
        self.fc2 = nn.Linear(hidden_dim, hidden_dim // 2)
        self.fc3 = nn.Linear(hidden_dim // 2, 1)
        
        self.dropout = nn.Dropout(p=0.3)
        
    def forward(self, user_embedding, group_embedding):
        combined = torch.cat([user_embedding, group_embedding], dim=-1)
        x = F.relu(self.fc1(combined))
        x = self.dropout(x)
        x = F.relu(self.fc2(x))
        x = self.dropout(x)
        x = torch.sigmoid(self.fc3(x))
        
        return x.squeeze()
    
    def recommend_groups(self, user_embedding, group_embeddings, top_k=10):
        user_emb_expanded = user_embedding.unsqueeze(0).expand(group_embeddings.shape[0], -1)
        scores = self.forward(user_emb_expanded, group_embeddings)
        top_scores, top_indices = torch.topk(scores, min(top_k, len(scores)))
        
        recommendations = [
            (idx.item(), score.item()) 
            for idx, score in zip(top_indices, top_scores)
        ]
        
        return recommendations


def create_social_graph(users, friendships):
    num_users = len(users)
    feature_dim = 16
    x = torch.randn(num_users, feature_dim)
    
    edge_list = []
    for user1, user2 in friendships:
        edge_list.append([user1, user2])
        edge_list.append([user2, user1]) 
    if edge_list:
        edge_index = torch.tensor(edge_list, dtype=torch.long).t().contiguous()
    else:
        edge_index = torch.empty((2, 0), dtype=torch.long)
    data = Data(x=x, edge_index=edge_index)
    
    return data
