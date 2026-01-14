"""
GNN Recommender API Server
Serves friend recommendations using Graph Neural Network
"""

import os
import sys
from datetime import datetime
from flask import Flask, request, jsonify
from flask_cors import CORS
from dotenv import load_dotenv

# Load environment variables from parent directory
parent_dir = os.path.dirname(os.path.dirname(__file__))
load_dotenv(os.path.join(parent_dir, '.env'))

# Add current directory to path
sys.path.append(os.path.dirname(__file__))

from recommender import RecommendationEngine

# Initialize Flask app
app = Flask(__name__)
CORS(app)

# Global recommender instance
recommender = None
last_update_time = None


def initialize_recommender():
    """Load trained GNN model"""
    global recommender, last_update_time
    
    try:
        model_dir = os.path.join(os.path.dirname(__file__), 'models')
        device = 'cpu'  # Use CPU for production
        
        print(f"Initializing GNN Recommendation Engine...")
        print(f"Model directory: {model_dir}")
        
        recommender = RecommendationEngine(model_dir=model_dir, device=device)
        
        # Try to load models
        try:
            recommender.load_models()
            print("✓ GNN models loaded successfully")
        except FileNotFoundError as e:
            print(f"⚠️  {e}")
            print("Server running without pre-trained models.")
            print("You can update graph data via POST /api/update-graph")
            return False
        
        last_update_time = datetime.now()
        return True
        
    except Exception as e:
        print(f"ERROR: Failed to initialize recommender: {e}")
        import traceback
        traceback.print_exc()
        return False


# Health check
@app.route('/health', methods=['GET'])
def health_check():
    """Check if API is running"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'recommender_type': 'graph_neural_network',
        'initialized': recommender is not None,
        'models_loaded': recommender is not None and hasattr(recommender, 'gnn_model') and recommender.gnn_model is not None,
        'last_update': last_update_time.isoformat() if last_update_time else None
    })


# Friend recommendations
@app.route('/api/recommendations/friends/<int:user_id>', methods=['GET'])
def get_friend_recommendations(user_id):
    """Get friend recommendations for a user"""
    if recommender is None:
        return jsonify({
            'success': False,
            'error': 'Recommender not initialized'
        }), 503
    
    if not hasattr(recommender, 'graph_data') or recommender.graph_data is None:
        return jsonify({
            'success': False,
            'error': 'Graph data not loaded. Please update graph via POST /api/update-graph'
        }), 503
    
    try:
        top_k = request.args.get('top_k', 10, type=int)
        
        recommendations = recommender.recommend_friends(user_id, top_k=top_k)
        
        return jsonify({
            'success': True,
            'user_id': user_id,
            'recommendations': recommendations,
            'count': len(recommendations),
            'timestamp': datetime.now().isoformat()
        })
    
    except Exception as e:
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500


# Update graph data
@app.route('/api/update-graph', methods=['POST'])
def update_graph():
    """Update recommender with new data from database"""
    global last_update_time
    
    if recommender is None:
        return jsonify({
            'success': False,
            'error': 'Recommender not initialized'
        }), 503
    
    try:
        data = request.get_json()
        users = data.get('users', [])
        friendships = data.get('friendships', [])
        
        if not users:
            return jsonify({
                'success': False,
                'error': 'No users provided'
            }), 400
        
        # Update graph
        recommender.update_graph(users, friendships)
        last_update_time = datetime.now()
        
        return jsonify({
            'success': True,
            'message': 'Graph updated successfully',
            'num_users': len(users),
            'num_friendships': len(friendships),
            'timestamp': last_update_time.isoformat()
        })
    
    except Exception as e:
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500


# Get statistics
@app.route('/api/stats', methods=['GET'])
def get_stats():
    """Get recommender statistics"""
    if recommender is None:
        return jsonify({
            'success': False,
            'error': 'Recommender not initialized'
        }), 503
    
    try:
        stats = {
            'recommender_type': 'graph_neural_network',
            'models_loaded': hasattr(recommender, 'gnn_model') and recommender.gnn_model is not None,
            'graph_loaded': hasattr(recommender, 'graph_data') and recommender.graph_data is not None,
            'last_update': last_update_time.isoformat() if last_update_time else None
        }
        
        if hasattr(recommender, 'graph_data') and recommender.graph_data is not None:
            stats['num_users'] = recommender.graph_data.num_nodes
            stats['num_edges'] = recommender.graph_data.edge_index.shape[1]
        
        return jsonify({
            'success': True,
            'stats': stats,
            'timestamp': datetime.now().isoformat()
        })
    
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500


# Get user embedding
@app.route('/api/embeddings/<int:user_id>', methods=['GET'])
def get_user_embedding(user_id):
    """Get user embedding vector"""
    if recommender is None:
        return jsonify({
            'success': False,
            'error': 'Recommender not initialized'
        }), 503
    
    try:
        embedding = recommender.get_user_embedding(user_id)
        
        if embedding is None:
            return jsonify({
                'success': False,
                'error': f'User {user_id} not found'
            }), 404
        
        return jsonify({
            'success': True,
            'user_id': user_id,
            'embedding': embedding.tolist(),
            'embedding_dim': len(embedding),
            'timestamp': datetime.now().isoformat()
        })
    
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500


if __name__ == '__main__':
    print("=" * 70)
    print("GNN RECOMMENDER API SERVER")
    print("Graph Neural Network for Social Media Recommendations")
    print("=" * 70)
    
    print("\nInitializing recommender...")
    if initialize_recommender():
        print("✓ Initialization successful\n")
    else:
        print("⚠️  Models not loaded. Server will run in update-only mode.\n")
    
    port = int(os.getenv('API_PORT', 5000))
    host = os.getenv('API_HOST', '0.0.0.0')
    debug = os.getenv('API_DEBUG', 'true').lower() == 'true'
    
    print("=" * 70)
    print(f"Starting API server on http://localhost:{port}")
    print("=" * 70)
    print("\nAvailable endpoints:")
    print("  GET  /health")
    print("  GET  /api/recommendations/friends/<user_id>")
    print("  POST /api/update-graph")
    print("  GET  /api/stats")
    print("  GET  /api/embeddings/<user_id>")
    print("\nPress Ctrl+C to stop")
    print("=" * 70 + "\n")
    
    app.run(host=host, port=port, debug=debug)
