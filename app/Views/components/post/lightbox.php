<div id="media-lightbox" class="lightbox" style="display: none;" tabindex="0" onkeydown="handleLightboxKey(event)">
    <div class="lightbox-backdrop" onclick="closeLightbox()"></div>
    <button class="lightbox-close" onclick="closeLightbox()"><i data-lucide="x"></i></button>
    
    <div class="lightbox-content">
        <button class="lightbox-nav prev" id="lightbox-prev" onclick="navigateLightbox(-1)"><i data-lucide="chevron-left"></i></button>
        
        <div class="lightbox-media-wrapper">
            <div class="lightbox-media-container" id="lightbox-media-container">
                <!-- Media injected here -->
            </div>
        </div>
        
        <button class="lightbox-nav next" id="lightbox-next" onclick="navigateLightbox(1)"><i data-lucide="chevron-right"></i></button>
    </div>
</div>

<style>
.lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    outline: none;
}

.lightbox-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.9);
}

.lightbox-close {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 10001;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.lightbox-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 80px; /* Space for arrows */
    pointer-events: none; /* Let clicks pass through to backdrop where empty */
}

.lightbox-media-wrapper {
    flex: 1;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: auto;
}

.lightbox-media-container {
    max-width: 100%;
    max-height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-media-container img, 
.lightbox-media-container video {
    max-width: 100%;
    max-height: 90vh; /* Leave some margins */
    object-fit: contain;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
}

.lightbox-nav {
    pointer-events: auto;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, transform 0.1s;
}

.lightbox-nav:hover {
    background: rgba(255, 255, 255, 0.2);
}

.lightbox-nav:active {
    transform: scale(0.95);
}
</style>

<script>
let currentLightboxMedia = [];
let currentLightboxIndex = 0;

function openLightbox(postId, index = 0) {
    // Find post element
    // Note: We look for data-id on the post-card
    const postElement = document.querySelector(`.post-card[data-id="${postId}"]`);
    if (!postElement) return;

    // Collect all media urls from the grid in that post
    // Note: We assume the grid img/video sources are high enough quality for now
    const mediaElements = postElement.querySelectorAll('.media-item img, .media-item video');
    
    currentLightboxMedia = Array.from(mediaElements).map(el => ({
        url: el.src,
        type: el.tagName.toLowerCase()
    }));

    if (currentLightboxMedia.length === 0) return;

    currentLightboxIndex = index;
    showLightboxMedia();
    
    const lightbox = document.getElementById('media-lightbox');
    lightbox.style.display = 'flex';
    lightbox.focus(); // Focus for key events
    lucide.createIcons();
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function showLightboxMedia() {
    const container = document.getElementById('lightbox-media-container');
    const media = currentLightboxMedia[currentLightboxIndex];
    
    if (media.type === 'video') {
         container.innerHTML = `<video src="${media.url}" controls autoplay style="max-height: 90vh;"></video>`;
    } else {
         container.innerHTML = `<img src="${media.url}">`;
    }
    
    // Manage arrow visibility
    document.getElementById('lightbox-prev').style.visibility = currentLightboxIndex > 0 ? 'visible' : 'hidden';
    document.getElementById('lightbox-next').style.visibility = currentLightboxIndex < currentLightboxMedia.length - 1 ? 'visible' : 'hidden';
}

function navigateLightbox(direction) {
    const newIndex = currentLightboxIndex + direction;
    if (newIndex >= 0 && newIndex < currentLightboxMedia.length) {
        currentLightboxIndex = newIndex;
        showLightboxMedia();
    }
}

function handleLightboxKey(event) {
    if (event.key === 'Escape') closeLightbox();
    if (event.key === 'ArrowLeft') navigateLightbox(-1);
    if (event.key === 'ArrowRight') navigateLightbox(1);
}

function closeLightbox() {
    document.getElementById('media-lightbox').style.display = 'none';
    document.getElementById('lightbox-media-container').innerHTML = '';
    document.body.style.overflow = ''; // Restore scroll
}
</script>\r
