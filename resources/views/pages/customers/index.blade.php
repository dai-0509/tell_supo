<x-app-layout>
    <div class="customer-index-page">
        <!-- ページヘッダー -->
        <div class="page-header bg-white border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div>
                            <h1 class="page-title">顧客管理</h1>
                            <p class="text-gray-500 text-sm">{{ $customers->total() }}件の顧客</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="btn-secondary">
                            <i class="fas fa-download mr-2"></i>エクスポート
                        </button>
                        <button id="create-customer-btn" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>顧客を作成
                        </button>
                    </div>
                </div>
            </div>

            <!-- タブナビゲーション -->
            <nav class="tab-nav border-b border-gray-200 px-6">
                <button class="tab-item active">
                    <span>すべての顧客</span>
                    <span class="ml-2 bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">{{ $customers->total() }}</span>
                </button>
                <button class="tab-item">
                    <span>既存顧客</span>
                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs">0</span>
                </button>
            </nav>
        </div>

        <!-- フィルター機能 -->
        <form id="filterForm" method="GET" action="{{ route('customers.index') }}">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 space-y-3">
                <!-- 検索バー -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="relative w-80">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                   placeholder="会社名、担当者名で検索..." 
                                   class="form-input pl-10 text-sm"
                                   name="search"
                                   value="{{ request('search') }}"
                                   onchange="document.getElementById('filterForm').submit()">
                        </div>
                    </div>
                    <!-- リセットボタン -->
                    <div>
                        <a href="{{ route('customers.index') }}" class="text-gray-500 text-sm hover:text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200 transition-colors">
                            <i class="fas fa-redo mr-1"></i>リセット
                        </a>
                    </div>
                </div>

                <!-- ソート機能 -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">並び替え:</label>
                    <div class="flex gap-2">
                        <select name="sort" class="form-select text-sm w-32" onchange="document.getElementById('filterForm').submit()">
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>日付</option>
                            <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>最終更新</option>
                        </select>
                        <select name="direction" class="form-select text-sm w-20" onchange="document.getElementById('filterForm').submit()">
                            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>降順</option>
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>昇順</option>
                        </select>
                    </div>
                </div>

                <!-- 温度感フィルター -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">温度感:</label>
                    @php
                        $temperatures = ['A', 'B', 'C', 'D', 'E', 'F'];
                        $selectedTemperatures = request('temperatures', []);
                    @endphp
                    <div class="flex gap-1 flex-wrap">
                        @foreach($temperatures as $temperature)
                        <label class="filter-checkbox-label flex items-center space-x-1 bg-white border rounded px-2 py-1 hover:bg-gray-50 cursor-pointer text-xs {{ in_array($temperature, $selectedTemperatures) ? 'bg-orange-50 border-orange-300' : '' }}">
                            <input type="checkbox" 
                                   name="temperatures[]" 
                                   value="{{ $temperature }}"
                                   class="form-checkbox rounded text-orange-600 w-3 h-3"
                                   {{ in_array($temperature, $selectedTemperatures) ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()">
                            <span class="text-gray-700">{{ $temperature }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- ステータスフィルター -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">ステータス:</label>
                    @php
                        $statuses = ['受けブロ', '会話のみ', '見込みあり', '競合サービス利用中', '過去取引あり', '取引中', '架電禁止'];
                        $selectedStatuses = request('statuses', []);
                    @endphp
                    <div class="flex gap-1 flex-wrap">
                        @foreach($statuses as $status)
                        <label class="filter-checkbox-label flex items-center space-x-1 bg-white border rounded px-2 py-1 hover:bg-gray-50 cursor-pointer text-xs {{ in_array($status, $selectedStatuses) ? 'bg-blue-50 border-blue-300' : '' }}">
                            <input type="checkbox" 
                                   name="statuses[]" 
                                   value="{{ $status }}"
                                   class="form-checkbox rounded text-blue-600 w-3 h-3"
                                   {{ in_array($status, $selectedStatuses) ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()">
                            <span class="text-gray-700">{{ $status }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- 業界フィルター -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">業界:</label>
                    @php
                        $industries = ['IT', '製造業', '小売業', '金融業', '医療・福祉', '教育'];
                        $selectedIndustries = request('industries', []);
                    @endphp
                    <div class="flex gap-1 flex-wrap">
                        @foreach($industries as $industry)
                        <label class="filter-checkbox-label flex items-center space-x-1 bg-white border rounded px-2 py-1 hover:bg-gray-50 cursor-pointer text-xs {{ in_array($industry, $selectedIndustries) ? 'bg-blue-50 border-blue-300' : '' }}">
                            <input type="checkbox" 
                                   name="industries[]" 
                                   value="{{ $industry }}"
                                   class="form-checkbox rounded text-blue-600 w-3 h-3"
                                   {{ in_array($industry, $selectedIndustries) ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()">
                            <span class="text-gray-700">{{ $industry }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- エリアフィルター -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium whitespace-nowrap w-16">エリア:</label>
                    @php
                        $areas = ['北海道', '東北', '関東', '中部', '関西', '中国'];
                        $selectedAreas = request('areas', []);
                    @endphp
                    <div class="flex gap-1 flex-wrap">
                        @foreach($areas as $area)
                        <label class="filter-checkbox-label flex items-center space-x-1 bg-white border rounded px-2 py-1 hover:bg-gray-50 cursor-pointer text-xs {{ in_array($area, $selectedAreas) ? 'bg-green-50 border-green-300' : '' }}">
                            <input type="checkbox" 
                                   name="areas[]" 
                                   value="{{ $area }}"
                                   class="form-checkbox rounded text-green-600 w-3 h-3"
                                   {{ in_array($area, $selectedAreas) ? 'checked' : '' }}
                                   onchange="document.getElementById('filterForm').submit()">
                            <span class="text-gray-700">{{ $area }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>

        <!-- データテーブル -->
        @if($customers->count() > 0)
            <div class="table-container">
                <table class="w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="w-8">
                                <input type="checkbox" class="form-checkbox rounded text-green-600">
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>会社名</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>担当者</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>電話番号</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>温度感</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>ステータス</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="text-left cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-2">
                                    <span>最終更新</span>
                                    <i class="fas fa-sort text-gray-400"></i>
                                </div>
                            </th>
                            <th class="w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50 border-b border-gray-100 cursor-pointer" onclick="window.location.href='{{ route('customers.show', $customer) }}'">
                                <td class="table-body" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="form-checkbox rounded text-green-600">
                                </td>
                                <td class="table-body">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-building text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $customer->company_name }}</div>
                                            @if($customer->email)
                                                <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="table-body">
                                    <div class="text-gray-900">{{ $customer->contact_name ?? '-' }}</div>
                                </td>
                                <td class="table-body">
                                    <div class="text-gray-900">{{ $customer->phone ?? '-' }}</div>
                                </td>
                                <td class="table-body">
                                    @if($customer->temperature_rating)
                                        <span class="badge
                                            @if($customer->temperature_rating === 'A') badge-danger
                                            @elseif($customer->temperature_rating === 'B') bg-red-100 text-red-800
                                            @elseif($customer->temperature_rating === 'C') bg-orange-100 text-orange-800
                                            @elseif($customer->temperature_rating === 'D') bg-yellow-100 text-yellow-800
                                            @elseif($customer->temperature_rating === 'E') bg-green-100 text-green-800
                                            @elseif($customer->temperature_rating === 'F') badge-secondary
                                            @else badge-secondary @endif">
                                            {{ $customer->temperature_rating }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="table-body">
                                    <span class="badge flex items-center gap-1
                                        @if($customer->status === '受けブロ') bg-gray-100 text-gray-800
                                        @elseif($customer->status === '会話のみ') bg-blue-100 text-blue-800
                                        @elseif($customer->status === '見込みあり') bg-yellow-100 text-yellow-800
                                        @elseif($customer->status === '競合サービス利用中') bg-purple-100 text-purple-800
                                        @elseif($customer->status === '過去取引あり') bg-indigo-100 text-indigo-800
                                        @elseif($customer->status === '取引中') bg-green-100 text-green-800
                                        @elseif($customer->status === '架電禁止') bg-red-100 text-red-800
                                        @else badge-secondary @endif">
                                        @if($customer->status === '受けブロ')
                                            <i class="fas fa-ban text-xs"></i>
                                        @elseif($customer->status === '会話のみ')
                                            <i class="fas fa-comments text-xs"></i>
                                        @elseif($customer->status === '見込みあり')
                                            <i class="fas fa-star text-xs"></i>
                                        @elseif($customer->status === '競合サービス利用中')
                                            <i class="fas fa-exchange-alt text-xs"></i>
                                        @elseif($customer->status === '過去取引あり')
                                            <i class="fas fa-history text-xs"></i>
                                        @elseif($customer->status === '取引中')
                                            <i class="fas fa-handshake text-xs"></i>
                                        @elseif($customer->status === '架電禁止')
                                            <i class="fas fa-phone-slash text-xs"></i>
                                        @endif
                                        {{ $customer->status }}
                                    </span>
                                </td>
                                <td class="table-body">
                                    <div class="text-sm text-gray-500">
                                        {{ $customer->updated_at->format('Y/m/d') }}
                                    </div>
                                </td>
                                <td class="table-body" onclick="event.stopPropagation()">
                                    <div class="relative">
                                        <button class="action-menu-trigger text-gray-400 hover:text-gray-600 p-1" 
                                                onclick="toggleActionMenu(this)">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="action-menu hidden">
                                            <a href="{{ route('customers.show', $customer) }}" class="action-menu-item">
                                                <i class="fas fa-eye mr-2"></i>詳細
                                            </a>
                                            <a href="{{ route('customers.edit', $customer) }}" class="action-menu-item">
                                                <i class="fas fa-edit mr-2"></i>編集
                                            </a>
                                            <button class="action-menu-item text-red-600" onclick="deleteCustomer({{ $customer->id }})">
                                                <i class="fas fa-trash mr-2"></i>削除
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- ページネーション -->
                @if($customers->hasPages())
                    <div class="pagination-container">
                        {{ $customers->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        @else
            <!-- 空状態 -->
            @php
                $hasFilters = request()->hasAny(['search', 'statuses', 'temperatures', 'industries', 'areas']);
            @endphp
            @if($hasFilters)
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="empty-state-title">条件に一致する顧客が見つかりません</h3>
                    <p class="empty-state-description">
                        検索条件やフィルターを変更してお試しください
                    </p>
                    <a href="{{ route('customers.index') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>フィルタをリセット
                    </a>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="empty-state-title">顧客がまだ登録されていません</h3>
                    <p class="empty-state-description">
                        最初の顧客を追加して、顧客管理を始めましょう
                    </p>
                    <button id="create-customer-btn-2" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>顧客を作成
                    </button>
                </div>
            @endif
        @endif
    </div>

    <!-- React顧客作成モーダル -->
    <div id="customer-modal-root"></div>
</x-app-layout>

<style>
/* カスタムスタイル */
.customer-index-page {
    @apply min-h-screen bg-gray-50;
}

.page-header {
    @apply bg-white border-b border-gray-200;
}

.page-title {
    @apply text-2xl font-semibold text-gray-900;
}

.tab-nav {
    @apply flex space-x-8;
}

.tab-item {
    @apply py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center transition-colors;
}

.tab-item.active {
    @apply border-green-500 text-green-600;
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

.action-menu {
    @apply absolute right-0 top-8 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10;
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

<script>
function toggleActionMenu(trigger) {
    const menu = trigger.nextElementSibling;
    const allMenus = document.querySelectorAll('.action-menu');
    
    // 他のメニューを閉じる
    allMenus.forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });
    
    // クリックされたメニューをトグル
    menu.classList.toggle('hidden');
    
    // 外部クリックで閉じる
    if (!menu.classList.contains('hidden')) {
        setTimeout(() => {
            document.addEventListener('click', function closeMenu(e) {
                if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', closeMenu);
                }
            });
        }, 0);
    }
}

function deleteCustomer(customerId) {
    if (confirm('この顧客を削除してもよろしいですか？')) {
        // 削除処理をここに実装
        console.log('Delete customer:', customerId);
    }
}
</script>

@vite('resources/js/app.tsx')