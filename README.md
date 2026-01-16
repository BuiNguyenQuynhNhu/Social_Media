# Social Media Platform

A full-featured social media application built with **CodeIgniter 4** (PHP), **MySQL**, **MongoDB**, and a **Graph Neural Network (GNN)** recommendation engine powered by **Python (PyTorch + Flask)**.

## 🚀 Key Features

### 👤 User Features
- **News Feed**: Infinite scroll feed with posts from friends and joined groups.
- **Rich Posts**: Support for text, images, and videos.
- **Groups**: Create, join, and manage communities with role-based access.
- **Real-time Chat**: Private messaging and group chats using MongoDB.
- **Notifications**: Real-time alerts for interactions, invites, and system updates.
- **Friends System**: Graph-based friend requests and suggestions.
- **User Profiles**: Custom avatars, cover photos, and personal timelines.

### 🛡️ Admin Panel (Facebook-Style)
- **Dashboard**: Real-time statistics and system status.
- **User Management**: Search, warn, ban, and unban users.
- **Content Moderation**: Review and delete posts/groups violating guidelines.
- **Ban Appeals**: Dedicated workflow for users to appeal bans, with accept/reject actions.
- **Role-Based Access**: Distinguish between regular Users and Admins.

### 🧠 AI Recommendation Engine
- **GNN-Powered**: Uses Graph Neural Networks (PyTorch Geometric) to suggest friends.
- **Graph Processing**: Analyzes user interactions and friendships to find hidden connections.
- **Hybrid Storage**: Uses MySQL for relational data and MongoDB for chat logs.

---

## 🛠️ Tech Stack

- **Backend**: CodeIgniter 4 (PHP 8.1+)
- **Database**: 
  - **MySQL 8.0**: Users, Posts, Groups, Relationships.
  - **MongoDB**: High-volume Chat messages and Logs.
- **AI/ML Service**: 
  - **Python 3.9**: Flask API, PyTorch, PyTorch Geometric.
- **Frontend**: HTML5, CSS3, Vanilla JS, Lucide Icons.
- **Containerization**: Docker & Docker Compose.

---

## ⚙️ Getting Started (Docker)

The easiest way to run the project is using Docker. This ensures all dependencies (PHP extensions, Python libraries, Databases) are correctly set up.

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.

### Installation
1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd Social_Media
   ```

2. **Configure Environment**:
   - The `.env` file is pre-configured for Docker.
   - Ensure `database.default.hostname` is set to `db`.
   - Ensure `MONGO_HOST` is set to `mongo`.

3. **Run with Docker Compose**:
   ```bash
   docker-compose up -d --build
   ```

4. **Access the Application**:
   - **Main App**: [http://localhost:8080](http://localhost:8080)
   - **PhpMyAdmin**: [http://localhost:8081](http://localhost:8081) (Configured for MySQL management)
   - **Recommender API**: [http://localhost:5000/health](http://localhost:5000/health)

---

## 🔧 Manual Setup (Local Development)

If you prefer to run services manually (e.g., XAMPP/WAMP), ensure you have:
1. **PHP 8.1+** with `intl`, `mbstring`, `mysqli`, `mongodb` extensions enabled.
2. **Composer** installed.
3. **MySQL** running on port 3306.
4. **MongoDB** running on port 27017.
5. **Python 3.9+** with dependencies: `pip install -r Recommender/requirements.txt`.

### Steps:
1. `composer install`
2. `php spark migrate` (Run database migrations)
3. `php spark db:seed MainSeeder` (Seed initial data)
4. `python Recommender/server.py` (Start AI Service)
5. `php spark serve` (Start PHP Built-in Server)

---

## 📂 Project Structure

```
/app                 # CodeIgniter Application Code
  /Controllers       # Request Handlers (Admin, User, Auth, etc.)
  /Models            # Database Interaction (MySQL & MongoDB)
  /Views             # HTML Templates
  /Libraries         # Custom Libraries (MongoDB Wrapper, etc.)
/public
  /CSS               # Stylesheets (admin.css, dashboard.css)
  /JS                # JavaScript Logic (notifications.js, post.js)
  /ICON              # Assets & Icons
/Recommender         # Python GNN Service
  /models            # Saved PyTorch Models
  server.py          # Flask API Entry Point
  recommender.py     # GNN Logic
/writable            # Logs, Cache, Uploads
Dockerfile           # Apache/PHP Image Config
docker-compose.yml   # Multi-container orchestration
```

## 🔐 Credentials (Default)

| Service | Username | Password |
|---------|----------|----------|
| **MySQL** | `user` | `userpassword` |
| **MySQL (Root)** | `root` | `rootpassword` |
| **MongoDB** | `root` | `3141592653Az` |
| **Admin User** | *(See database or register first user)* | *(Set during registration)* |

> **Note**: To make a user an Admin, set their `type` to `1` in the `users` table.

## 🤝 Contributing
1. Fork the repository.
2. Create your feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---
© 2026 Social Media Project. All Rights Reserved.