<!-- Comment Section for a Post -->
<div class="comments-section" id="comments-<?= $post['id'] ?>" style="display: none;">
    <!-- Comment Input -->
    <div class="comment-input-container">
        <img src="<?= $avatarUrl ?>" class="avatar-xs">
        <div class="comment-input-wrapper">
            <input type="text" 
                   id="comment-input-<?= $post['id'] ?>" 
                   placeholder="Write a comment..." 
                   onkeydown="if(event.key==='Enter') addComment(this, '<?= $post['id'] ?>')">
            <button onclick="addComment(document.getElementById('comment-input-<?= $post['id'] ?>'), '<?= $post['id'] ?>')" 
                    class="comment-send-btn">
                <i data-lucide="send"></i>
            </button>
        </div>
    </div>
    
    <!-- Comments List -->
    <div class="comments-list"></div>
</div>

<style>
.comments-section {
    padding: 12px 16px;
    border-top: 1px solid #e4e6eb;
}

.comment-input-container {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}

.comment-input-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    background: #f0f2f5;
    border-radius: 20px;
    padding: 8px 12px;
}

.comment-input-wrapper input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 15px;
}

.comment-send-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #1877f2;
    padding: 4px;
    display: flex;
    align-items: center;
}

.comment-send-btn:hover {
    opacity: 0.8;
}

.comment-send-btn i {
    width: 16px;
    height: 16px;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
</style>
