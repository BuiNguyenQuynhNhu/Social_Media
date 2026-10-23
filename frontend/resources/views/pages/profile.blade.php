<x-layouts.main title="Your profile | Facebook">
    <x-atoms.navbar />
    <main class="mx-auto max-w-[1120px] px-3 pb-10 sm:px-5">
        <section class="overflow-hidden rounded-b-xl bg-white shadow-sm">
            <div class="h-40 bg-gradient-to-r from-blue-200 via-sky-100 to-indigo-200 sm:h-64"></div>
            <div class="px-4 pb-4 sm:px-8">
                <div class="-mt-12 flex flex-col items-center gap-4 sm:-mt-16 sm:flex-row sm:items-end">
                    <div class="grid size-28 shrink-0 place-items-center rounded-full border-4 border-white bg-blue-100 text-4xl font-bold text-blue-700 sm:size-36">Y</div>
                    <div class="min-w-0 flex-1 pb-2 text-center sm:text-left">
                        <h1 class="text-3xl font-bold">Your Name</h1>
                        <p class="mt-1 text-sm font-semibold text-[#65676b]">120 friends</p>
                    </div>
                    <div class="flex gap-2 pb-2">
                        <button type="button" class="rounded-lg bg-[#0866ff] px-4 py-2 font-semibold text-white hover:bg-[#075ce5]">＋ Add to story</button>
                        <button type="button" class="rounded-lg bg-[#e4e6eb] px-4 py-2 font-semibold hover:bg-[#d8dadf]">Edit profile</button>
                    </div>
                </div>
                <div class="mt-5 border-t border-[#e4e6eb] pt-2">
                    <nav class="-mb-2 flex gap-2 overflow-x-auto" aria-label="Profile sections">
                        <a href="#posts" class="border-b-[3px] border-[#0866ff] px-4 py-3 text-sm font-semibold text-[#0866ff]">Posts</a>
                        <a href="#about" class="rounded-t-lg px-4 py-3 text-sm font-semibold text-[#65676b] hover:bg-[#f0f2f5]">About</a>
                        <a href="#friends" class="rounded-t-lg px-4 py-3 text-sm font-semibold text-[#65676b] hover:bg-[#f0f2f5]">Friends</a>
                        <a href="#photos" class="rounded-t-lg px-4 py-3 text-sm font-semibold text-[#65676b] hover:bg-[#f0f2f5]">Photos</a>
                    </nav>
                </div>
            </div>
        </section>

        <div class="mx-auto mt-5 grid max-w-[960px] grid-cols-1 gap-4 md:grid-cols-[minmax(240px,0.8fr)_minmax(0,1.2fr)]">
            <aside class="space-y-4">
                <section id="about" class="rounded-xl bg-white p-4 shadow-sm">
                    <h2 class="text-xl font-bold">Intro</h2>
                    <p class="mt-3 text-center text-sm text-[#65676b]">Add a short bio to tell people a little about yourself.</p>
                    <button type="button" class="mt-3 w-full rounded-lg bg-[#e4e6eb] px-4 py-2 text-sm font-semibold hover:bg-[#d8dadf]">Add bio</button>
                </section>
                <section id="friends" class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div><h2 class="text-xl font-bold">Friends</h2><p class="text-sm text-[#65676b]">120 friends</p></div>
                        <a href="#" class="text-sm font-medium text-[#0866ff] hover:underline">See all friends</a>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs font-medium">
                        <div><div class="aspect-square rounded-lg bg-rose-100"></div><p class="mt-1">Amanda</p></div>
                        <div><div class="aspect-square rounded-lg bg-amber-100"></div><p class="mt-1">Marcus</p></div>
                        <div><div class="aspect-square rounded-lg bg-green-100"></div><p class="mt-1">Sophia</p></div>
                    </div>
                </section>
            </aside>

            <section id="posts" class="space-y-4" aria-label="Your posts">
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700">Y</div>
                        <a href="/" class="flex-1 rounded-full bg-[#f0f2f5] px-4 py-3 text-[15px] text-[#65676b] hover:bg-[#e9ebee]">What's on your mind?</a>
                    </div>
                </div>
                <article class="overflow-hidden rounded-xl bg-white shadow-sm" data-post-card>
                    <div class="flex items-center gap-3 px-4 pt-4">
                        <div class="grid size-10 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700">Y</div>
                        <div class="flex-1"><p class="text-sm font-semibold">Your Name</p><p class="text-xs text-[#65676b]">Yesterday · 🌐</p></div>
                    </div>
                    <p class="px-4 py-3 text-[15px]" data-post-content>Welcome to my profile! Happy to connect with friends here. 👋</p>
                    <div class="mx-4 flex items-center justify-between border-b border-[#e4e6eb] py-2 text-sm text-[#65676b]">
                        <span>👍 <span data-like-count>5</span></span>
                        <button type="button" class="hover:underline" data-action="comments" data-comment-count="0">0 comments</button>
                    </div>
                    <div class="grid grid-cols-3 gap-1 px-2 py-1">
                        <button type="button" class="rounded-lg py-2 text-sm font-semibold text-[#65676b] hover:bg-[#f0f2f5]" data-action="like" aria-pressed="false">👍 <span>Like</span></button>
                        <button type="button" class="rounded-lg py-2 text-sm font-semibold text-[#65676b] hover:bg-[#f0f2f5]" data-action="comments">💬 <span>Comment</span></button>
                        <button type="button" class="rounded-lg py-2 text-sm font-semibold text-[#65676b] hover:bg-[#f0f2f5]" data-action="share">↗ <span>Share</span></button>
                    </div>
                    <div class="hidden border-t border-[#e4e6eb] px-4 py-3" data-comment-panel>
                        <div class="mb-3 space-y-2" data-comments></div>
                        <form class="flex items-center gap-2" data-comment-form>
                            <label class="sr-only" for="profile-comment">Write a comment</label>
                            <input id="profile-comment" name="comment" class="min-w-0 flex-1 rounded-full bg-[#f0f2f5] px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500" placeholder="Write a comment…" required>
                            <button type="submit" class="font-semibold text-[#0866ff] hover:underline">Send</button>
                        </form>
                    </div>
                </article>
            </section>
        </div>
    </main>
</x-layouts.main>