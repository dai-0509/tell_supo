<!-- グローバルヘッダー -->
<header class="global-header bg-slate-700 h-14 flex items-center justify-between px-6 text-white border-b border-slate-600">
    <!-- 左側: 検索 -->
    <div class="search-container relative w-80">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" 
               placeholder="TellSupoを検索" 
               class="w-full bg-slate-600 border border-transparent rounded-md px-9 py-2 text-white text-sm outline-none focus:bg-white focus:text-gray-900 placeholder-gray-400 focus:placeholder-gray-500">
        <div class="keyboard-shortcut absolute right-2 top-1/2 transform -translate-y-1/2 border border-gray-400 rounded px-1 text-xs text-gray-400 pointer-events-none">
            <i class="fab fa-apple mr-1"></i>K
        </div>
    </div>

    <!-- 右側: アクション -->
    <div class="header-actions flex items-center gap-5">
        <!-- プラスボタン（クイックアクション） -->
        <button class="plus-btn w-8 h-8 bg-slate-600 rounded-full text-white flex items-center justify-center hover:bg-slate-500">
            <i class="fas fa-plus"></i>
        </button>

        <!-- アイコングループ -->
        <div class="icon-group flex gap-4 pr-5 border-r border-slate-600">
            <!-- 電話 -->
            <button class="icon-btn text-gray-300 hover:text-white text-lg">
                <i class="fas fa-phone"></i>
            </button>
            <!-- ヘルプ -->
            <button class="icon-btn text-gray-300 hover:text-white text-lg">
                <i class="far fa-question-circle"></i>
            </button>
            <!-- 設定 -->
            <button class="icon-btn text-gray-300 hover:text-white text-lg">
                <i class="fas fa-cog"></i>
            </button>
            <!-- 通知 -->
            <button class="icon-btn text-gray-300 hover:text-white text-lg relative">
                <i class="fas fa-bell"></i>
                @if(true) {{-- 通知があることを想定 --}}
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs h-4 w-4 rounded-full flex items-center justify-center border-2 border-slate-700 font-bold">
                        2
                    </span>
                @endif
            </button>
        </div>

        <!-- AIアシスタント -->
        <div class="copilot-link flex items-center gap-2 text-gray-300 text-sm cursor-pointer hover:text-white">
            <i class="fas fa-magic"></i>
            <span>AI アシスト</span>
        </div>

        <!-- ユーザープロフィール -->
        <div class="user-profile flex items-center gap-2 text-gray-300 cursor-pointer hover:text-white text-sm">
            <div class="avatar w-6 h-6 bg-gray-400 rounded-full flex items-center justify-center text-slate-800 text-xs">
                <i class="fas fa-user"></i>
            </div>
            <span>{{ Auth::user()->name ?? 'ユーザー' }}</span>
            <i class="fas fa-caret-down"></i>
        </div>
    </div>
</header>

<style>
/* 検索フォーカス時のスタイル調整 */
.search-container input:focus ~ .search-icon {
    color: #374151;
}

.search-container input:focus ~ .keyboard-shortcut {
    color: #6b7280;
    border-color: #9ca3af;
}
</style>