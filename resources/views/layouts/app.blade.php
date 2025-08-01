<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'openPIMS') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ mix('/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ mix('/bootstrap-select.min.css') }}">

    <link rel="icon" href="/favicon.png" type="image/png">

    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                @guest
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="/openpims.png" width="32" height="32" class="d-inline-block align-top" alt="">
                        {{ config('app.name', 'openPIMS') }}
                    </a>
                @else
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="/openpims.png" width="32" height="32" class="d-inline-block align-top" alt="">
                        {{ config('app.name', 'openPIMS') }}
                    </a>
                @endguest

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <!--li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li-->
                            @endif
                            @if (Route::has('register'))
                                <!--li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li-->
                            @endif
                        @else

                            <li class="nav-item">
                                <a class="nav-link" href="/">Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="/export">Export</a>
                            </li>

                            <!--li class="nav-item">
                                <a class="nav-link" href="/setup">Setup</a>
                            </li-->

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->email }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="{{ mix('/jquery.min.js') }}"></script>
    <script src="{{ mix('/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ mix('/bootstrap-select.min.js') }}"></script>
    @yield('script')
</body>
</html>
