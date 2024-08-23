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

    <!-- Styles -->
    <link href="{{ asset('css/banglatube.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    
    <div id="app">
        
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <p id="navbar_icon">  {{ config('app.name', 'Laravel') }} </p> 
                    <!-- Slogan : shilpoke choya projukti-->
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <div class="row">
                                    <div class="px-1" style="width: 3rem;">
                                        <avatar-user :image="''" :href="'{{ route(\App\Enums\RouteEnum::MYACCOUNT_SHOW, auth()->user()->id ) }}'" :username="'{{ addslashes(auth()->user()->name) }}'" :size=35 :rounded=true ></avatar-user>
                                    </div>
                                    <div class="px-1">
                                        
                                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                            {{ Auth::user()->name }}
                                        </a>
    
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            @auth
                                                <a class="dropdown-item" href="{{ route(\App\Enums\RouteEnum::MYACCOUNT_SHOW ) }}">
                                                    My Account
                                                </a>    
                                            @endauth
                                            
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">
                                                {{ __('Logout') }}
                                            </a>
    
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </div>
                                        



                                    </div>
                                    
                                    
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        
        <!--Search-->
        @yield('search')

        <main class="py-4">
            {{-- @yield('content') --}}
            <div class="container">
                <h1>403</h1>
                <p>Forbidden. Sorry, you dont have access to visit this page.</p>
                <a href="{{ url('/') }}">Go back to Home</a>
            </div>
        </main>
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

