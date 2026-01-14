"""
GNN Training Utilities
Contains trainer classes for GNN models
"""

import torch
import torch.nn.functional as F
from torch_geometric.data import Data
import numpy as np
from typing import Tuple, Optional
import os
from datetime import datetime


class GNNTrainer:
    """
    Trainer for Social GNN models
    """
    
    def __init__(self, model, device='cpu', learning_rate=0.01):
        self.model = model.to(device)
        self.device = device
        self.optimizer = torch.optim.Adam(model.parameters(), lr=learning_rate, weight_decay=5e-4)
        self.history = {
            'train_loss': [],
            'val_loss': [],
            'epoch': []
        }
        
    def create_link_prediction_labels(self, edge_index, num_nodes):
        num_edges = edge_index.shape[1]
        num_samples = num_edges // 2
        
        perm = torch.randperm(num_edges // 2)[:num_samples]
        pos_edge_index = edge_index[:, perm * 2]
        
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
        pos_src = embeddings[pos_edge_index[0]]
        pos_dst = embeddings[pos_edge_index[1]]
        pos_scores = (pos_src * pos_dst).sum(dim=-1)
        pos_loss = -F.logsigmoid(pos_scores).mean()
        
        neg_src = embeddings[neg_edge_index[0]]
        neg_dst = embeddings[neg_edge_index[1]]
        neg_scores = (neg_src * neg_dst).sum(dim=-1)
        neg_loss = -F.logsigmoid(-neg_scores).mean()
        
        return pos_loss + neg_loss
    
    def train_epoch(self, data, train_mask=None):
        self.model.train()
        self.optimizer.zero_grad()
        
        embeddings = self.model(data.x.to(self.device), data.edge_index.to(self.device))
        
        pos_edge_index, neg_edge_index = self.create_link_prediction_labels(
            data.edge_index, data.num_nodes
        )
        
        loss = self.link_prediction_loss(
            embeddings,
            pos_edge_index.to(self.device),
            neg_edge_index.to(self.device)
        )
        
        loss.backward()
        self.optimizer.step()
        
        return loss.item()
    
    def evaluate(self, data, val_edge_index=None):
        self.model.eval()
        
        with torch.no_grad():
            embeddings = self.model(data.x.to(self.device), data.edge_index.to(self.device))
            
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
        best_val_loss = float('inf')
        
        for epoch in range(epochs):
            train_loss = self.train_epoch(data)
            
            if val_data is not None:
                val_loss = self.evaluate(val_data)
            else:
                val_loss = self.evaluate(data)
            
            self.history['train_loss'].append(train_loss)
            self.history['val_loss'].append(val_loss)
            self.history['epoch'].append(epoch)
            
            if val_loss < best_val_loss and save_path is not None:
                best_val_loss = val_loss
                self.save_model(save_path)
            
            if verbose and (epoch + 1) % 10 == 0:
                print(f'Epoch {epoch+1}/{epochs} | '
                      f'Train Loss: {train_loss:.4f} | '
                      f'Val Loss: {val_loss:.4f}')
        
        return self.history
    
    def save_model(self, path):
        os.makedirs(os.path.dirname(path) if os.path.dirname(path) else '.', exist_ok=True)
        
        checkpoint = {
            'model_state_dict': self.model.state_dict(),
            'optimizer_state_dict': self.optimizer.state_dict(),
            'history': self.history,
            'timestamp': datetime.now().isoformat()
        }
        
        torch.save(checkpoint, path)
        
    def load_model(self, path):
        checkpoint = torch.load(path, map_location=self.device)
        self.model.load_state_dict(checkpoint['model_state_dict'])
        self.optimizer.load_state_dict(checkpoint['optimizer_state_dict'])
        self.history = checkpoint.get('history', self.history)
