<x-layouts.main title="Log in to Facebook">
    <main class="auth-background flex min-h-screen flex-col">
        <div class="mx-auto grid w-full max-w-5xl flex-1 items-center gap-10 px-5 py-12 md:grid-cols-[1.1fr_0.9fr] md:gap-16">
            <section class="text-center md:text-left">
                <a href="/" class="text-5xl font-bold tracking-tight text-[#0866ff] sm:text-6xl">facebook</a>
                <p class="mx-auto mt-4 max-w-lg text-xl leading-8 text-[#1c1e21] sm:text-2xl md:mx-0">Connect with friends and the world around you.</p>
            </section>

            <section class="mx-auto w-full max-w-[400px]">
                <div class="auth-card p-4 sm:p-5">
                    <form action="{{ route('login') }}" method="POST" class="space-y-3">
                        @csrf
                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700" role="alert">Please check your details and try again.</div>
                        @endif
                        <div>
                            <label class="sr-only" for="email">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="Email address" class="auth-input">
                            @error('email')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="sr-only" for="password">Password</label>
                            <input id="password" type="password" name="password" autocomplete="current-password" required placeholder="Password" class="auth-input">
                            @error('password')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="auth-submit">Log in</button>
                        <div class="border-b border-[#dadde1] pb-4 text-center">
                            <a href="{{ route('password.request') }}" class="inline-block py-2 text-sm font-medium text-[#0866ff] hover:underline">Forgot password?</a>
                        </div>
                    </form>
                    <div class="pt-4 text-center">
                        <a href="{{ route('register.form') }}" class="ui-button auth-submit--create">Create new account</a>
                    </div>
                </div>
                <p class="mt-7 text-center text-sm text-[#1c1e21]"><a href="{{ route('register.form') }}" class="font-semibold hover:underline">Create a Page</a> for a celebrity, brand or business.</p>
            </section>
        </div>
        <footer class="bg-white px-5 py-6 text-center text-xs text-[#737373]">English (US) · Privacy · Terms · Cookies · About</footer>
    </main>
</x-layouts.main>
