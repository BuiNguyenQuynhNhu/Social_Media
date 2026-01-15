# Quick Start Guide

## For Testing/Demo (Use Sample Data)

If your database doesn't have enough friendships yet or you want to test quickly:

```bash
python train.py
python server.py
```

This uses pre-built sample social network data with 100 users and realistic friendship patterns.

## For Production (Use Your Database)

When you have real users and friendships in your database:

```bash
python train_db.py
python server.py
```

This loads your actual users and friendships from MySQL.

---

## API Endpoints

- `GET /health` - Check server status
- `GET /api/recommendations/friends/<user_id>?top_k=5` - Get recommendations
- `GET /api/stats` - Get system info

## Web Integration

See `app/Libraries/RecommenderAPI.php` for PHP integration code.
