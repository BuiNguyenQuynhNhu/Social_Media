<div class="friends-combined-container">
    
    <!-- Friend Requests Section -->
    <div id="requests" class="friends-scroll-section">
        <?= view('friends/requests', ['requests' => $requests]) ?>
    </div>

    <!-- Suggestions Section -->
    <div id="suggestions" class="friends-scroll-section">
        <?= view('friends/suggestions', ['suggestions' => $suggestions]) ?>
    </div>

    <!-- All Friends Section -->
    <div id="list" class="friends-scroll-section">
        <?= view('friends/list', ['friends' => $friends]) ?>
    </div>

</div>

<style>
    .friends-scroll-section {
        margin-bottom: 40px;
        scroll-margin-top: 100px; /* Offset for fixed navbar + header */
        min-height: 200px;
    }
</style>
