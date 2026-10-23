<x-layouts.main title="Create an account">
    <main class="auth-background flex min-h-screen flex-col">
        <div class="mx-auto flex w-full max-w-[460px] flex-1 flex-col justify-center px-4 py-10">
            <div class="mb-5 text-center">
                <a href="/" class="text-5xl font-bold tracking-tight text-[#0866ff]">facebook</a>
            </div>
            <section class="auth-card">
                <header class="border-b border-[#dadde1] px-5 py-4 text-center">
                    <h1 class="text-2xl font-bold text-[#1c1e21]">Create a new account</h1>
                    <p class="mt-1 text-sm text-[#606770]">It's quick and easy.</p>
                </header>
                <form action="{{ route('register') }}" method="POST" class="space-y-4 p-5">
                    @csrf
                    @if ($errors->any())
                        <div class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700" role="alert">Please check your details and try again.</div>
                    @endif
                    <div>
                        <label class="auth-label" for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="Email address" class="auth-input">
                        @error('email')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="auth-label" for="password">New password</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" required minlength="8" placeholder="New password" class="auth-input">
                        @error('password')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs leading-4 text-[#777]">By clicking Sign Up, you agree to our Terms, Privacy Policy and Cookies Policy.</p>
                    <div class="text-center">
                        <button type="submit" class="ui-button auth-submit--create">Sign up</button>
                    </div>
                    <p class="text-center"><a href="{{ route('login.form') }}" class="text-sm font-medium text-[#0866ff] hover:underline">Already have an account?</a></p>
                </form>
            </section>
        </div>
    </main>
</x-layouts.main>
