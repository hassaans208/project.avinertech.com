<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $__env->make('partials.meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.configuration', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="bg-gray-900 text-white min-h-screen flex">

<?php echo $__env->make('partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- Sidebar -->
<aside class="w-64 mt-16 bg-gray-800 h-screen p-4 overflow-y-auto">
    <h2 class="text-xl font-semibold mb-4">API Navigation</h2>

    <ul class="space-y-2">
        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceKey => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li x-data="{ open: false }">
                <!-- Service Dropdown -->
                <button class="w-full text-left bg-gray-700 p-2 rounded flex justify-between items-center"
                        @click="open = !open">
                    <?php echo e($service['name']); ?>

                    <span x-text="open ? '▲' : '▼'"></span>
                </button>

                <ul x-show="open" class="mt-2 space-y-1 pl-4">
                    <?php $__currentLoopData = $service['apis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apiKey => $api): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a href="#<?php echo e($serviceKey); ?>-<?php echo e($apiKey); ?>"
                               class="block bg-gray-700 hover:bg-gray-600 p-2 rounded text-sm">
                                <?php echo e(ucfirst(str_replace('-', ' ', $apiKey))); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</aside>

<main class="flex-1 p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">API Reference</h1>

    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceKey => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mb-8 bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold"><?php echo e($service['name']); ?></h2>
            <p class="text-gray-400 text-sm">Base URL: <span class="text-blue-400"><?php echo e($service['url']); ?></span></p>

            <div class="mt-4 space-y-4">
                <?php $__currentLoopData = $service['apis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apiKey => $api): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div id="<?php echo e($serviceKey); ?>-<?php echo e($apiKey); ?>" x-data="{ open: false }"
                         class="border border-gray-700 rounded-lg overflow-hidden">
                        <!-- API Header -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 cursor-pointer"
                             @click="open = !open">
                            <div>
                                <span class="text-sm text-gray-300"><?php echo e(strtoupper($api['method'])); ?></span>
                                <span class="ml-2 text-lg font-semibold"><?php echo e($api['endpoint']); ?></span>
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
                                <?php $__currentLoopData = $api['body_params']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="border-b border-gray-700">
                                        <td class="p-2"><?php echo e($param['name']); ?></td>
                                        <td class="p-2 text-center">
                                            <?php if($param['required']): ?> ✅ <?php else: ?> ❌ <?php endif; ?>
                                        </td>
                                        <td class="p-2 text-gray-400"><?php echo e($param['description']); ?></td>
                                        <td class="p-2 text-blue-300"><?php echo e($param['datatype']); ?></td>
                                        <td class="p-2 text-green-400"><?php echo e($param['example']); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>

                            <?php if(!empty($api['query_params'])): ?>
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
                                    <?php $__currentLoopData = $api['query_params']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="border-b border-gray-700">
                                            <td class="p-2"><?php echo e($param['name']); ?></td>
                                            <td class="p-2 text-center">
                                                <?php if($param['required']): ?> ✅ <?php else: ?> ❌ <?php endif; ?>
                                            </td>
                                            <td class="p-2 text-gray-400"><?php echo e($param['description']); ?></td>
                                            <td class="p-2 text-blue-300"><?php echo e($param['datatype']); ?></td>
                                            <td class="p-2 text-green-400"><?php echo e($param['example']); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <!-- cURL Request Section -->
                            <h3 class="text-lg font-semibold text-blue-400 mt-4">cURL Request</h3>
                            <div class="relative bg-gray-700 p-4 rounded-md text-sm text-gray-300">
                                <pre id="curl-command-<?php echo e($serviceKey); ?>-<?php echo e($apiKey); ?>" class="overflow-auto">
curl -X <?php echo e(strtoupper($api['method'])); ?> "<?php echo e($service['url']); ?><?php echo e($api['endpoint']); ?>" \
     -H "Content-Type: application/json" \
     -d '{
        <?php $__currentLoopData = $api['body_params']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paramKey => $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        "<?php echo e($param['name']); ?>": "<?php echo e($param['example']); ?>",
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     }'
                                </pre>
                                <!-- Copy Button -->
                                <button onclick="copyToClipboard('curl-command-<?php echo e($serviceKey); ?>-<?php echo e($apiKey); ?>')"
                                        class="absolute top-2 right-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                    Copy
                                </button>
                            </div>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH /var/www/sites/Project/core.avinertech.com/Services/developers.avinertech.com/resources/views/welcome.blade.php ENDPATH**/ ?>