<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon and Icons -->
        <link rel="icon" type="image/png" href="/images/favicon.png">
        <link rel="shortcut icon" type="image/png" href="/images/favicon.png">
        <link rel="apple-touch-icon" href="/images/favicon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="theme-color" content="#8B5CF6">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Amiri:wght@400;700&family=Noto+Sans+Arabic:wght@400;600;700;900&display=swap" rel="stylesheet">

        <!-- Moyasar Payment Form -->
        <link rel="stylesheet" href="https://unpkg.com/moyasar-payment-form@2.0.16/dist/moyasar.css" />
        <script src="https://unpkg.com/moyasar-payment-form@2.0.16/dist/moyasar.umd.js"></script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
