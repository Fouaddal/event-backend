<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() == 'ar') dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Language Switcher -->
        <!--div class="px-4 py-2 bg-white border-b border-gray-200 text-right text-sm">
            🌐
            <a href="{{ route('lang.switch', 'en') }}" class="text-blue-600 hover:underline">English</a> |
            <a href="{{ route('lang.switch', 'ar') }}" class="text-blue-600 hover:underline">العربية</a>
        </div-->

        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                      
                    </h2>
                </div>
            </header>
        @endif

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
