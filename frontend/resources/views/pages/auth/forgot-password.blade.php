<x-layouts.main title="Find your account">
    <main class="auth-background flex min-h-screen items-center justify-center px-4 py-10">
        <section class="auth-card w-full max-w-[500px]">
            <header class="border-b border-[#dadde1] px-5 py-4">
                <h1 class="text-xl font-bold">Find your account</h1>
            </header>
            <form class="space-y-4 p-5" data-forgot-password-form>
                <p class="text-sm leading-6 text-[#606770]">Enter the email address associated with your account to receive password reset instructions.</p>
                <div>
                    <label class="sr-only" for="reset-email">Email address</label>
                    <input id="reset-email" type="email" name="email" autocomplete="email" required placeholder="Email address" class="auth-input">
                </div>
                <p class="hidden rounded-lg bg-green-50 px-3 py-2 text-sm text-green-800" role="status" data-form-message></p>
                <div class="flex justify-end gap-2 border-t border-[#dadde1] pt-4">
                    <a href="{{ route('login.form') }}" class="rounded-lg bg-[#e4e6eb] px-4 py-2 text-sm font-semibold hover:bg-[#d8dadf]">Cancel</a>
                    <button type="submit" class="rounded-lg bg-[#0866ff] px-4 py-2 text-sm font-semibold text-white hover:bg-[#075ce5]">Search</button>
                </div>
            </form>
        </section>
    </main>
</x-layouts.main>
