<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AvinerTech</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Vite Development Mode Asset Handling -->
        @if (app()->environment('local'))
            @php
                $viteServer = 'http://' . request()->getHost() . ':5173';
                if (request()->getHost() === 'localhost') {
                    $viteServer = 'http://localhost:5173';
                }
                // Add a test to see if the Vite dev server is running
                $devServerRunning = @file_get_contents($viteServer . '/@vite/client', false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false,]])) !== false;
            @endphp
            
            @if ($devServerRunning)
                <script type="module" src="{{ $viteServer }}/@vite/client"></script>
                <script type="module" src="{{ $viteServer }}/resources/js/app.js"></script>
                <link rel="stylesheet" href="{{ $viteServer }}/resources/css/app.css">
            @else
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @endif
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="antialiased">
        <div id="app"></div>

        <script>
            window.csrfToken = '{{ csrf_token() }}';
        </script>
    </body>
</html>
