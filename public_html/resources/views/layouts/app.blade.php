<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-user-data" data-auth-user='@json(auth()->user())' style="display: none;">

    <title>@yield('title', config('app.name', 'Laravel') )</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/banglatube.css') }}" rel="stylesheet">
    {{-- <link href="{{ mix('css/app.css') }}" rel="stylesheet"> --}}
    <link id="light-theme" rel="stylesheet" href="{{ mix('css/theme-light.css') }}">
    <link id="dark-theme" rel="stylesheet" href="{{ mix('css/theme-dark.css') }}" disabled>
    <link href="{{ mix('css/theme.css') }}" rel="stylesheet">
    
    @yield('styles')

</head>
<body class="{{ isset($theme) && $theme == 'dark' ? 'dark-theme' : 'light-theme' }}" data-theme="{{ $theme }}">
    
    <div id="app">
        
        <!-- Header -->
        <!-- Search -->
        @include('layouts.header')

        <div class="container-fullwidth">
            <div class="row">
                <!-- Sidebar -->
                <aside class="col-md-2 col-lg-2 ">
                    @include('layouts.sidebar')
                </aside>

                <!-- Main Content -->
                <main class="col-md-10 col-lg-10 main-content">
                    

                    <!-- Breadcrumb -->
                    @if(View::hasSection('breadcrumb'))
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                        </svg>
                                    </a>
                                </li>
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    @endif

                    <div class="py-4">
                        @yield('content') 
                    </div>
                </main>
            </div>
        </div>
    </div>
    
    <script src="{{ mix('js/app.js') }}"></script>
    @yield('footer-script')


    @include('admin/manage-site.push-notification.footer-script')
</body>
</html>
