<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - Aanaya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="admin-shell">

    <a href="#admin-main-content" class="admin-skip-link">
        Skip to main content
    </a>

    {{-- ADMIN NAVBAR --}}
    @include('admin.layouts.admin-navbar')

    {{-- CONTENT --}}
    <main id="admin-main-content" class="admin-main">

        @yield('content')

    </main>

    {{-- FOOTER --}}
    @include('admin.layouts.footer')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
</body>

</html>
