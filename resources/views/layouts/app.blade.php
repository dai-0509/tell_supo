<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>TellSupo - テレアポ業務支援ダッシュボード</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <style>
            /* カスタムCSS変数 */
            :root {
                --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
                --glass-bg: rgba(255, 255, 255, 0.1);
                --glass-border: rgba(255, 255, 255, 0.2);
            }
            
            /* スムーズスクロール */
            html {
                scroll-behavior: smooth;
            }
            
            /* カスタムスクロールバー */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            }
            
            /* グラデーション背景アニメーション */
            .gradient-bg {
                background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }
            
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            /* ガラスモーフィズム効果 */
            .glass {
                background: var(--glass-bg);
                backdrop-filter: blur(20px);
                border: 1px solid var(--glass-border);
            }
            
            /* ホバーエフェクト */
            .hover-lift {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .hover-lift:hover {
                transform: translateY(-8px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
            
            /* テキストグラデーション */
            .text-gradient {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100" style="font-family: 'Inter', sans-serif;">
        <div class="min-h-screen flex">
            <!-- サイドバー -->
            <aside class="w-64 bg-white/90 backdrop-blur-xl border-r border-white/20 shadow-xl relative lg:block hidden" x-data="{ open: true }" id="sidebar">
                <!-- サイドバーヘッダー -->
                <div class="p-6 border-b border-white/20">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                        </div>
                        <h1 class="ml-3 text-xl font-bold text-gray-800">TellSupo</h1>
                    </div>
                </div>

                <!-- ナビゲーションメニュー -->
                <nav class="mt-6 px-4">
                    <div class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            ダッシュボード
                        </a>
                        
                        <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            顧客管理
                        </a>
                        
                        <a href="{{ route('call-logs.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('call-logs.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                            架電履歴
                        </a>
                        
                        <a href="{{ route('kpi-targets.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('kpi-targets.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            KPI管理
                        </a>
                        
                        <a href="{{ route('scripts.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('scripts.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            スクリプト管理
                        </a>
                    </div>
                </nav>

                <!-- ユーザー情報 -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/20">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">ログアウト</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- モバイル用サイドバートグル -->
            <div class="lg:hidden fixed top-4 left-4 z-50">
                <button @click="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="p-2 bg-white/90 backdrop-blur-xl rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- モバイル用サイドバー -->
            <aside class="lg:hidden fixed inset-y-0 left-0 w-64 bg-white/90 backdrop-blur-xl border-r border-white/20 shadow-xl z-40 hidden" id="mobile-sidebar">
                <div class="p-6 border-b border-white/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                            </div>
                            <h1 class="ml-3 text-xl font-bold text-gray-800">TellSupo</h1>
                        </div>
                        <button @click="document.getElementById('mobile-sidebar').classList.add('hidden')" class="p-1 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <nav class="mt-6 px-4">
                    <div class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                            </svg>
                            ダッシュボード
                        </a>
                        
                        <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            顧客管理
                        </a>
                        
                        <a href="{{ route('call-logs.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('call-logs.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                            架電履歴
                        </a>
                        
                        <a href="{{ route('kpi-targets.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('kpi-targets.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            KPI管理
                        </a>
                        
                        <a href="{{ route('scripts.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ request()->routeIs('scripts.*') ? 'bg-blue-100 text-blue-700' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            スクリプト管理
                        </a>
                    </div>
                </nav>

                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/20">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">ログアウト</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- メインコンテンツエリア -->
            <div class="flex-1 flex flex-col lg:ml-0">

                <!-- Page Heading -->
                @isset($header)
                    <header class="relative overflow-hidden">
                        <!-- 装飾的な背景要素 -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10"></div>
                        <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-pink-400/20 to-orange-400/20 rounded-full blur-3xl"></div>
                        
                        <div class="relative bg-white/80 backdrop-blur-xl border-b border-white/20 shadow-lg">
                            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="relative flex-1">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>