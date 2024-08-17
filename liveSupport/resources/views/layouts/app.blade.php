<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Eterna Technology') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div id="app">
        <nav class="bg-white shadow-sm">
            <div class="container mx-auto flex justify-between items-center py-4">
                <a class="text-lg font-semibold text-gray-800" href="{{ url('/') }}">
                    {{ config('app.name', 'Eterna Technology') }}
                </a>
                <div class="flex items-center">
                    @guest
                        @if (Route::has('login'))
                            <a class="text-gray-600 hover:text-gray-800 mr-4" href="{{ route('login') }}">{{ __('Login') }}</a>
                        @endif
                        @if (Route::has('register'))
                            <a class="text-gray-600 hover:text-gray-800" href="{{ route('register') }}">{{ __('Register') }}</a>
                        @endif
                    @else
                        <div class="relative">
                            <input type="checkbox" id="dropdown-toggle" class="hidden peer" />
                            <label for="dropdown-toggle" class="text-gray-600 hover:text-gray-800 cursor-pointer">
                                {{ Auth::user()->name }}
                            </label>
                            <div class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg py-2 w-48 hidden peer-checked:block">
                                <a class="block px-4 py-2 text-gray-600 hover:bg-gray-100" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="container mx-auto py-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
