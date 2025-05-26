<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', 'resources/css/app.css']); ?>
    @inertiaHead
  </head>
  <body id="app" data-page="<?php echo e(json_encode($page)); ?>"">
    @inertia
    <script>
      window.page = <?php echo json_encode($page ?? [], 15, 512) ?>;
    </script>
  </body>
</html> <?php /**PATH /var/www/sites/Project/tenant.avinertech.com/CustomApplications/new-app-2.avinertech.com/resources/views/app.blade.php ENDPATH**/ ?>