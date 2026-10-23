<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Facebook' }}</title>
    @vite(['resources/css/index.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f0f2f5] text-[#1c1e21] antialiased">
    {{ $slot }}
    <div class="fixed bottom-4 left-1/2 z-[100] hidden -translate-x-1/2 rounded-lg bg-[#1c1e21] px-4 py-3 text-sm font-medium text-white shadow-lg" role="status" aria-live="polite" data-toast-region></div>
</body>
</html>