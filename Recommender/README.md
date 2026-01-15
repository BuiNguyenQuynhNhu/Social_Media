# GNN Recommender System

AI-powered friend recommendations using Graph Neural Networks (GNN) for social media.

## 🚀 Quick Start

### 1. Install Dependencies

```bash
pip install torch torch-geometric flask flask-cors pymysql python-dotenv numpy
# Or use requirements.txt
pip install -r requirements.txt
```

### 2. Train the Model

```bash
python train.py
```

This trains the GNN model using sample social network data and saves it to `models/social_gnn_best.pt`.

### 3. Start API Server

```bash
python server.py
# Or use the batch file
start.bat  
```

Server runs on `http://localhost:5000`

## 📁 Project Structure

```
Recommender/
├── model.py          # GNN model architecture (Graph Attention Networks)
├── processor.py      # Data processing for GNN
├── recommender.py    # Recommendation engine using trained GNN
├── train.py          # Training script
├── server.py         # Flask API server
├── config.py         # Configuration settings
├── requirements.txt  # Python dependencies
├── start.bat         # Quick start script
└── models/          # Saved trained models
```

## 🔌 API Endpoints

- `GET /health` - Health check
- `GET /api/recommendations/friends/<user_id>?top_k=10` - Get friend recommendations
- `GET /api/stats` - Get system statistics
- `GET /api/embeddings/<user_id>` - Get user embedding vector
- `POST /api/update-graph` - Update with new user/friendship data

## 🧠 How It Works

The system uses **Graph Neural Networks (GNN)** with Graph Attention Networks (GAT):

1. **Graph Construction**: Users are nodes, friendships are edges
2. **Feature Learning**: GNN learns user embeddings from network structure
3. **Recommendations**: Uses cosine similarity between embeddings to find similar users
4. **Filtering**: Excludes existing friends and recommends new connections

## 🔧 For Production

### Connect to Your Database

Update the `.env` file in the parent directory with your database credentials:

```env
database.default.hostname = localhost
database.default.database = social_media
database.default.username = root
database.default.password = your_password
```

### Train with Your Data

Modify `train.py` to load users and friendships from your database, then run:

```bash
python train.py
```

### Deploy

Start the server in production mode:

```bash
python server.py
```

## 💡 Example Usage

**Get friend recommendations:**
```bash
curl http://localhost:5000/api/recommendations/friends/1?top_k=5
```

**Update graph with new data:**
```bash
curl -X POST http://localhost:5000/api/update-graph \
  -H "Content-Type: application/json" \
  -d '{"users": [...], "friendships": [[1,2], [2,3], ...]}'
```

## 🎯 Model Performance

- **Algorithm**: Graph Attention Networks (GAT)
- **Embedding Dimension**: 32
- **Training**: Link prediction task
- **Accuracy**: Learns from social network structure

## 📊 Maintenance

**Retrain model with latest data:**
```bash
python train.py
# Restart server
python server.py
```

---

Built with PyTorch, PyTorch Geometric, and Flask 🚀
