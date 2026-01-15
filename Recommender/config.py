# GNN Recommender System Configuration

# Model Configuration
MODEL_CONFIG = {
    'gnn': {
        'hidden_dim': 64,
        'embedding_dim': 32,
        'num_heads': 4,  # For Graph Attention Network
        'dropout': 0.6
    },
    'post_recommender': {
        'user_embedding_dim': 32,
        'post_embedding_dim': 32,
        'hidden_dim': 64
    },
    'group_recommender': {
        'user_embedding_dim': 32,
        'group_embedding_dim': 32,
        'hidden_dim': 64
    }
}

# Training Configuration
TRAINING_CONFIG = {
    'epochs': 100,
    'learning_rate': 0.01,
    'weight_decay': 5e-4,
    'batch_size': 32,
    'validation_split': 0.2,
    'early_stopping_patience': 20
}

# Data Configuration
DATA_CONFIG = {
    'user_feature_dim': 16,
    'post_feature_dim': 16,
    'group_feature_dim': 16,
    'min_friends': 0,
    'max_friends': 5000
}

# Recommendation Configuration
RECOMMENDATION_CONFIG = {
    'friend_recommendations': {
        'top_k': 10,
        'min_similarity_score': 0.3,
        'exclude_existing_friends': True,
        'exclude_pending_requests': True
    },
    'post_recommendations': {
        'top_k': 20,
        'min_score': 0.5,
        'diversity_weight': 0.3,  # Balance between relevance and diversity
        'recency_weight': 0.2  # Boost recent posts
    },
    'group_recommendations': {
        'top_k': 10,
        'min_score': 0.4,
        'exclude_joined_groups': True
    }
}

# Paths
PATHS = {
    'models_dir': 'models',
    'data_dir': 'data',
    'checkpoints_dir': 'checkpoints',
    'logs_dir': 'logs'
}

# Database Configuration (adapt to your database)
DATABASE_CONFIG = {
    'user_table': 'users',
    'friendship_table': 'friendships',
    'post_table': 'posts',
    'group_table': 'groups',
    'interaction_table': 'user_post_interactions'
}

# Feature Engineering
FEATURE_CONFIG = {
    'user_features': [
        'age',
        'account_age_days',
        'post_count',
        'friend_count',
        'comment_count',
        'like_count',
        'is_verified',
        'has_profile_pic',
        'gender',
        'interests'
    ],
    'post_features': [
        'like_count',
        'comment_count',
        'share_count',
        'age_hours',
        'content_type',
        'has_media',
        'content_length',
        'topics'
    ]
}

# Update frequency (in minutes)
UPDATE_CONFIG = {
    'graph_update_frequency': 60,  # Update social graph every hour
    'embedding_cache_ttl': 30,  # Cache user embeddings for 30 minutes
    'model_retrain_frequency': 1440  # Retrain model daily (1440 minutes)
}
