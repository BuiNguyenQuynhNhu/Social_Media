# Quick Fix: Model Trained with Sample Data

The model is currently trained with sample data (users 0-99). Your database has different user IDs (UUIDs).

## Solution 1: Return Sample Recommendations (Quick Test)

The system will now return fallback recommendations when it can't find the user.

## Solution 2: Train with Your Database (Recommended)

Once you have more friendships in your database, train with real data:

```bash
python train_db.py
```

This will create a mapping between your database UUIDs and the model.

## Solution 3: Manual Test

Test the API with sample user IDs (0-99):

```bash
curl "http://localhost:5000/api/recommendations/friends/0?top_k=5"
```

---

**Current Status**: Server updated to handle UUID mismatch gracefully. Will return fallback recommendations for users not in the trained model.
