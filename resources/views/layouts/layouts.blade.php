<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        @include('layouts.sidebar')
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            @include('layouts.header')
            
            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('javascript')
</body>
</html>
