<nav x-data="{ open: false }" class="relative bg-white/80 backdrop-blur-xl border-b border-white/20 shadow-lg sticky top-0 z-50">
    <!-- 装飾的な背景要素 -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5"></div>
    
    <!-- Primary Navigation Menu -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="group flex items-center space-x-4 hover:opacity-80 transition-all duration-300 hover:scale-105">
                        <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:rotate-12">
                            T
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">TellSupo</h1>
                            <p class="text-xs text-gray-500 font-medium">テレアポ支援</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:scale-105">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">📊</span>
                        <span>ダッシュボード</span>
                    </x-nav-link>
                    <a href="{{ route('customers.index') }}" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-300 hover:scale-105 {{ request()->routeIs('customers.*') ? 'bg-white/60 text-gray-900' : '' }}">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">👥</span>
                        <span>顧客管理</span>
                    </a>
                    <a href="{{ route('call-logs.index') }}" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-300 hover:scale-105 {{ request()->routeIs('call-logs.*') ? 'bg-white/60 text-gray-900' : '' }}">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">📞</span>
                        <span>架電履歴</span>
                    </a>
                    <a href="{{ route('kpi.index') }}" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-300 hover:scale-105 {{ request()->routeIs('kpi.*') ? 'bg-white/60 text-gray-900' : '' }}">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">🎯</span>
                        <span>KPI管理</span>
                    </a>
                    <a href="#" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-300 hover:scale-105">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">📝</span>
                        <span>スクリプト</span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Quick Actions -->
                <button class="group p-3 text-gray-400 hover:text-gray-600 hover:bg-white/60 rounded-xl transition-all duration-300 hover:scale-110 hover:rotate-12">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </button>

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="group flex items-center space-x-3 px-4 py-3 border border-white/30 text-sm leading-4 font-medium rounded-2xl text-gray-700 bg-white/60 hover:bg-white/80 focus:outline-none transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 backdrop-blur-sm">
                            <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center text-white text-sm font-bold group-hover:rotate-12 transition-transform duration-300">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="font-medium">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">営業担当者</div>
                            </div>
                            <svg class="fill-current h-4 w-4 group-hover:rotate-180 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-purple-50">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center space-x-2 group hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>プロフィール</span>
                        </x-dropdown-link>

                        <x-dropdown-link href="#" class="flex items-center space-x-2 group hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>設定</span>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center space-x-2 text-red-600 hover:text-red-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 group">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>ログアウト</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="group inline-flex items-center justify-center p-3 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-white/60 focus:outline-none focus:bg-white/60 focus:text-gray-500 transition-all duration-300 hover:scale-110">
                    <svg class="h-6 w-6 group-hover:rotate-90 transition-transform duration-300" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 backdrop-blur-xl border-t border-white/20">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
