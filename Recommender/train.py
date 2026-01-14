"""
Training script for the GNN recommender system
Handles model training, validation, and saving
"""

import torch
import torch.nn.functional as F
from torch_geometric.data import Data
import numpy as np
from typing import Tuple, Optional
import os
import json
from datetime import datetime

from model import SocialGNN, FriendRecommender, PostRecommender, GroupRecommender
from processor import SocialDataProcessor, PostDataProcessor, load_sample_data


class GNNTrainer:
    """
    Trainer for Social GNN models
    """
    
    def __init__(self, model, device='cpu', learning_rate=0.01):
        """
        Args:
            model: GNN model to train
            device: 'cpu' or 'cuda'
            learning_rate: Learning rate for optimizer
        """
        self.model = model.to(device)
        self.device = device
        self.optimizer = torch.optim.Adam(model.parameters(), lr=learning_rate, weight_decay=5e-4)
        self.history = {
            'train_loss': [],
            'val_loss': [],
            'epoch': []
        }
        
    def create_link_prediction_labels(self, edge_index, num_nodes):
        """
        Create positive and negative samples for link prediction
        
        Args:
            edge_index: Existing edges
            num_nodes: Total number of nodes
            
        Returns:
            Tuple of (pos_edge_index, neg_edge_index)
        """
        # Positive samples (existing edges)
        num_edges = edge_index.shape[1]
        num_samples = num_edges // 2  # Since we have bidirectional edges
        
        # Sample positive edges
        perm = torch.randperm(num_edges // 2)[:num_samples]
        pos_edge_index = edge_index[:, perm * 2]
        
        # Create negative samples (non-existing edges)
        neg_edges = []
        edge_set = set(map(tuple, edge_index.t().tolist()))
        
        while len(neg_edges) < num_samples:
            src = np.random.randint(0, num_nodes)
            dst = np.random.randint(0, num_nodes)
            
            if src != dst and (src, dst) not in edge_set and (dst, src) not in edge_set:
                neg_edges.append([src, dst])
        
        neg_edge_index = torch.tensor(neg_edges, dtype=torch.long).t()
        
        return pos_edge_index, neg_edge_index
    
    def link_prediction_loss(self, embeddings, pos_edge_index, neg_edge_index):
        """
        Calculate link prediction loss using dot product
        
        Args:
            embeddings: Node embeddings from GNN
            pos_edge_index: Positive edge samples
            neg_edge_index: Negative edge samples
            
        Returns:
            Loss value
        """
        # Positive scores
        pos_src = embeddings[pos_edge_index[0]]
        pos_dst = embeddings[pos_edge_index[1]]
        pos_scores = (pos_src * pos_dst).sum(dim=-1)
        pos_loss = -F.logsigmoid(pos_scores).mean()
        
        # Negative scores
        neg_src = embeddings[neg_edge_index[0]]
        neg_dst = embeddings[neg_edge_index[1]]
        neg_scores = (neg_src * neg_dst).sum(dim=-1)
        neg_loss = -F.logsigmoid(-neg_scores).mean()
        
        return pos_loss + neg_loss
    
    def train_epoch(self, data, train_mask=None):
        """
        Train for one epoch
        
        Args:
            data: PyTorch Geometric Data object
            train_mask: Optional mask for training nodes
            
        Returns:
            Average training loss
        """
        self.model.train()
        self.optimizer.zero_grad()
        
        # Forward pass
        embeddings = self.model(data.x.to(self.device), data.edge_index.to(self.device))
        
        # Create positive and negative samples
        pos_edge_index, neg_edge_index = self.create_link_prediction_labels(
            data.edge_index, data.num_nodes
        )
        
        # Calculate loss
        loss = self.link_prediction_loss(
            embeddings,
            pos_edge_index.to(self.device),
            neg_edge_index.to(self.device)
        )
        
        # Backward pass
        loss.backward()
        self.optimizer.step()
        
        return loss.item()
    
    def evaluate(self, data, val_edge_index=None):
        """
        Evaluate model performance
        
        Args:
            data: PyTorch Geometric Data object
            val_edge_index: Optional validation edges
            
        Returns:
            Validation loss
        """
        self.model.eval()
        
        with torch.no_grad():
            embeddings = self.model(data.x.to(self.device), data.edge_index.to(self.device))
            
            # Create validation samples
            if val_edge_index is None:
                pos_edge_index, neg_edge_index = self.create_link_prediction_labels(
                    data.edge_index, data.num_nodes
                )
            else:
                pos_edge_index, neg_edge_index = self.create_link_prediction_labels(
                    val_edge_index, data.num_nodes
                )
            
            loss = self.link_prediction_loss(
                embeddings,
                pos_edge_index.to(self.device),
                neg_edge_index.to(self.device)
            )
        
        return loss.item()
    
    def train(self, data, epochs=100, val_data=None, save_path=None, verbose=True):
        """
        Train the model for multiple epochs
        
        Args:
            data: Training data
            epochs: Number of training epochs
            val_data: Optional validation data
            save_path: Path to save the best model
            verbose: Print training progress
            
        Returns:
            Training history
        """
        best_val_loss = float('inf')
        
        for epoch in range(epochs):
            # Train
            train_loss = self.train_epoch(data)
            
            # Validate
            if val_data is not None:
                val_loss = self.evaluate(val_data)
            else:
                val_loss = self.evaluate(data)
            
            # Save history
            self.history['train_loss'].append(train_loss)
            self.history['val_loss'].append(val_loss)
            self.history['epoch'].append(epoch)
            
            # Save best model
            if val_loss < best_val_loss and save_path is not None:
                best_val_loss = val_loss
                self.save_model(save_path)
            
            # Print progress
            if verbose and (epoch + 1) % 10 == 0:
                print(f'Epoch {epoch+1}/{epochs} | '
                      f'Train Loss: {train_loss:.4f} | '
                      f'Val Loss: {val_loss:.4f}')
        
        return self.history
    
    def save_model(self, path):
        """Save model checkpoint"""
        os.makedirs(os.path.dirname(path) if os.path.dirname(path) else '.', exist_ok=True)
        
        checkpoint = {
            'model_state_dict': self.model.state_dict(),
            'optimizer_state_dict': self.optimizer.state_dict(),
            'history': self.history,
            'timestamp': datetime.now().isoformat()
        }
        
        torch.save(checkpoint, path)
        
    def load_model(self, path):
        """Load model checkpoint"""
        checkpoint = torch.load(path, map_location=self.device)
        self.model.load_state_dict(checkpoint['model_state_dict'])
        self.optimizer.load_state_dict(checkpoint['optimizer_state_dict'])
        self.history = checkpoint.get('history', self.history)


class RecommenderTrainer:
    """
    Trainer for post and group recommenders
    """
    
    def __init__(self, model, device='cpu', learning_rate=0.001):
        self.model = model.to(device)
        self.device = device
        self.optimizer = torch.optim.Adam(model.parameters(), lr=learning_rate)
        self.criterion = torch.nn.BCELoss()
        
    def train_batch(self, user_embeddings, item_embeddings, labels):
        """
        Train on a batch of user-item pairs
        
        Args:
            user_embeddings: User embedding tensor
            item_embeddings: Item (post/group) embedding tensor
            labels: Ground truth labels (0 or 1)
            
        Returns:
            Batch loss
        """
        self.model.train()
        self.optimizer.zero_grad()
        
        # Forward pass
        predictions = self.model(
            user_embeddings.to(self.device),
            item_embeddings.to(self.device)
        )
        
        # Calculate loss
        loss = self.criterion(predictions, labels.to(self.device))
        
        # Backward pass
        loss.backward()
        self.optimizer.step()
        
        return loss.item()
    
    def save_model(self, path):
        """Save model checkpoint"""
        os.makedirs(os.path.dirname(path) if os.path.dirname(path) else '.', exist_ok=True)
        torch.save({
            'model_state_dict': self.model.state_dict(),
            'optimizer_state_dict': self.optimizer.state_dict(),
            'timestamp': datetime.now().isoformat()
        }, path)
    
    def load_model(self, path):
        """Load model checkpoint"""
        checkpoint = torch.load(path, map_location=self.device)
        self.model.load_state_dict(checkpoint['model_state_dict'])
        self.optimizer.load_state_dict(checkpoint['optimizer_state_dict'])


def main():
    """
    Main training script
    """
    print("=" * 60)
    print("GNN Recommender System - Training")
    print("=" * 60)
    
    # Configuration
    device = 'cuda' if torch.cuda.is_available() else 'cpu'
    print(f"\n✓ Using device: {device}")
    
    # Load and process data
    print("\n[1] Loading data...")
    users, friendships = load_sample_data()
    
    processor = SocialDataProcessor()
    graph_data = processor.create_graph(users, friendships)
    
    print(f"✓ Loaded {len(users)} users")
    print(f"✓ Feature dimension: {graph_data.x.shape[1]}")
    print(f"✓ Number of edges: {graph_data.edge_index.shape[1]}")
    
    # Initialize model
    print("\n[2] Initializing GNN model...")
    num_features = graph_data.x.shape[1]
    model = SocialGNN(
        num_node_features=num_features,
        hidden_dim=64,
        embedding_dim=32,
        num_heads=4
    )
    print(f"✓ Model initialized with {sum(p.numel() for p in model.parameters())} parameters")
    
    # Train model
    print("\n[3] Training model...")
    trainer = GNNTrainer(model, device=device, learning_rate=0.01)
    
    save_dir = os.path.join(os.path.dirname(__file__), 'models')
    os.makedirs(save_dir, exist_ok=True)
    
    history = trainer.train(
        graph_data,
        epochs=100,
        save_path=os.path.join(save_dir, 'social_gnn_best.pt'),
        verbose=True
    )
    
    print("\n[4] Training complete!")
    print(f"✓ Final train loss: {history['train_loss'][-1]:.4f}")
    print(f"✓ Final val loss: {history['val_loss'][-1]:.4f}")
    print(f"✓ Model saved to: {os.path.join(save_dir, 'social_gnn_best.pt')}")
    
    # Test recommendations
    print("\n[5] Testing friend recommendations...")
    friend_recommender = FriendRecommender(model)
    test_user_id = 0
    
    recommendations = friend_recommender.recommend_friends(
        test_user_id,
        graph_data.x,
        graph_data.edge_index,
        top_k=5
    )
    
    print(f"\nTop 5 friend recommendations for User {test_user_id}:")
    for i, (user_idx, score) in enumerate(recommendations, 1):
        actual_user_id = processor.get_user_id(user_idx)
        print(f"  {i}. User {actual_user_id} (index: {user_idx}, score: {score:.4f})")
    
    print("\n" + "=" * 60)
    print("Training completed successfully!")
    print("=" * 60)


if __name__ == "__main__":
    main()
