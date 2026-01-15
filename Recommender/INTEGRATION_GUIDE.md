# Connecting Recommender to Database & Web Server

Complete guide for integrating the GNN Recommender with your MySQL database and PHP web application.

## ✅ What's Been Created

### 1. Database Integration Files

- **`db_loader.py`**: Loads users and friendships from MySQL database
- **`train_db.py`**: Trains GNN model using your actual database data

### 2. Web Integration Files

- **`app/Libraries/RecommenderAPI.php`**: PHP library to call the Recommender API
- **`php_integration_example.php`**: Example code showing how to use the API in controllers

---

## 🚀 Step-by-Step Integration

### Step 1: Install Dependencies

```bash
cd c:\Users\Lenovo\Desktop\Social_Media\Recommender
pip install python-dotenv pymysql
```

### Step 2: Test Database Connection

```bash
python db_loader.py
```

This will connect to your MySQL database using credentials from `.env` and show how many users/friendships are loaded.

### Step 3: Train Model with Your Data

```bash
python train_db.py
```

This trains the GNN model on YOUR actual users and friendships from the database.

### Step 4: Start API Server

```bash
python server.py
```

The recommender API will run on `http://localhost:5000`

### Step 5: Test API

```bash
curl http://localhost:5000/health
curl "http://localhost:5000/api/recommendations/friends/1?top_k=5"
```

---

## 📝 How to Use in Your Web Application

### In Your PHP Controller

```php
use App\Libraries\RecommenderAPI;

public function showDashboard()
{
    $userId = session()->get('user_id');
    
    // Get AI recommendations
    $recommenderAPI = new RecommenderAPI();
    $suggestions = $recommenderAPI->getFriendRecommendations($userId, 5);
    
    // Pass to view
    return view('dashboard', [
        'friend_suggestions' => $suggestions
    ]);
}
```

### In Your Sidebar View (sidebar_right.php)

```php
<?php if (!empty($friend_suggestions)): ?>
    <div class="suggestions-section">
        <h3>People You May Know</h3>
        <?php foreach ($friend_suggestions as $suggestion): ?>
            <div class="suggestion-item">
                <img src="<?= $suggestion['avatar'] ?>" alt="<?= $suggestion['username'] ?>">
                <span><?= $suggestion['username'] ?></span>
                <span class="ai-score">AI Score: <?= round($suggestion['score'] * 100) ?>%</span>
                <button class="add-friend" data-user-id="<?= $suggestion['user_id'] ?>">
                    Add Friend
                </button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

---

## 🔄 Updating Recommendations

The model should be retrained periodically with new data:

### Option 1: Manual Retrain (Recommended Weekly)

```bash
python train_db.py
# Restart server
python server.py
```

### Option 2: Auto-Update via API (After Friendships Change)

In your `FriendShipController.php` after accepting friendships:

```php
public function acceptRequest($requestId)
{
    // ... your existing code to accept friendship ...
    
    // Update recommender
    $recommenderAPI = new RecommenderAPI();
    if ($recommenderAPI->healthCheck()) {
        // Trigger background update (optional)
        $this->updateRecommenderGraph();
    }
}
```

---

## 📊 API Endpoints Reference

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/health` | GET | Check if API is running |
| `/api/recommendations/friends/<user_id>` | GET | Get friend recommendations |
| `/api/stats` | GET | Get system statistics |
| `/api/update-graph` | POST | Update with new data |
| `/api/embeddings/<user_id>` | GET | Get user embedding vector |

---

##Next Steps

1. **Install dependencies**: `pip install python-dotenv pymysql`
2. **Test database connection**: `python db_loader.py`
3. **Train with your data**: `python train_db.py`
4. **Start API server**: `python server.py`
5. **Integrate in PHP**: Use `RecommenderAPI` class in your controllers
6. **Display in UI**: Show recommendations in sidebar/dashboard

---

## 🛠️ Troubleshooting

**API not responding?**
- Check if server is running: `python server.py`
- Check port 5000 is not blocked

**No recommendations?**
- Ensure model is trained: `python train_db.py`
- Check graph is updated: `curl http://localhost:5000/api/stats`

**Database connection error?**
- Verify `.env` credentials are correct
- Test with: `python db_loader.py`
