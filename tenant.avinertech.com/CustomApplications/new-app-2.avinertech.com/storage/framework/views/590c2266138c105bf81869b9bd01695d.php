<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title>AvinerTech</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Vite Development Mode Asset Handling -->
        <?php if(app()->environment('local')): ?>
            <?php
                $viteServer = 'http://' . request()->getHost() . ':5173';
                if (request()->getHost() === 'localhost') {
                    $viteServer = 'http://localhost:5173';
                }
                // Add a test to see if the Vite dev server is running
                $devServerRunning = @file_get_contents($viteServer . '/@vite/client', false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false,]])) !== false;
            ?>
            
            <?php if($devServerRunning): ?>
                <script type="module" src="<?php echo e($viteServer); ?>/<?php echo app('Illuminate\Foundation\Vite')(); ?>/client"></script>
                <script type="module" src="<?php echo e($viteServer); ?>/resources/js/app.js"></script>
                <link rel="stylesheet" href="<?php echo e($viteServer); ?>/resources/css/app.css">
            <?php else: ?>
                <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php endif; ?>
    </head>
    <body class="antialiased">
        <div id="app"></div>

        <script>
            window.csrfToken = '<?php echo e(csrf_token()); ?>';
        </script>
    </body>
</html>
<?php /**PATH /var/www/sites/Project/tenant.avinertech.com/CustomApplications/new-app-2.avinertech.com/resources/views/welcome.blade.php ENDPATH**/ ?>