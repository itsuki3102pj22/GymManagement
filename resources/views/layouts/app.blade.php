<!DOCTYPE html>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{-- フラッシュメッセージ --}}
            @if(session('success') || session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm"
                    style="background:var(--green-bg);border:1px solid var(--green-border);color:var(--green-dark)">
                    <span class="font-medium">✅ &nbsp;{{ session('success') }}</span>
                    <button @click="show = false"
                        style="color:var(--green-dark);opacity:.6"
                        class="ml-4 text-lg leading-none">×</button>
                </div>
                @endif
                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm"
                    style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626">
                    <span class="font-medium">❌ &nbsp;{{ session('error') }}</span>
                    <button @click="show = false"
                        style="color:#dc2626;opacity:.6"
                        class="ml-4 text-lg leading-none">×</button>
                </div>
                @endif
            </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>

</html>