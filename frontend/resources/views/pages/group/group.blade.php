<x-layouts.main title="Groups | Facebook">
    <x-atoms.navbar />

    <main class="groups-layout">
        <aside class="groups-sidebar ui-card p-4">
            <h1 class="text-2xl font-bold">Groups</h1>
            <label class="mt-4 flex items-center gap-2 rounded-full bg-[#f0f2f5] px-4 py-2.5 text-[#65676b]">
                <span aria-hidden="true">⌕</span>
                <input type="search" class="w-full bg-transparent text-sm text-[#1c1e21] outline-none" placeholder="Search groups">
            </label>
            <nav class="mt-4 space-y-1" aria-label="Groups navigation">
                <a href="#your-groups" class="block rounded-lg bg-[#e7f3ff] px-3 py-3 font-semibold text-[#0866ff]">Your groups</a>
                <a href="#discover" class="block rounded-lg px-3 py-3 font-semibold hover:bg-[#f0f2f5]">Discover</a>
                <button type="button" class="ui-button ui-button--primary ui-button--block mt-2">＋ Create group</button>
            </nav>

            <div class="my-4 border-t border-[#e4e6eb]"></div>
            <h2 class="font-semibold">Your communities</h2>
            <a href="#community-feed" class="mt-2 flex items-center gap-3 rounded-lg p-2 hover:bg-[#f0f2f5]">
                <span class="grid size-10 place-items-center rounded-lg bg-emerald-100 text-xl">🌱</span>
                <span><span class="block text-sm font-semibold">Home Gardeners</span><span class="text-xs text-[#65676b]">12 new posts</span></span>
            </a>
            <a href="#community-feed" class="flex items-center gap-3 rounded-lg p-2 hover:bg-[#f0f2f5]">
                <span class="grid size-10 place-items-center rounded-lg bg-sky-100 text-xl">📷</span>
                <span><span class="block text-sm font-semibold">Weekend Photography</span><span class="text-xs text-[#65676b]">3 new posts</span></span>
            </a>
            <a href="#community-feed" class="flex items-center gap-3 rounded-lg p-2 hover:bg-[#f0f2f5]">
                <span class="grid size-10 place-items-center rounded-lg bg-amber-100 text-xl">🍜</span>
                <span><span class="block text-sm font-semibold">Easy Home Cooking</span><span class="text-xs text-[#65676b]">8 new posts</span></span>
            </a>
        </aside>

        <div class="min-w-0 space-y-6">
            <section id="your-groups" class="ui-card p-5">
                <p class="text-sm font-semibold text-[#0866ff]">YOUR COMMUNITIES</p>
                <h2 class="mt-1 text-2xl font-bold">Your groups</h2>
                <p class="mt-1 text-sm text-[#65676b]">Keep up with the communities you care about.</p>

                <div class="group-card-grid mt-4 grid gap-3 sm:grid-cols-2">
                    <a href="#community-feed" class="group-card">
                        <div class="group-card__cover bg-gradient-to-br from-emerald-200 to-lime-100">🌱</div>
                        <div class="group-card__body">
                            <h3 class="font-bold">Home Gardeners</h3>
                            <p class="group-card__description">A friendly place to grow together.</p>
                            <p class="group-card__meta">18.4K members · 12 new posts</p>
                        </div>
                    </a>
                    <a href="#community-feed" class="group-card">
                        <div class="group-card__cover bg-gradient-to-br from-sky-200 to-indigo-100">📷</div>
                        <div class="group-card__body">
                            <h3 class="font-bold">Weekend Photography</h3>
                            <p class="group-card__description">Share what you see through your lens.</p>
                            <p class="group-card__meta">32.1K members · 3 new posts</p>
                        </div>
                    </a>
                    <a href="#community-feed" class="group-card">
                        <div class="group-card__cover bg-gradient-to-br from-amber-200 to-orange-100">🍜</div>
                        <div class="group-card__body">
                            <h3 class="font-bold">Easy Home Cooking</h3>
                            <p class="group-card__description">Everyday recipes and kitchen tips.</p>
                            <p class="group-card__meta">9.7K members · 8 new posts</p>
                        </div>
                    </a>
                </div>
            </section>

            <section id="discover" class="ui-card p-5">
                <p class="text-sm font-semibold text-[#0866ff]">MEET YOUR PEOPLE</p>
                <h2 class="mt-1 text-2xl font-bold">Discover groups</h2>
                <div class="group-card-grid mt-4 grid gap-3 sm:grid-cols-2">
                    <article class="group-card">
                        <div class="group-card__cover bg-gradient-to-br from-violet-200 to-fuchsia-100">🎨</div>
                        <div class="group-card__body">
                            <h3 class="font-bold">Creative Corner</h3>
                            <p class="group-card__description">Artists sharing inspiration and works in progress.</p>
                            <p class="group-card__meta">8.2K members</p>
                            <button type="button" class="ui-button ui-button--soft ui-button--block mt-3">Join group</button>
                        </div>
                    </article>
                    <article class="group-card">
                        <div class="group-card__cover bg-gradient-to-br from-rose-200 to-orange-100">🐾</div>
                        <div class="group-card__body">
                            <h3 class="font-bold">Pet Parents Club</h3>
                            <p class="group-card__description">Cute pets, helpful advice, and friendly faces.</p>
                            <p class="group-card__meta">24.6K members</p>
                            <button type="button" class="ui-button ui-button--soft ui-button--block mt-3">Join group</button>
                        </div>
                    </article>
                    <article class="group-card">
                        <div class="group-card__cover bg-gradient-to-br from-cyan-200 to-blue-100">🏃</div>
                        <div class="group-card__body">
                            <h3 class="font-bold">Local Running Friends</h3>
                            <p class="group-card__description">Find running buddies and share your progress.</p>
                            <p class="group-card__meta">5.3K members</p>
                            <button type="button" class="ui-button ui-button--soft ui-button--block mt-3">Join group</button>
                        </div>
                    </article>
                </div>
            </section>

            <section id="community-feed" class="group-feed">
                <div>
                    <p class="text-sm font-semibold text-[#0866ff]">FROM YOUR COMMUNITIES</p>
                    <h2 class="mt-1 text-2xl font-bold">Recent group activity</h2>
                </div>
                <article class="feed-card" data-post-card>
                    <div class="feed-post__header">
                        <span class="ui-avatar bg-emerald-100 text-emerald-800">🌱</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold">Home Gardeners</p>
                            <p class="text-xs text-[#65676b]">Mia Thompson · 1 hour ago</p>
                        </div>
                    </div>
                    <p class="feed-post__body" data-post-content>My balcony tomatoes are finally turning red! Sharing a few things that helped: consistent morning watering, plenty of sun, and a little patience. What are you growing this season?</p>
                    <div class="feed-post__stats"><span>👍 <span data-like-count>36</span></span><button type="button" data-action="comments" data-comment-count="4">4 comments</button></div>
                    <div class="feed-post__actions">
                        <button type="button" class="feed-post__action" data-action="like" aria-pressed="false">👍 <span>Like</span></button>
                        <button type="button" class="feed-post__action" data-action="comments">💬 <span>Comment</span></button>
                        <button type="button" class="feed-post__action" data-action="share">↗ <span>Share</span></button>
                    </div>
                    <div class="feed-post__comments hidden" data-comment-panel>
                        <div class="mb-3 space-y-2" data-comments></div>
                        <form class="feed-comment-form" data-comment-form>
                            <label class="sr-only" for="group-comment">Write a comment</label>
                            <input id="group-comment" name="comment" class="feed-comment-form__input" placeholder="Write a comment…" required>
                            <button type="submit" class="ui-button ui-button--soft">Send</button>
                        </form>
                    </div>
                </article>
            </section>
        </div>
    </main>
</x-layouts.main>