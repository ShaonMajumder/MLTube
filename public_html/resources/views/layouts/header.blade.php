@php
    $theme = 'light';
@endphp
<nav class="navbar navbar-expand-md navbar-light shadow-sm">
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
                                <avatar-user :image="'{{ isset($channel) && $channel->image() ?? ''}}'" :href="'{{ route(\App\Enums\RouteEnum::CHANNELS_SHOW, auth()->user()->channel()->first()->id ) }}'" :username="'{{ Auth::user()->name }}'" :size=35 :rounded=true ></avatar-user>
                            </div>
                            <div class="px-1">
                                
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @auth
                                        <a class="dropdown-item" href="{{ route(\App\Enums\RouteEnum::CHANNELS_SHOW, auth()->user()->channel()->first()->id ) }}">
                                            My Channel
                                        </a>    
                                    @endauth
                                    <a class="dropdown-item" href="#" id="themeSwitcher">
                                        Theme: 
                                        <i id="lightIcon" class="fas fa-sun {{ $theme == 'light' ? 'active' : 'inactive' }}"></i>
                                        <i id="darkIcon" class="fas fa-moon {{ $theme == 'dark' ? 'active' : 'inactive' }}"></i>
                                    </a>
                                    
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
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