<!-- Single Comment Item Component -->
<!-- This is rendered dynamically by post.js renderComment() function -->
<!-- Kept as separate file for documentation and potential future server-side rendering -->

<div class="comment-wrapper" id="comment-{{id}}">
    <div class="comment" data-depth="{{depth}}">
        <a href="{{profileUrl}}">
            <img src="{{avatar}}" class="avatar-xs">
        </a>
        <div class="comment-content-wrapper">
            <div class="comment-bubble">
                <a href="{{profileUrl}}" class="comment-author">{{name}}</a>
                <p>{{content}}</p>
            </div>
            <div class="comment-actions">
                <span>{{timeText}}</span>
                <span class="comment-action-btn" data-action="like">Like</span>
                <span class="comment-action-btn" data-action="reply">Reply</span>
                <span class="comment-action-btn delete" data-action="delete">Delete</span>
            </div>
            <div class="reply-box" id="reply-box-{{id}}" style="display: none;">
                <div class="comment-input-wrapper">
                    <input type="text" 
                           id="reply-input-{{id}}" 
                           placeholder="Write a reply...">
                    <button class="comment-send-btn">
                        <i data-lucide="send"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="replies-container"></div>
</div>

<style>
/* Comment Styles */
.comment-wrapper {
    margin-bottom: 8px;
}

.comment {
    display: flex;
    gap: 8px;
    padding: 4px 0;
}

.comment .avatar-xs {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-content-wrapper {
    flex: 1;
}

.comment-bubble {
    background: #f0f2f5;
    border-radius: 18px;
    padding: 8px 12px;
    display: inline-block;
    max-width: 100%;
}

.comment-author {
    font-weight: 600;
    font-size: 13px;
    color: #050505;
    text-decoration: none;
}

.comment-author:hover {
    text-decoration: underline;
}

.comment-bubble p {
    margin: 4px 0 0 0;
    font-size: 15px;
    color: #050505;
    word-break: break-word;
}

.comment-actions {
    display: flex;
    gap: 12px;
    padding: 4px 12px;
    font-size: 12px;
    color: #65676b;
}

.comment-action-btn {
    cursor: pointer;
    font-weight: 600;
}

.comment-action-btn:hover {
    text-decoration: underline;
}

.comment-action-btn.delete {
    color: #e41e3f;
}

/* Nested replies indentation */
.comment[data-depth="1"] {
    margin-left: 40px;
}

.comment[data-depth="2"] {
    margin-left: 40px;
}

/* Reply box */
.reply-box {
    margin: 8px 0 8px 40px;
}

.reply-box .comment-input-wrapper {
    background: #f0f2f5;
    border-radius: 20px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.reply-box input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 14px;
}

.replies-container {
    margin-top: 4px;
}
</style>
