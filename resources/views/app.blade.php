<!DOCTYPE html>
<html class="dark bg-neutral-900 text-white" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>Ёрдан</title>
        <meta name="description" content="На данном сайте вы можете создавать свои конланги. И делиться ими с другими людьми.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,500;0,700;1,300;1,500;1,700&display=swap" rel="stylesheet">
        <link href="https://fonts.cdnfonts.com/css/helvetica-neue-55?styles=15981,15980,15992,30130" rel="stylesheet">

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
