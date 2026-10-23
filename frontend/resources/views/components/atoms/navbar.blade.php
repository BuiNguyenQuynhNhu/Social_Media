<header class="sticky top-0 z-40 border-b border-[#dddfe2] bg-white shadow-sm">
    <div class="mx-auto flex h-16 max-w-[1920px] items-center justify-between gap-3 px-3 sm:px-5">
        <div class="flex min-w-0 items-center gap-2">
            <a href="/" class="grid size-10 shrink-0 place-items-center rounded-full bg-[#0866ff] text-2xl font-bold leading-none text-white" aria-label="Facebook home">f</a>
            <label class="hidden h-10 items-center gap-2 rounded-full bg-[#f0f2f5] px-4 text-[#65676b] sm:flex">
                <svg aria-hidden="true" class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>
                <input type="search" class="w-36 bg-transparent text-sm text-[#1c1e21] outline-none placeholder:text-[#65676b] md:w-48" placeholder="Search Facebook" aria-label="Search posts" data-post-search>
            </label>
        </div>

        <nav class="hidden h-full flex-1 items-center justify-center gap-1 md:flex" aria-label="Main navigation">
            <a href="/" aria-current="page" class="grid h-full w-20 place-items-center border-b-[3px] border-[#0866ff] text-[#0866ff]" aria-label="Home">
                <svg aria-hidden="true" class="size-7" viewBox="0 0 24 24" fill="currentColor"><path d="m12 3 9 8h-2v9h-5v-6h-4v6H5v-9H3l9-8Z"></path></svg>
            </a>
            <a href="/profile" class="grid h-12 w-20 place-items-center rounded-lg text-[#65676b] transition hover:bg-[#f0f2f5]" aria-label="Profile">
                <svg aria-hidden="true" class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
            </a>
            <button type="button" class="grid h-12 w-20 place-items-center rounded-lg text-[#65676b] transition hover:bg-[#f0f2f5]" aria-label="Watch">
                <svg aria-hidden="true" class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m10 9 5 3-5 3V9Z" fill="currentColor" stroke="none"></path></svg>
            </button>
            <a href="{{ route('groups') }}" class="grid h-12 w-20 place-items-center rounded-lg text-[#65676b] transition hover:bg-[#f0f2f5]" aria-label="Groups">
                <svg aria-hidden="true" class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"></circle><circle cx="17" cy="9" r="2.5"></circle><path d="M3 20a6 6 0 0 1 12 0m1-5a5 5 0 0 1 5 5"></path></svg>
            </a>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <button type="button" class="grid size-10 place-items-center rounded-full bg-[#e4e6eb] text-[#1c1e21] hover:bg-[#d8dadf] md:hidden" aria-label="Toggle navigation" aria-expanded="false" data-mobile-menu-toggle>
                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <button type="button" class="grid size-10 place-items-center rounded-full bg-[#e4e6eb] text-[#1c1e21] transition hover:bg-[#d8dadf]" aria-label="Messenger">
                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.5 2 2 6.1 2 11.2c0 2.9 1.4 5.5 3.7 7.2V22l3.4-1.9c.9.2 1.9.4 2.9.4 5.5 0 10-4.1 10-9.2S17.5 2 12 2Zm1 12-2.6-2.8L5.3 14l5.6-6 2.7 2.8L18.7 8l-5.7 6Z"></path></svg>
            </button>
            <button type="button" class="grid size-10 place-items-center rounded-full bg-[#e4e6eb] text-[#1c1e21] transition hover:bg-[#d8dadf]" aria-label="Notifications">
                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2.5 2.5 0 0 0 2.4-2h-4.8a2.5 2.5 0 0 0 2.4 2Zm7-6-2-2.2V10a5 5 0 0 0-4-4.9V4a1 1 0 0 0-2 0v1.1A5 5 0 0 0 7 10v3.8L5 16v1h14v-1Z"></path></svg>
            </button>
            <button type="button" class="grid size-10 place-items-center rounded-full bg-blue-100 font-semibold text-blue-700 ring-2 ring-white" aria-label="Open account menu" aria-expanded="false" aria-controls="account-menu" data-menu-toggle="#account-menu">Y</button>
            <div id="account-menu" class="absolute right-3 top-[4.5rem] hidden w-64 rounded-xl bg-white p-2 shadow-xl ring-1 ring-black/5" data-menu-panel>
                <a href="/profile" class="flex items-center gap-3 rounded-lg p-3 text-sm font-semibold hover:bg-[#f0f2f5]">Your profile</a>
                <a href="/login" class="flex items-center gap-3 rounded-lg p-3 text-sm font-semibold hover:bg-[#f0f2f5]">Log in</a>
            </div>
        </div>
    </div>
    <nav class="hidden border-t border-[#e4e6eb] bg-white px-4 py-2 md:hidden" aria-label="Mobile navigation" data-mobile-menu>
        <a href="/" class="block rounded-lg px-3 py-2 font-semibold text-[#0866ff] hover:bg-[#f0f2f5]">Home</a>
        <a href="/profile" class="block rounded-lg px-3 py-2 font-semibold text-[#1c1e21] hover:bg-[#f0f2f5]">Profile</a>
        <a href="{{ route('groups') }}" class="block rounded-lg px-3 py-2 font-semibold text-[#1c1e21] hover:bg-[#f0f2f5]">Groups</a>
    </nav>
</header>