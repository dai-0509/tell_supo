<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    </head>
    <body class="font-sans antialiased overflow-hidden h-screen">
        <div class="app-container flex h-full w-full">
            <!-- サイドバー -->
            @include('layouts.sidebar')

            <!-- メインエリア -->
            <div class="main-wrapper flex-grow flex flex-col min-w-0">
                <!-- グローバルヘッダー -->
                @include('layouts.header')

                <!-- Page Content -->
                <main class="page-content flex-grow bg-gray-50 overflow-y-auto">
                    {{-- フラッシュメッセージ --}}
                    @if(session('success') || session('error') || session('warning'))
                        <div class="px-6 pt-4">
                            <x-flash />
                        </div>
                    @endif

                    <!-- Page Heading -->
                    @isset($header)
                        <div class="bg-white border-b border-gray-200">
                            <div class="px-6 py-4">
                                {{ $header }}
                            </div>
                        </div>
                    @endisset

                    <!-- Page Content -->
                    <div class="px-6 py-6">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
