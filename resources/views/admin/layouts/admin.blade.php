<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Panel - Aanaya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    {{-- ADMIN NAVBAR --}}
    @include('admin.layouts.admin-navbar')

    {{-- CONTENT --}}
    <main class="admin-container">

        @yield('content')

    </main>

    {{-- FOOTER --}}
    @include('admin.layouts.footer')

</body>

</html>