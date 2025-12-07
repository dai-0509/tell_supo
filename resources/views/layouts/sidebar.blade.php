<aside class="sidebar w-60 bg-slate-800 text-white flex flex-col flex-shrink-0 border-r border-slate-600">
    <!-- ロゴエリア -->
    <div class="logo-area p-4 h-16 flex items-center border-b border-slate-600">
        <div class="flex items-center gap-3">
            <i class="fas fa-phone-alt text-green-500 text-xl"></i>
            <span class="text-lg font-bold">{{ config('app.name', 'TellSupo') }}</span>
            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded">beta</span>
        </div>
    </div>

    <!-- ナビゲーション -->
    <nav class="sidebar-nav flex-grow overflow-y-auto pt-2">
        <ul class="list-none">
            <!-- ダッシュボード -->
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-5 py-3 text-gray-300 text-sm hover:bg-slate-700 hover:text-white transition-all duration-200 gap-3 font-medium border-l-3 {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white border-blue-400' : 'border-transparent' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span>ダッシュボード</span>
                </a>
            </li>

            <!-- 顧客管理 -->
            <li>
                <a href="{{ route('customers.index') }}"
                   class="flex items-center px-5 py-3 text-gray-300 text-sm hover:bg-slate-700 hover:text-white transition-all duration-200 gap-3 font-medium border-l-3 {{ request()->routeIs('customers.*') ? 'bg-slate-700 text-white border-blue-400' : 'border-transparent' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span>顧客管理</span>
                </a>
            </li>

            <!-- 架電記録 -->
            <li>
                <a href="{{ route('call-logs.index') }}"
                   class="flex items-center px-5 py-3 text-gray-300 text-sm hover:bg-slate-700 hover:text-white transition-all duration-200 gap-3 font-medium border-l-3 {{ request()->routeIs('call-logs.*') ? 'bg-slate-700 text-white border-blue-400' : 'border-transparent' }}">
                    <i class="fas fa-phone w-5 text-center"></i>
                    <span>架電記録</span>
                </a>
            </li>

            <!-- KPI管理 -->
            <li>
                <a href="{{ route('kpi-targets.index') }}"
                   class="flex items-center px-5 py-3 text-gray-300 text-sm hover:bg-slate-700 hover:text-white transition-all duration-200 gap-3 font-medium border-l-3 {{ request()->routeIs('kpis.*') ? 'bg-slate-700 text-white border-blue-400' : 'border-transparent' }}">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span>KPI管理</span>
                </a>
            </li>
        </ul>

        <!-- 区切り線 -->
        <div class="h-px bg-slate-600 mx-5 my-2"></div>

        <!-- 設定・その他 -->
        <ul class="list-none">
            <li>
                <a href="#"
                   class="flex items-center px-5 py-3 text-gray-300 text-sm hover:bg-slate-700 hover:text-white transition-all duration-200 gap-3 font-medium border-l-3 border-transparent">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span>設定</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- サイドバーフッター -->
    <div class="sidebar-footer p-4 border-t border-slate-600">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-300">
                <div class="w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center text-xs text-slate-800">
                    <i class="fas fa-user"></i>
                </div>
                <span>{{ Auth::user()->name ?? 'ユーザー' }}</span>
            </div>
            <button class="w-6 h-6 border border-slate-500 text-gray-300 rounded cursor-pointer flex items-center justify-center hover:bg-slate-700">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
        </div>
    </div>
</aside>

<style>
.sidebar-nav li a.border-blue-400 {
    border-left: 3px solid #60a5fa;
}
</style>
