<x-layouts.main title="Facebook - Home">
    <x-atoms.navbar />

    <main class="feed-layout">
        <aside class="feed-sidebar hidden xl:block">
            <div class="sticky top-20 space-y-1">
                <a href="/profile" class="flex items-center gap-3 rounded-lg p-3 font-semibold hover:bg-white">
                    <span class="grid size-9 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700">Y</span>
                    <span>Your profile</span>
                </a>
                <a href="{{ route('groups') }}" class="flex items-center gap-3 rounded-lg p-3 font-semibold hover:bg-white">
                    <span class="grid size-9 place-items-center rounded-full bg-blue-100 text-lg">👥</span>
                    <span>Friends</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg p-3 font-semibold hover:bg-white">
                    <span class="grid size-9 place-items-center rounded-full bg-purple-100 text-lg">👥</span>
                    <span>Groups</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg p-3 font-semibold hover:bg-white">
                    <span class="grid size-9 place-items-center rounded-full bg-green-100 text-lg">🛍️</span>
                    <span>Marketplace</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg p-3 font-semibold hover:bg-white">
                    <span class="grid size-9 place-items-center rounded-full bg-red-100 text-lg">▶️</span>
                    <span>Watch</span>
                </a>
                <div class="mx-3 my-3 border-t border-[#ced0d4]"></div>
                <p class="px-3 text-sm font-semibold text-[#65676b]">Your shortcuts</p>
                <p class="px-3 pt-2 text-xs leading-5 text-[#65676b]">Your favorite people and communities will show up here.</p>
            </div>
        </aside>

        <section class="feed-column" aria-label="News feed">
            <section class="feed-card feed-composer" aria-label="Create a post">
                <form data-post-form>
                    <div class="feed-composer__row">
                        <div class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700" aria-hidden="true">Y</div>
                        <label class="sr-only" for="post-content">What is on your mind?</label>
                        <textarea id="post-content" name="post" rows="1" required maxlength="2000" placeholder="What's on your mind, You?" class="feed-composer__input"></textarea>
                    </div>
                    <div class="feed-composer__footer">
                        <span class="text-sm font-semibold text-[#65676b]">Share a moment with friends</span>
                        <button type="submit" class="ui-button ui-button--primary">Post</button>
                    </div>
                </form>
            </section>

            <section class="feed-stories" aria-labelledby="stories-heading">
                <div class="mb-3 flex items-center justify-between">
                    <h2 id="stories-heading" class="text-[17px] font-bold">Stories</h2>
                    <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-[#0866ff] hover:bg-[#f0f2f5]">See all</button>
                </div>
                <div class="scrollbar-hide flex gap-2 overflow-x-auto pb-1">
                    <button type="button" class="relative flex min-h-48 min-w-[118px] flex-col overflow-hidden rounded-xl border border-[#e4e6eb] bg-white text-left sm:min-w-[120px]">
                        <span class="grid flex-1 place-items-center bg-[#e7f3ff] text-4xl text-[#0866ff]">+</span>
                        <span class="px-2 pb-3 pt-6 text-center text-sm font-semibold">Create story</span>
                    </button>
                    <button type="button" class="story-card story-photo-1 relative flex min-h-48 min-w-[118px] flex-col justify-between rounded-xl bg-cover bg-center p-3 text-left text-white sm:min-w-[120px]">
                        <span class="grid size-10 place-items-center rounded-full border-4 border-[#0866ff] bg-pink-200 font-bold text-pink-800">A</span><span class="text-sm font-semibold">Amanda</span>
                    </button>
                    <button type="button" class="story-card story-photo-2 relative flex min-h-48 min-w-[118px] flex-col justify-between rounded-xl bg-cover bg-center p-3 text-left text-white sm:min-w-[120px]">
                        <span class="grid size-10 place-items-center rounded-full border-4 border-[#0866ff] bg-amber-200 font-bold text-amber-900">M</span><span class="text-sm font-semibold">Marcus</span>
                    </button>
                    <button type="button" class="story-card story-photo-3 relative flex min-h-48 min-w-[118px] flex-col justify-between rounded-xl bg-cover bg-center p-3 text-left text-white sm:min-w-[120px]">
                        <span class="grid size-10 place-items-center rounded-full border-4 border-[#0866ff] bg-violet-200 font-bold text-violet-800">S</span><span class="text-sm font-semibold">Sophia</span>
                    </button>
                    <button type="button" class="story-card story-photo-4 relative flex min-h-48 min-w-[118px] flex-col justify-between rounded-xl bg-cover bg-center p-3 text-left text-white sm:min-w-[120px]">
                        <span class="grid size-10 place-items-center rounded-full border-4 border-[#0866ff] bg-teal-200 font-bold text-teal-800">J</span><span class="text-sm font-semibold">Jordan</span>
                    </button>
                </div>
            </section>

            <div class="flex items-center justify-between px-1">
                <h2 class="text-[17px] font-bold text-[#65676b]">Recent posts</h2>
                <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-[#0866ff] hover:bg-white">Filters</button>
            </div>

            <div class="space-y-4" data-feed>
                <article class="feed-card" data-post-card>
                    <div class="feed-post__header">
                        <div class="grid size-10 shrink-0 place-items-center rounded-full bg-amber-100 font-semibold text-amber-800" aria-hidden="true">A</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-[#050505]">Amanda Lee</p>
                            <p class="text-xs text-[#65676b]">2 hours ago · 🌐</p>
                        </div>
                        <button type="button" class="grid size-9 place-items-center rounded-full text-xl text-[#65676b] hover:bg-[#f0f2f5]" aria-label="Post options">···</button>
                    </div>
                    <p class="feed-post__body" data-post-content>A little sunshine and a slow afternoon. Hope everyone is having a lovely week ☀️</p>
                    <img class="post-photo" src="https://images.unsplash.com/photo-1470252649378-9c29740c9fa8?auto=format&fit=crop&w=1200&q=85" alt="Sunlight falling across a green field at sunset" loading="lazy">
                    <div class="feed-post__stats">
                        <span><span aria-hidden="true">👍</span> <span data-like-count>24</span></span>
                        <button type="button" class="hover:underline" data-action="comments" data-comment-count="3">3 comments</button>
                    </div>
                    <div class="feed-post__actions">
                        <button type="button" class="feed-post__action" data-action="like" aria-pressed="false">👍 <span>Like</span></button>
                        <button type="button" class="feed-post__action" data-action="comments">💬 <span>Comment</span></button>
                        <button type="button" class="feed-post__action" data-action="share">↗ <span>Share</span></button>
                    </div>
                    <div class="feed-post__comments hidden" data-comment-panel>
                        <div class="mb-3 space-y-2" data-comments></div>
                        <form class="feed-comment-form" data-comment-form>
                            <label class="sr-only" for="comment-amanda">Write a comment</label>
                            <input id="comment-amanda" name="comment" class="feed-comment-form__input" placeholder="Write a comment…" required>
                            <button type="submit" class="ui-button ui-button--soft">Send</button>
                        </form>
                    </div>
                </article>

                <article class="feed-card" data-post-card>
                    <div class="feed-post__header">
                        <div class="grid size-10 shrink-0 place-items-center rounded-full bg-purple-100 font-semibold text-purple-800" aria-hidden="true">M</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-[#050505]">Marcus Chen</p>
                            <p class="text-xs text-[#65676b]">5 hours ago · 🌐</p>
                        </div>
                        <button type="button" class="grid size-9 place-items-center rounded-full text-xl text-[#65676b] hover:bg-[#f0f2f5]" aria-label="Post options">···</button>
                    </div>
                    <p class="feed-post__body" data-post-content>Finally got out for a hike this weekend. Nature is the best reset 🌿</p>
                    <div class="feed-post__stats">
                        <span><span aria-hidden="true">❤️</span> <span data-like-count>18</span></span>
                        <button type="button" class="hover:underline" data-action="comments" data-comment-count="2">2 comments</button>
                    </div>
                    <div class="feed-post__actions">
                        <button type="button" class="feed-post__action" data-action="like" aria-pressed="false">👍 <span>Like</span></button>
                        <button type="button" class="feed-post__action" data-action="comments">💬 <span>Comment</span></button>
                        <button type="button" class="feed-post__action" data-action="share">↗ <span>Share</span></button>
                    </div>
                    <div class="feed-post__comments hidden" data-comment-panel>
                        <div class="mb-3 space-y-2" data-comments></div>
                        <form class="feed-comment-form" data-comment-form>
                            <label class="sr-only" for="comment-marcus">Write a comment</label>
                            <input id="comment-marcus" name="comment" class="feed-comment-form__input" placeholder="Write a comment…" required>
                            <button type="submit" class="ui-button ui-button--soft">Send</button>
                        </form>
                    </div>
                </article>
            </div>
        </section>

        <aside class="hidden lg:block">
            <div class="sticky top-20">
                <div class="flex items-center justify-between border-b border-[#ced0d4] pb-3">
                    <h2 class="font-semibold text-[#65676b]">Contacts</h2>
                    <div class="flex gap-1">
                        <button type="button" class="grid size-9 place-items-center rounded-full text-[#65676b] hover:bg-white" aria-label="Search contacts">
                            <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                        </button>
                        <button type="button" class="grid size-9 place-items-center rounded-full text-[#65676b] hover:bg-white" aria-label="Contact options">···</button>
                    </div>
                </div>
                <ul class="mt-2 space-y-1">
                    <li><a href="#" class="flex items-center gap-3 rounded-lg p-2 font-medium hover:bg-white"><span class="relative grid size-9 place-items-center rounded-full bg-green-100 font-semibold text-green-800">S<span class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-[#f0f2f5] bg-green-500"></span></span> Sophia Kim</a></li>
                    <li><a href="#" class="flex items-center gap-3 rounded-lg p-2 font-medium hover:bg-white"><span class="relative grid size-9 place-items-center rounded-full bg-amber-100 font-semibold text-amber-800">J<span class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-[#f0f2f5] bg-green-500"></span></span> Jordan Davis</a></li>
                    <li><a href="#" class="flex items-center gap-3 rounded-lg p-2 font-medium hover:bg-white"><span class="relative grid size-9 place-items-center rounded-full bg-rose-100 font-semibold text-rose-800">L<span class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-[#f0f2f5] bg-green-500"></span></span> Lily Nguyen</a></li>
                    <li><a href="#" class="flex items-center gap-3 rounded-lg p-2 font-medium hover:bg-white"><span class="relative grid size-9 place-items-center rounded-full bg-blue-100 font-semibold text-blue-800">D<span class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-[#f0f2f5] bg-green-500"></span></span> Daniel Park</a></li>
                </ul>
            </div>
        </aside>
    </main>
</x-layouts.main>