<x-app-layout>
    <!-- ページヘッダー -->
    <div class="page-header bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-gray-900">架電記録</h1>
                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">{{ $callLogs->total() }}件</span>
            </div>
            <div class="flex items-center gap-3">
                <button class="btn-outline text-sm">
                    <i class="fas fa-download mr-2"></i>レポート
                </button>
                <button id="create-call-log-btn" class="btn-primary text-sm">
                    <i class="fas fa-plus mr-2"></i>架電記録を作成
                </button>
            </div>
        </div>
    </div>

    <!-- コンテンツエリア -->
    <div class="flex-grow bg-white">
        <!-- タブナビゲーション -->
        <div class="tab-navigation border-b border-gray-200 px-6">
            <nav class="flex gap-8">
                <button class="tab-item active">
                    <span>すべての記録</span>
                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $callLogs->total() }}</span>
                </button>
                <button class="tab-item">
                    <span>今日</span>
                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs">0</span>
                </button>
                <button class="tab-item">
                    <span>今週</span>
                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs">0</span>
                </button>
                <button class="tab-item">
                    <span>完了</span>
                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs">0</span>
                </button>
            </nav>
        </div>

        <!-- フィルターバー -->
        <div class="filter-bar bg-gray-50 border-b border-gray-200 px-6 py-3">
            <div class="flex items-center justify-between">
                <div class="filter-items flex items-center gap-3">
                    <div class="filter-group flex items-center gap-1">
                        <label class="text-sm text-gray-600 font-medium whitespace-nowrap">結果:</label>
                        <select class="form-select text-sm w-28 pl-2 pr-6 py-1.5">
                            <option>すべて</option>
                            <option>接続済み</option>
                            <option>応答なし</option>
                            <option>話中</option>
                            <option>失敗</option>
                            <option>留守番電話</option>
                        </select>
                    </div>
                    <div class="filter-group flex items-center gap-1">
                        <label class="text-sm text-gray-600 font-medium whitespace-nowrap">期間:</label>
                        <select class="form-select text-sm w-32 pl-2 pr-6 py-1.5">
                            <option>すべて</option>
                            <option>今日</option>
                            <option>今週</option>
                            <option>今月</option>
                            <option>カスタム期間</option>
                        </select>
                    </div>
                    <div class="filter-group flex items-center gap-1">
                        <label class="text-sm text-gray-600 font-medium whitespace-nowrap">顧客:</label>
                        <select class="form-select text-sm w-24 pl-2 pr-6 py-1.5">
                            <option>すべて</option>
                        </select>
                    </div>
                    <button class="text-gray-500 text-sm hover:text-gray-700 px-2 py-1.5 rounded hover:bg-gray-200 transition-colors whitespace-nowrap">
                        <i class="fas fa-filter mr-1"></i>その他
                    </button>
                </div>
                <div class="filter-actions">
                    <button class="text-gray-500 text-sm hover:text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200 transition-colors whitespace-nowrap">
                        <i class="fas fa-redo mr-1"></i>リセット
                    </button>
                </div>
            </div>
        </div>

        <!-- 検索・アクションバー -->
        <div class="search-bar bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="search-container relative w-80">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                           placeholder="顧客名、メモで検索..."
                           class="form-input pl-10 text-sm"
                           name="search"
                           value="{{ request('search') }}">
                </div>
                <div class="table-actions flex items-center gap-3">
                    <button class="text-gray-500 hover:text-gray-700" title="並び替え">
                        <i class="fas fa-sort"></i>
                    </button>
                    <button class="text-gray-500 hover:text-gray-700" title="表示設定">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- データテーブル -->
        @if($callLogs->count() > 0)
            <div class="table-container">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="w-8">
                                <input type="checkbox" class="form-checkbox rounded text-green-600">
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>日時</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>顧客</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>時間</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>結果</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>メモ</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($callLogs as $callLog)
                            <tr class="hover:bg-gray-50 border-b border-gray-100 cursor-pointer" onclick="window.location.href='{{ route('call-logs.show', $callLog) }}'">
                                <td class="table-body" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="form-checkbox rounded text-green-600">
                                </td>
                                <td class="table-body">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-phone text-green-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $callLog->started_at->format('m/d H:i') }}</div>
                                            @if($callLog->ended_at)
                                                <div class="text-sm text-gray-500">終了: {{ $callLog->ended_at->format('H:i') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="table-body">
                                    <div class="text-gray-900 font-medium">{{ $callLog->customer->company_name }}</div>
                                    @if($callLog->customer->contact_name)
                                        <div class="text-sm text-gray-500">{{ $callLog->customer->contact_name }}</div>
                                    @endif
                                </td>
                                <td class="table-body">
                                    <div class="text-gray-900">{{ $callLog->formatted_duration }}</div>
                                </td>
                                <td class="table-body">
                                    <span class="badge
                                        @if($callLog->result === 'connected') badge-success
                                        @elseif($callLog->result === 'no_answer') badge-warning
                                        @elseif($callLog->result === 'busy') bg-orange-100 text-orange-800
                                        @elseif($callLog->result === 'failed') badge-danger
                                        @elseif($callLog->result === 'voicemail') badge-info
                                        @else badge-secondary @endif">
                                        {{ $callLog->result_label }}
                                    </span>
                                </td>
                                <td class="table-body">
                                    @if($callLog->notes)
                                        <div class="text-gray-900 truncate max-w-xs">{{ Str::limit($callLog->notes, 50) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="table-body" onclick="event.stopPropagation()">
                                    <div class="relative">
                                        <button class="action-menu-trigger text-gray-400 hover:text-gray-600 p-1"
                                                onclick="toggleActionMenu(this)">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="action-menu hidden absolute right-0 top-8 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-10 min-w-32">
                                            <a href="{{ route('call-logs.show', $callLog) }}"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye mr-2"></i>詳細
                                            </a>
                                            <a href="{{ route('call-logs.edit', $callLog) }}"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>編集
                                            </a>
                                            <form action="{{ route('call-logs.destroy', $callLog) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('本当に削除しますか？')"
                                                  class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                    <i class="fas fa-trash mr-2"></i>削除
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- ページネーション -->
            <div class="pagination-container bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="pagination-info text-sm text-gray-700">
                        {{ $callLogs->firstItem() ?? 0 }}〜{{ $callLogs->lastItem() ?? 0 }}件 / 全{{ $callLogs->total() }}件
                    </div>
                    <div class="pagination-controls">
                        {{ $callLogs->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        @else
            <!-- 空状態 -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <h3 class="empty-state-title">架電記録がまだ登録されていません</h3>
                <p class="empty-state-description">
                    最初の架電記録を追加して、架電履歴を管理しましょう
                </p>
                <button id="create-call-log-btn-2" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>架電記録を作成
                </button>
            </div>
        @endif
    </div>

    <!-- React架電記録作成モーダル -->
    <div id="call-log-modal-root"></div>
</x-app-layout>

<style>
/* タブナビゲーション */
.tab-item {
    @apply px-4 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 transition-all duration-200;
}

.tab-item.active {
    @apply text-green-600 border-green-500;
}

/* アクションメニュー */
.action-menu {
    min-width: 140px;
}

/* 検索入力フィールド */
.search-container input:focus {
    @apply ring-2 ring-green-500 border-green-500;
}

/* テーブル行ホバー効果 */
tbody tr:hover .action-menu-trigger {
    @apply text-gray-600;
}
</style>

@vite('resources/js/app.tsx')
