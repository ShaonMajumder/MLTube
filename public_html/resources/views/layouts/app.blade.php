<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <!-- Styles -->
    <link href="{{ asset('css/banglatube.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    
    @yield('styles')
    <style>
        


        /*theme*/
        #themeSwitcher .active {
            opacity: 1;
            color: #FFD700; /* Bright color for active theme */
        }

        #themeSwitcher .inactive {
            opacity: 0.5;
            color: #888888; /* Faded color for inactive theme */
        }


        /* Light Theme */
        body.light-theme {
            background-color: #fff;
            color: #333;
        }

        .sidebar {
            background-color: #f8f9fa;
        }

        /* Dark Theme */
        body.dark-theme {
            background-color: #1a1a1a;
            color: #fff;
        }
/* 
        .sidebar {
            background-color: #333;
        } */

        /* Theme Toggle Button Styling */
        .theme-toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 24px;
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }

        .theme-toggle-btn i {
            transition: color 0.3s ease;
        }













    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentTheme = document.body.getAttribute('data-theme');
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            var navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.classList.remove('navbar-dark', 'navbar-light');
                navbar.classList.add(currentTheme === 'dark' ? 'navbar-dark' : 'navbar-light');
            }
            
            document.getElementById('themeSwitcher').addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default action of the link

                currentTheme = document.body.getAttribute('data-theme');
                let newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                navbar = document.querySelector('.navbar');
                if (navbar) {
                    navbar.classList.remove('navbar-dark', 'navbar-light');
                    navbar.classList.add(newTheme === 'dark' ? 'navbar-dark' : 'navbar-light');
                }

                // Toggle the data-theme attribute
                document.body.setAttribute('data-theme', newTheme);

                // Toggle the theme
                let bodyClassList = document.body.classList;
                bodyClassList.remove('light-theme', 'dark-theme');
                bodyClassList.add(newTheme  === 'dark' ? 'dark-theme' : 'light-theme');

                
                // Update the icons
                document.getElementById('lightIcon').classList.toggle('active');
                document.getElementById('lightIcon').classList.toggle('inactive');
                document.getElementById('darkIcon').classList.toggle('active');
                document.getElementById('darkIcon').classList.toggle('inactive');

                
                fetch('/update-theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ theme: newTheme })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Failed to update theme on the server');
                    }
                })
                .catch(error => console.error('Error:', error));
            });


            const toggleElements = document.querySelectorAll('[data-toggle="collapse"]');

            toggleElements.forEach(function(element) {
                element.addEventListener('click', function() {
                    const arrowIcon = this.querySelector('.fa-chevron-right');
                    
                    if (arrowIcon) {
                        arrowIcon.classList.toggle('fa-chevron-down');
                        arrowIcon.classList.toggle('fa-chevron-right');
                    }
                });
            });
        });



    </script>
</head>
<body class="{{ isset($theme) && $theme == 'dark' ? 'dark-theme' : 'light-theme' }}" data-theme="{{ $theme }}">
    
    <div id="app">
        <!-- Header -->
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

                    <!-- Search -->
                    @yield('search')

                    <div class="py-4">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        /*
        Window.AuthUser = '{!! auth()->user() !!}'
        Window.__auth = function(){
            try{
                return JSON.parse(AuthUser)
            }catch(error){
                return null
            }
        }
        */
        window.AuthUser = '{!! auth()->user() !!}'
        window.__auth = function () {
            try {
                return JSON.parse(AuthUser)
            } catch (error) {
                return null
            }
        }
    </script>
    
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
