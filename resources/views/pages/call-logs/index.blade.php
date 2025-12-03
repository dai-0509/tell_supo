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

        <!-- フィルター機能 -->
        <form id="filterForm" method="GET" action="{{ route('call-logs.index') }}">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 space-y-3">
                <!-- 検索バー -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="relative w-80">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   placeholder="顧客名、メモで検索..." 
                                   class="form-input pl-10 text-sm"
                                   name="search"
                                   value="{{ request('search') }}"
                                   onchange="document.getElementById('filterForm').submit()">
                        </div>
                    </div>
                    <!-- リセットボタン -->
                    <div>
                        <a href="{{ route('call-logs.index') }}" class="text-gray-500 text-sm hover:text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200 transition-colors">
                            <i class="fas fa-redo mr-1"></i>リセット
                        </a>
                    </div>
                </div>

                <!-- 日付フィルター -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">期間:</label>
                    <div class="flex gap-1 flex-wrap">
                        @php
                            $dateFilters = [
                                '' => 'すべて',
                                'today' => '今日',
                                'yesterday' => '昨日',
                                'this_week' => '今週',
                                'this_month' => '今月'
                            ];
                            $selectedDate = request('date_filter', '');
                        @endphp
                        @foreach($dateFilters as $value => $label)
                        <label class="filter-checkbox-label flex items-center space-x-1 bg-white border rounded px-2 py-1 hover:bg-gray-50 cursor-pointer text-xs {{ $selectedDate === $value ? 'bg-blue-50 border-blue-300' : '' }}">
                            <input type="radio" 
                                   name="date_filter" 
                                   value="{{ $value }}"
                                   class="form-radio rounded text-blue-600 w-3 h-3"
                                   {{ $selectedDate === $value ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()">
                            <span class="text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- 結果フィルター -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">結果:</label>
                    @php
                        $results = [
                            '通話成功' => '通話成功',
                            '受けブロ' => '受けブロ',
                            '会話のみ' => '会話のみ',
                            '見込みあり' => '見込みあり'
                        ];
                        $selectedResults = request('results', []);
                    @endphp
                    <div class="flex gap-1 flex-wrap">
                        @foreach($results as $value => $label)
                        <label class="filter-checkbox-label flex items-center space-x-1 bg-white border rounded px-2 py-1 hover:bg-gray-50 cursor-pointer text-xs {{ in_array($value, $selectedResults) ? 'bg-green-50 border-green-300' : '' }}">
                            <input type="checkbox" 
                                   name="results[]" 
                                   value="{{ $value }}"
                                   class="form-checkbox rounded text-green-600 w-3 h-3"
                                   {{ in_array($value, $selectedResults) ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()">
                            <span class="text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>

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
                                        @if($callLog->result === '通話成功') bg-green-100 text-green-800
                                        @elseif($callLog->result === '受けブロ') bg-red-100 text-red-800
                                        @elseif($callLog->result === '会話のみ') bg-blue-100 text-blue-800
                                        @elseif($callLog->result === '見込みあり') bg-yellow-100 text-yellow-800
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
            @php
                $hasFilters = request()->hasAny(['search', 'date_filter', 'results']);
            @endphp
            @if($hasFilters)
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="empty-state-title">条件に一致する架電記録が見つかりません</h3>
                    <p class="empty-state-description">
                        検索条件やフィルターを変更してお試しください
                    </p>
                    <a href="{{ route('call-logs.index') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>フィルタをリセット
                    </a>
                </div>
            @else
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
        @endif
    </div>

    <!-- React架電記録作成モーダル -->
    <div id="call-log-modal-root"></div>
</x-app-layout>

<style>
/* カスタムスタイル */
.call-logs-index-page {
    @apply min-h-screen bg-gray-50;
}

.page-header {
    @apply bg-white border-b border-gray-200;
}

/* タブナビゲーション */
.tab-item {
    @apply px-4 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 transition-all duration-200;
}

.tab-item.active {
    @apply text-green-600 border-green-500;
}

.table-container {
    @apply bg-white;
}

.table-header {
    @apply bg-gray-50 border-b border-gray-200;
}

.table-header th {
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
}

.table-body {
    @apply px-6 py-4;
}

.badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.badge-danger {
    @apply bg-red-100 text-red-800;
}

.badge-warning {
    @apply bg-yellow-100 text-yellow-800;
}

.badge-success {
    @apply bg-green-100 text-green-800;
}

.badge-info {
    @apply bg-blue-100 text-blue-800;
}

.badge-secondary {
    @apply bg-gray-100 text-gray-800;
}

.empty-state {
    @apply text-center py-12 px-6;
}

.empty-state-icon {
    @apply mx-auto w-12 h-12 text-gray-400 text-4xl mb-4;
}

.empty-state-title {
    @apply text-lg font-medium text-gray-900 mb-2;
}

.empty-state-description {
    @apply text-gray-500 mb-6 max-w-md mx-auto;
}

.btn-primary {
    @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center;
}

.btn-secondary {
    @apply bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center;
}

.form-input {
    @apply block w-full rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 sm:text-sm;
}

.form-select {
    @apply block rounded-lg border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-green-500 sm:text-sm;
}

.form-checkbox {
    @apply h-4 w-4 rounded border-gray-300 focus:ring-green-500 focus:ring-2;
}

.form-radio {
    @apply h-4 w-4 border-gray-300 focus:ring-green-500 focus:ring-2;
}

.action-menu {
    @apply absolute right-0 top-8 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10;
    min-width: 140px;
}

.action-menu-item {
    @apply block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors;
}

.pagination-container {
    @apply px-6 py-4 border-t border-gray-200;
}

/* フォーカス時のスタイル */
.search-container input:focus {
    @apply ring-2 ring-green-500 border-green-500;
}

/* テーブル行ホバー効果 */
tbody tr:hover .action-menu-trigger {
    @apply text-gray-600;
}
</style>

@vite('resources/js/app.tsx')
