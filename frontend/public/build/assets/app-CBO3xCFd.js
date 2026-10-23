var e=document.querySelector(`[data-toast-region]`),t=0;function n(t){e&&(e.textContent=t,e.classList.remove(`hidden`),window.clearTimeout(n.timeout),n.timeout=window.setTimeout(()=>{e.classList.add(`hidden`)},2600))}function r(e){let t=document.createElement(`p`);return t.className=`feed-comment`,t.textContent=e,t}function i(e){let n=`new-comment-${++t}`,r=document.createElement(`article`);return r.className=`feed-card`,r.dataset.postCard=``,r.innerHTML=`
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
        <label class="sr-only" for="${n}">Write a comment</label>
        <input id="${n}" name="comment" class="feed-comment-form__input" placeholder="Write a comment…" required>
        <button type="submit" class="ui-button ui-button--soft">Send</button>
      </form>
    </div>`,r.querySelector(`[data-post-content]`).textContent=e,r}document.addEventListener(`click`,async e=>{let t=e.target instanceof Element?e.target:null;if(!t)return;let r=t.closest(`[data-menu-toggle]`);if(r){let e=document.querySelector(r.dataset.menuToggle);if(e){let t=e.classList.toggle(`hidden`)===!1;r.setAttribute(`aria-expanded`,String(t))}return}let i=t.closest(`[data-mobile-menu-toggle]`);if(i){let e=document.querySelector(`[data-mobile-menu]`);if(e){let t=e.classList.toggle(`hidden`)===!1;i.setAttribute(`aria-expanded`,String(t))}return}let a=t.closest(`[data-action]`);if(a){let e=a.closest(`[data-post-card]`);if(a.dataset.action===`like`&&e){let t=a.getAttribute(`aria-pressed`)===`true`,n=e.querySelector(`[data-like-count]`),r=Math.max(0,Number(n?.textContent??0)+(t?-1:1));a.setAttribute(`aria-pressed`,String(!t)),a.classList.toggle(`text-[#0866ff]`,!t),a.classList.toggle(`text-[#65676b]`,t),n&&(n.textContent=String(r))}else if(a.dataset.action===`comments`&&e){let t=e.querySelector(`[data-comment-panel]`);t&&t.classList.toggle(`hidden`)===!1&&t.querySelector(`input`)?.focus()}else if(a.dataset.action===`share`&&e)try{await navigator.clipboard.writeText(e.querySelector(`[data-post-content]`)?.textContent??``),n(`Post text copied to clipboard`)}catch{n(`Sharing is unavailable in this browser`)}}else t.closest(`[data-menu-panel]`)||(document.querySelectorAll(`[data-menu-panel]`).forEach(e=>e.classList.add(`hidden`)),document.querySelectorAll(`[data-menu-toggle]`).forEach(e=>e.setAttribute(`aria-expanded`,`false`)))}),document.addEventListener(`submit`,e=>{let t=e.target;if(t instanceof HTMLFormElement){if(t.matches(`[data-post-form]`)){e.preventDefault();let n=t.querySelector(`[name='post']`),r=n?.value.trim();if(!r)return;document.querySelector(`[data-feed]`)?.prepend(i(r)),t.reset(),n?.focus()}else if(t.matches(`[data-comment-form]`)){e.preventDefault();let n=t.querySelector(`[name='comment']`),i=n?.value.trim();if(!i)return;let a=t.closest(`[data-post-card]`);(a?.querySelector(`[data-comments]`))?.append(r(i)),n.value=``;let o=a?.querySelector(`[data-action="comments"]`);if(o){let e=Number(o.dataset.commentCount??0)+1;o.dataset.commentCount=String(e),o.textContent=`${e} ${e===1?`comment`:`comments`}`}}else if(t.matches(`[data-forgot-password-form]`)){e.preventDefault();let n=t.querySelector(`[data-form-message]`);n&&(n.textContent=`Password recovery is not set up yet. Please contact the site administrator.`,n.classList.remove(`hidden`))}}}),document.addEventListener(`input`,e=>{let t=e.target;if(!(t instanceof HTMLInputElement)||!t.matches(`[data-post-search]`))return;let n=t.value.trim().toLocaleLowerCase();document.querySelectorAll(`[data-feed] [data-post-card]`).forEach(e=>{e.classList.toggle(`hidden`,!e.textContent.toLocaleLowerCase().includes(n))})});