<!doctype html>
<html dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'eshop')</title>



    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito"
        rel="stylesheet">

    <!-- Styles -->
    @if (app()->isLocale('ar'))
    <link rel="stylesheet"
        href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css"
        integrity="sha384-vus3nQHTD+5mpDiZ4rkEPlnkcyTP+49BhJ4wJeJunw06ZAp+wzzeBPUXr42fi8If"
        crossorigin="anonymous">
    @else
    <!-- TODO add bootstrap ltr native cdn link -->
    @endif
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <ul class="nav justify-content-end bg-dark text-light col-12">
            @guest
            <li class="nav-item">
                <a class="nav-link text-light"
                    href="{{ LaravelLocalization::localizeUrl('(/register)') }}">
                    @lang('t.nav.account_create')
                </a>
            </li>
            @endguest
            <li class="nav-item">
                <a class="nav-link text-light"
                    href="{{ LaravelLocalization::localizeUrl('(/daily)') }}">
                    @lang('t.nav.daily')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light"
                    href="{{ LaravelLocalization::localizeUrl('(/addItem)') }}">
                    @lang('t.nav.sellUs')
                </a>
            </li>

            <li class="nav-item">
                @if (app()->isLocale('en'))
                <a class="nav-link text-light" rel="alternate" hreflang="ar"
                    href="/ar{{
                        Str::after(url()->current(), LaravelLocalization::getCurrentLocale())
                    }}">العربية
                </a>
                @else
                <a class="nav-link text-light" rel="alternate" hreflang="en"
                    href="/en{{
                        Str::after(url()->current(), LaravelLocalization::getCurrentLocale())
                    }}">English
                </a>
                @endif
            </li>
        </ul>
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button"
                    data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse"
                    id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown"
                                class="nav-link dropdown-toggle" href="#"
                                role="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"
                                v-pre>
                                {{ Auth::user()->name }} <span
                                    class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="navbarDropdown">
                                <a class="dropdown-item"
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form"
                                    action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
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
            <{{$cpt ?? ''}} :data="h">
                @yield('content')
            </{{$cpt ?? ''}}>
        </main>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{asset('js/bootstrap-native-v4.min.js')}}" defer></script>
</body>

</html>