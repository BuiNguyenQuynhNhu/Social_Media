<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Recommendations</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/recommendations.css">
</head>
<body>
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>
    
    <div class="container">
        <div class="recommendations-overview">
            <div class="page-header">
                <h1>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    AI-Powered Recommendations
                </h1>
                <p class="subtitle">Personalized suggestions based on your network and interests</p>
            </div>

            <div class="recommendation-categories">
                <!-- Friend Recommendations -->
                <a href="/recommendations/friends" class="category-card">
                    <div class="card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2>Friend Suggestions</h2>
                    <p>Discover people you may know based on mutual connections and interests</p>
                    <div class="card-action">
                        <span>View Suggestions</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>

                <!-- Post Recommendations -->
                <a href="/recommendations/posts" class="category-card">
                    <div class="card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2>Post Recommendations</h2>
                    <p>See posts tailored to your interests and engagement patterns</p>
                    <div class="card-action">
                        <span>View Posts</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>

                <!-- Group Recommendations -->
                <a href="/recommendations/groups" class="category-card">
                    <div class="card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2>Group Suggestions</h2>
                    <p>Find groups that match your interests and activities</p>
                    <div class="card-action">
                        <span>Explore Groups</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- About AI Recommendations -->
            <div class="about-section">
                <h3>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    About AI Recommendations
                </h3>
                <p>Our recommendation system uses advanced Graph Neural Networks (GNN) to analyze your social connections, interests, and behavior patterns. This allows us to provide highly personalized suggestions that help you discover new friends, content, and communities.</p>
                
                <div class="features">
                    <div class="feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Privacy-focused</span>
                    </div>
                    <div class="feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Real-time updates</span>
                    </div>
                    <div class="feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Personalized for you</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
