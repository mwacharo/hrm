<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ucfirst(config('app.name')) }} - HRMIS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app" class="main-wrapper">

        <!-- Header -->
        @include('includes.header')
        <!-- /Header -->

        <!-- Sidebar -->
        @include('includes.sidebar')
        <!-- /Sidebar -->

        <!-- main content -->
        <main class="page-wrapper">
            @yield('content_one')
            <div class="content">
                <div class="page-header">
                    @yield('page-header')
                </div>
                @yield('content')
            </div>
        </main>
        <!-- /main content -->
    </div>
</body>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

</html>
