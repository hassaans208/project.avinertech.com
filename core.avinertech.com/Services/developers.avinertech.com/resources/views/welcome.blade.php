<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.meta')
    @include('partials.configuration')
</head>
<body class="bg-gray-900 text-white min-h-screen flex">

@include('partials.header')
<!-- Sidebar -->
<aside class="w-64 mt-16 bg-gray-800 h-screen p-4 overflow-y-auto">
    <h2 class="text-xl font-semibold mb-4">API Navigation</h2>

    <ul class="space-y-2">
        @foreach($services as $serviceKey => $service)
            <li x-data="{ open: false }">
                <!-- Service Dropdown -->
                <button class="w-full text-left bg-gray-700 p-2 rounded flex justify-between items-center"
                        @click="open = !open">
                    {{ $service['name'] }}
                    <span x-text="open ? '▲' : '▼'"></span>
                </button>

                <ul x-show="open" class="mt-2 space-y-1 pl-4">
                    @foreach($service['apis'] as $apiKey => $api)
                        <li>
                            <a href="#{{ $serviceKey }}-{{ $apiKey }}"
                               class="block bg-gray-700 hover:bg-gray-600 p-2 rounded text-sm">
                                {{ ucfirst(str_replace('-', ' ', $apiKey)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</aside>

<main class="flex-1 p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">API Reference</h1>

    @foreach($services as $serviceKey => $service)
        <div class="mb-8 bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold">{{ $service['name'] }}</h2>
            <p class="text-gray-400 text-sm">Base URL: <span class="text-blue-400">{{ $service['url'] }}</span></p>

            <div class="mt-4 space-y-4">
                @foreach($service['apis'] as $apiKey => $api)
                    <div id="{{ $serviceKey }}-{{ $apiKey }}" x-data="{ open: false }"
                         class="border border-gray-700 rounded-lg overflow-hidden">
                        <!-- API Header -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 cursor-pointer"
                             @click="open = !open">
                            <div>
                                <span class="text-sm text-gray-300">{{ strtoupper($api['method']) }}</span>
                                <span class="ml-2 text-lg font-semibold">{{ $api['endpoint'] }}</span>
                            </div>
                            <span x-text="open ? '-' : '+'" class="text-xl"></span>
                        </div>

                        <!-- API Details -->
                        <div x-show="open" class="bg-gray-800 p-4 space-y-3">
                            <h3 class="text-lg font-semibold text-blue-400">Body Parameters</h3>
                            <table class="w-full border border-gray-700 rounded-lg overflow-hidden">
                                <thead>
                                <tr class="bg-gray-700">
                                    <th class="p-2">Name</th>
                                    <th class="p-2">Required</th>
                                    <th class="p-2">Description</th>
                                    <th class="p-2">Type</th>
                                    <th class="p-2">Example</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($api['body_params'] as $param)
                                    <tr class="border-b border-gray-700">
                                        <td class="p-2">{{ $param['name'] }}</td>
                                        <td class="p-2 text-center">
                                            @if($param['required']) ✅ @else ❌ @endif
                                        </td>
                                        <td class="p-2 text-gray-400">{{ $param['description'] }}</td>
                                        <td class="p-2 text-blue-300">{{ $param['datatype'] }}</td>
                                        <td class="p-2 text-green-400">{{ $param['example'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            @if(!empty($api['query_params']))
                                <h3 class="text-lg font-semibold text-blue-400 mt-4">Query Parameters</h3>
                                <table class="w-full border border-gray-700 rounded-lg overflow-hidden">
                                    <thead>
                                    <tr class="bg-gray-700">
                                        <th class="p-2">Name</th>
                                        <th class="p-2">Required</th>
                                        <th class="p-2">Description</th>
                                        <th class="p-2">Type</th>
                                        <th class="p-2">Example</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($api['query_params'] as $param)
                                        <tr class="border-b border-gray-700">
                                            <td class="p-2">{{ $param['name'] }}</td>
                                            <td class="p-2 text-center">
                                                @if($param['required']) ✅ @else ❌ @endif
                                            </td>
                                            <td class="p-2 text-gray-400">{{ $param['description'] }}</td>
                                            <td class="p-2 text-blue-300">{{ $param['datatype'] }}</td>
                                            <td class="p-2 text-green-400">{{ $param['example'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <!-- cURL Request Section -->
                            <h3 class="text-lg font-semibold text-blue-400 mt-4">cURL Request</h3>
                            <div class="relative bg-gray-700 p-4 rounded-md text-sm text-gray-300">
                                <pre id="curl-command-{{ $serviceKey }}-{{ $apiKey }}" class="overflow-auto">
curl -X {{ strtoupper($api['method']) }} "{{ $service['url'] }}{{ $api['endpoint'] }}" \
     -H "Content-Type: application/json" \
     -d '{
        @foreach($api['body_params'] as $paramKey => $param)
                                        "{{ $param['name'] }}": "{{ $param['example'] }}",
                                    @endforeach
     }'
                                </pre>
                                <!-- Copy Button -->
                                <button onclick="copyToClipboard('curl-command-{{ $serviceKey }}-{{ $apiKey }}')"
                                        class="absolute top-2 right-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                    Copy
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</main>
<script>
    function copyToClipboard(elementId) {
        let text = document.getElementById(elementId).innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied to clipboard!");
        }).catch(err => {
            console.error("Error copying text: ", err);
        });
    }
</script>
</body>
</html>
