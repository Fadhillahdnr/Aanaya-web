<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'Aanaya') }}
    </title>

    <!-- FONT -->
    <link rel="preconnect"
          href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
          rel="stylesheet" />

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- VITE -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased">

    <div class="min-h-screen">

        {{-- NAVIGATION --}}
        @include('layouts.navigation')

        {{-- OPTIONAL HEADER --}}
        @isset($header)

            <header class="page-header">

                <div class="page-header-container">

                    {{ $header }}

                </div>

            </header>

        @endisset

        {{-- MAIN CONTENT --}}
        <main>

            {{ $slot }}

        </main>

    </div>
    
    {{-- FOOTER --}}
    @include('layouts.footer')
</body>

</html>