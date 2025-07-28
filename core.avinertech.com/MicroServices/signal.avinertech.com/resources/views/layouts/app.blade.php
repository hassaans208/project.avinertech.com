<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="access-token" content="{{ $accessToken ?? '' }}">

    <title>{{ config('app.name', 'Laravel') }} - Signal Handler Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">Signal Handler Admin</h1>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('tenants.index', ['access_token' => request()->get('access_token', '')]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tenants.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Tenants
                            </a>
                            <a href="{{ route('packages.index', ['access_token' => request()->get('access_token', '')]) }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('packages.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Packages
                            </a>
                            <a href="/encryptor-decryptor?access_token={{ request()->get('access_token', '') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->is('encryptor-decryptor') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                🔐 Encryptor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    <script>
        // Set up global access token handling
        document.addEventListener('DOMContentLoaded', function() {
            const accessToken = document.querySelector('meta[name="access-token"]').getAttribute('content');
            
            if (accessToken) {
                // Set up XMLHttpRequest interceptor
                const originalOpen = XMLHttpRequest.prototype.open;
                XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                    this.addEventListener('readystatechange', function() {
                        if (this.readyState === 1) { // OPENED
                            this.setRequestHeader('Authorization', accessToken);
                        }
                    });
                    return originalOpen.call(this, method, url, async, user, pass);
                };

                // Set up fetch interceptor
                const originalFetch = window.fetch;
                window.fetch = function(input, init = {}) {
                    init.headers = init.headers || {};
                    if (typeof init.headers.append === 'function') {
                        init.headers.append('Authorization', accessToken);
                    } else {
                        init.headers['Authorization'] = accessToken;
                    }
                    return originalFetch.call(this, input, init);
                };

                // Add access token to all forms as hidden input
                const forms = document.querySelectorAll('form');
                forms.forEach(function(form) {
                    // Check if form already has access_token input
                    if (!form.querySelector('input[name="access_token"]')) {
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'access_token';
                        tokenInput.value = accessToken;
                        form.appendChild(tokenInput);
                    }
                });

                // Add access token to dynamically created forms
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                const forms = node.querySelectorAll ? node.querySelectorAll('form') : [];
                                forms.forEach(function(form) {
                                    if (!form.querySelector('input[name="access_token"]')) {
                                        const tokenInput = document.createElement('input');
                                        tokenInput.type = 'hidden';
                                        tokenInput.name = 'access_token';
                                        tokenInput.value = accessToken;
                                        form.appendChild(tokenInput);
                                    }
                                });
                            }
                        });
                    });
                });

                observer.observe(document.body, { childList: true, subtree: true });
            }
        });
    </script>
</body>
</html> 