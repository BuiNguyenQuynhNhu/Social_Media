const toastRegion = document.querySelector("[data-toast-region]");
let postCounter = 0;

function showToast(message) {
  if (!toastRegion) return;

  toastRegion.textContent = message;
  toastRegion.classList.remove("hidden");
  window.clearTimeout(showToast.timeout);
  showToast.timeout = window.setTimeout(() => {
    toastRegion.classList.add("hidden");
  }, 2600);
}

function makeComment(text) {
  const comment = document.createElement("p");
  comment.className = "feed-comment";
  comment.textContent = text;
  return comment;
}

function createPost(content) {
  const commentInputId = `new-comment-${++postCounter}`;
  const post = document.createElement("article");
  post.className = "feed-card";
  post.dataset.postCard = "";
  post.innerHTML = `
    <div class="feed-post__header">
      <div class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700" aria-hidden="true">Y</div>
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-[#050505]">You</p>
        <p class="text-xs text-[#65676b]">Just now · 🌐</p>
      </div>
      <button type="button" class="grid size-9 place-items-center rounded-full text-xl text-[#65676b] hover:bg-[#f0f2f5]" aria-label="Post options">···</button>
    </div>
    <p class="feed-post__body whitespace-pre-wrap" data-post-content></p>
    <div class="feed-post__stats">
      <span><span aria-hidden="true">👍</span> <span data-like-count>0</span></span>
      <button type="button" class="hover:underline" data-action="comments" data-comment-count="0">0 comments</button>
    </div>
    <div class="feed-post__actions">
      <button type="button" class="feed-post__action" data-action="like" aria-pressed="false">👍 <span>Like</span></button>
      <button type="button" class="feed-post__action" data-action="comments">💬 <span>Comment</span></button>
      <button type="button" class="feed-post__action" data-action="share">↗ <span>Share</span></button>
    </div>
    <div class="feed-post__comments hidden" data-comment-panel>
      <div class="mb-3 space-y-2" data-comments></div>
      <form class="feed-comment-form" data-comment-form>
        <label class="sr-only" for="${commentInputId}">Write a comment</label>
        <input id="${commentInputId}" name="comment" class="feed-comment-form__input" placeholder="Write a comment…" required>
        <button type="submit" class="ui-button ui-button--soft">Send</button>
      </form>
    </div>`;

  post.querySelector("[data-post-content]").textContent = content;
  return post;
}

document.addEventListener("click", async (event) => {
  const target = event.target instanceof Element ? event.target : null;
  if (!target) return;

  const menuToggle = target.closest("[data-menu-toggle]");
  if (menuToggle) {
    const menu = document.querySelector(menuToggle.dataset.menuToggle);
    if (menu) {
      const isOpen = menu.classList.toggle("hidden") === false;
      menuToggle.setAttribute("aria-expanded", String(isOpen));
    }
    return;
  }

  const mobileToggle = target.closest("[data-mobile-menu-toggle]");
  if (mobileToggle) {
    const menu = document.querySelector("[data-mobile-menu]");
    if (menu) {
      const isOpen = menu.classList.toggle("hidden") === false;
      mobileToggle.setAttribute("aria-expanded", String(isOpen));
    }
    return;
  }

  const action = target.closest("[data-action]");
  if (action) {
    const card = action.closest("[data-post-card]");
    if (action.dataset.action === "like" && card) {
      const isLiked = action.getAttribute("aria-pressed") === "true";
      const count = card.querySelector("[data-like-count]");
      const nextCount = Math.max(0, Number(count?.textContent ?? 0) + (isLiked ? -1 : 1));
      action.setAttribute("aria-pressed", String(!isLiked));
      action.classList.toggle("text-[#0866ff]", !isLiked);
      action.classList.toggle("text-[#65676b]", isLiked);
      if (count) count.textContent = String(nextCount);
    } else if (action.dataset.action === "comments" && card) {
      const panel = card.querySelector("[data-comment-panel]");
      if (panel) {
        const isOpen = panel.classList.toggle("hidden") === false;
        if (isOpen) panel.querySelector("input")?.focus();
      }
    } else if (action.dataset.action === "share" && card) {
      try {
        await navigator.clipboard.writeText(card.querySelector("[data-post-content]")?.textContent ?? "");
        showToast("Post text copied to clipboard");
      } catch {
        showToast("Sharing is unavailable in this browser");
      }
    }
    return;
  }

  if (!target.closest("[data-menu-panel]")) {
    document.querySelectorAll("[data-menu-panel]").forEach((menu) => menu.classList.add("hidden"));
    document.querySelectorAll("[data-menu-toggle]").forEach((toggle) => toggle.setAttribute("aria-expanded", "false"));
  }
});

document.addEventListener("submit", (event) => {
  const form = event.target;
  if (!(form instanceof HTMLFormElement)) return;

  if (form.matches("[data-post-form]")) {
    event.preventDefault();
    const input = form.querySelector("[name='post']");
    const content = input?.value.trim();
    if (!content) return;

    const feed = document.querySelector("[data-feed]");
    feed?.prepend(createPost(content));
    form.reset();
    input?.focus();
  } else if (form.matches("[data-comment-form]")) {
    event.preventDefault();
    const input = form.querySelector("[name='comment']");
    const content = input?.value.trim();
    if (!content) return;

    const card = form.closest("[data-post-card]");
    const comments = card?.querySelector("[data-comments]");
    comments?.append(makeComment(content));
    input.value = "";

    const countButton = card?.querySelector('[data-action="comments"]');
    if (countButton) {
      const count = Number(countButton.dataset.commentCount ?? 0) + 1;
      countButton.dataset.commentCount = String(count);
      countButton.textContent = `${count} ${count === 1 ? "comment" : "comments"}`;
    }
  } else if (form.matches("[data-forgot-password-form]")) {
    event.preventDefault();
    const message = form.querySelector("[data-form-message]");
    if (message) {
      message.textContent = "Password recovery is not set up yet. Please contact the site administrator.";
      message.classList.remove("hidden");
    }
  }
});

document.addEventListener("input", (event) => {
  const input = event.target;
  if (!(input instanceof HTMLInputElement) || !input.matches("[data-post-search]")) return;

  const query = input.value.trim().toLocaleLowerCase();
  document.querySelectorAll("[data-feed] [data-post-card]").forEach((post) => {
    post.classList.toggle("hidden", !post.textContent.toLocaleLowerCase().includes(query));
  });
});
