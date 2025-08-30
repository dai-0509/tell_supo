<nav x-data="{ open: false }" class="lg:hidden relative bg-white/80 backdrop-blur-xl border-b border-white/20 shadow-lg sticky top-0 z-50">
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
                    <a href="{{ route('kpi-targets.index') }}" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-300 hover:scale-105 {{ request()->routeIs('kpi-targets.*') ? 'bg-white/60 text-gray-900' : '' }}">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">🎯</span>
                        <span>KPI管理</span>
                    </a>
                    <a href="{{ route('scripts.index') }}" class="group flex items-center space-x-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-300 hover:scale-105 {{ request()->routeIs('scripts.*') ? 'bg-white/60 text-gray-900' : '' }}">
                        <span class="text-lg group-hover:rotate-12 transition-transform duration-300">📝</span>
                        <span>スクリプト</span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- リアルタイム検索バー -->
                <div class="relative" x-data="globalSearch()">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               x-model="searchQuery"
                               x-on:focus="showResults = true"
                               x-on:keydown.escape="showResults = false"
                               placeholder="顧客検索..." 
                               class="block w-64 pl-9 pr-3 py-2 border border-white/30 rounded-xl text-sm bg-white/60 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all duration-200">
                    </div>
                    
                    <!-- 検索結果ドロップダウン -->
                    <div x-show="showResults && searchQuery.length > 1" 
                         x-cloak
                         class="absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-64 overflow-y-auto">
                        <div x-show="isLoading" class="p-4 text-center text-gray-500">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mx-auto"></div>
                            <span class="ml-2">検索中...</span>
                        </div>
                        
                        <div x-show="!isLoading && results.length === 0" class="p-4 text-center text-gray-500">
                            検索結果がありません
                        </div>
                        
                        <template x-for="customer in results" :key="customer.id">
                            <a :href="`/customers/${customer.id}`" 
                               class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900" x-text="customer.company_name"></div>
                                        <div class="text-xs text-gray-500" x-text="customer.contact_name || 'No contact'"></div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span x-show="customer.temperature_rating" 
                                              :class="getTemperatureClass(customer.temperature_rating)"
                                              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                              x-text="customer.temperature_rating"></span>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
                
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

<script>
function globalSearch() {
    return {
        searchQuery: '',
        results: [],
        showResults: false,
        isLoading: false,
        searchTimer: null,
        
        init() {
            this.$watch('searchQuery', () => {
                if (this.searchQuery.length > 1) {
                    this.debounceSearch();
                } else {
                    this.results = [];
                    this.showResults = false;
                }
            });
            
            // 外部クリックで結果を非表示
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.showResults = false;
                }
            });
        },
        
        debounceSearch() {
            this.isLoading = true;
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.performSearch();
            }, 300);
        },
        
        async performSearch() {
            if (!this.searchQuery.trim()) {
                this.results = [];
                this.isLoading = false;
                return;
            }
            
            try {
                // 実際の検索API呼び出し（モックデータで代替）
                // const response = await fetch(`/api/customers/search?q=${encodeURIComponent(this.searchQuery)}`);
                // const data = await response.json();
                
                // モックデータ
                await new Promise(resolve => setTimeout(resolve, 200));
                
                const mockResults = [
                    {
                        id: 1,
                        company_name: 'サンプル株式会社',
                        contact_name: '田中太郎',
                        temperature_rating: 'A'
                    },
                    {
                        id: 2,
                        company_name: 'テスト商事',
                        contact_name: '佐藤花子',
                        temperature_rating: 'B'
                    },
                    {
                        id: 3,
                        company_name: 'デモ企業',
                        contact_name: '鈴木一郎',
                        temperature_rating: 'C'
                    }
                ].filter(customer => 
                    customer.company_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    customer.contact_name.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
                
                this.results = mockResults;
                this.showResults = true;
                
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
            } finally {
                this.isLoading = false;
            }
        },
        
        getTemperatureClass(rating) {
            const classes = {
                'A': 'bg-red-100 text-red-800',
                'B': 'bg-orange-100 text-orange-800',
                'C': 'bg-yellow-100 text-yellow-800',
                'D': 'bg-blue-100 text-blue-800',
                'E': 'bg-green-100 text-green-800',
                'F': 'bg-gray-100 text-gray-800'
            };
            return classes[rating] || 'bg-gray-100 text-gray-800';
        }
    }
}
</script>
