<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @routes
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @inertiaHead
  </head>
  <body id="app" data-page="{{ json_encode($page) }}"">
    @inertia
    <script>
      window.page = @json($page ?? []);
    </script>
  </body>
</html> 