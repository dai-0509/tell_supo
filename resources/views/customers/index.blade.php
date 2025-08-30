@extends('layouts.app')

@section('content')
<div class="p-6" x-data="customerFilter()">
    <!-- ヘッダー -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">顧客管理</h1>
                <p class="text-gray-600 mt-1">顧客情報の管理と戦略的分析</p>
            </div>
            <a href="{{ route('customers.create') }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>新規顧客登録</span>
            </a>
        </div>
    </div>

    <!-- フィルターバー -->
    <div class="bg-white/80 backdrop-blur-xl rounded-xl p-6 mb-6 border border-white/20 shadow-lg">
        <form method="GET" action="{{ route('customers.index') }}" class="space-y-4">
            <!-- 検索バー -->
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">会社名・担当者名で検索</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="検索キーワードを入力..."
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div class="lg:w-48">
                    <label for="temperature_rating" class="block text-sm font-medium text-gray-700 mb-2">温度感</label>
                    <select name="temperature_rating" 
                            id="temperature_rating"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">全て</option>
                        <option value="A" {{ request('temperature_rating') == 'A' ? 'selected' : '' }}>A（最高）</option>
                        <option value="B" {{ request('temperature_rating') == 'B' ? 'selected' : '' }}>B（高）</option>
                        <option value="C" {{ request('temperature_rating') == 'C' ? 'selected' : '' }}>C（中）</option>
                        <option value="D" {{ request('temperature_rating') == 'D' ? 'selected' : '' }}>D（低）</option>
                        <option value="E" {{ request('temperature_rating') == 'E' ? 'selected' : '' }}>E（最低）</option>
                        <option value="F" {{ request('temperature_rating') == 'F' ? 'selected' : '' }}>F（要検討）</option>
                    </select>
                </div>
                
                <div class="lg:w-48">
                    <label for="area" class="block text-sm font-medium text-gray-700 mb-2">エリア</label>
                    <input type="text" 
                           name="area" 
                           id="area"
                           value="{{ request('area') }}"
                           placeholder="東京、大阪など"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div class="lg:w-48">
                    <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">業界</label>
                    <input type="text" 
                           name="industry" 
                           id="industry"
                           value="{{ request('industry') }}"
                           placeholder="IT、製造業など"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- ボタン群 -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>検索</span>
                    </button>
                    
                    <a href="{{ route('customers.index') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200">
                        リセット
                    </a>
                </div>
                
                <!-- 結果表示 -->
                <div class="text-sm text-gray-600">
                    全{{ $customers->total() }}件中 {{ $customers->firstItem() }}-{{ $customers->lastItem() }}件を表示
                </div>
            </div>
        </form>
    </div>

    <!-- 顧客一覧 -->
    <div class="bg-white/80 backdrop-blur-xl rounded-xl border border-white/20 shadow-lg overflow-hidden">
        @if($customers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/80 backdrop-blur-xl">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">会社名・担当者</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">温度感</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">エリア・業界</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">優先度</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最終更新</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->company_name }}</div>
                                        @if($customer->contact_name)
                                            <div class="text-sm text-gray-500">{{ $customer->contact_name }}</div>
                                        @endif
                                        @if($customer->email)
                                            <div class="text-xs text-gray-400">{{ $customer->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($customer->temperature_rating)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $customer->temperature_rating == 'A' ? 'bg-red-100 text-red-800' : 
                                               ($customer->temperature_rating == 'B' ? 'bg-orange-100 text-orange-800' : 
                                               ($customer->temperature_rating == 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($customer->temperature_rating == 'D' ? 'bg-blue-100 text-blue-800' : 
                                               ($customer->temperature_rating == 'E' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))) }}">
                                            {{ $customer->temperature_rating }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">未設定</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($customer->area)
                                            <span class="block">{{ $customer->area }}</span>
                                        @endif
                                        @if($customer->industry)
                                            <span class="block text-gray-500 text-xs">{{ $customer->industry }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $customer->status == 'new' ? 'bg-blue-100 text-blue-800' : 
                                           ($customer->status == 'contacted' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($customer->status == 'interested' ? 'bg-green-100 text-green-800' : 
                                           ($customer->status == 'not_interested' ? 'bg-red-100 text-red-800' : 
                                           ($customer->status == 'callback_scheduled' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')))) }}">
                                        {{ match($customer->status) {
                                            'new' => '新規',
                                            'contacted' => '連絡済',
                                            'interested' => '興味あり',
                                            'not_interested' => '興味なし',
                                            'callback_scheduled' => 'コールバック予定',
                                            'closed' => 'クローズ',
                                            default => $customer->status
                                        } }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $customer->priority == 'high' ? 'bg-red-100 text-red-800' : 
                                           ($customer->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ match($customer->priority) {
                                            'high' => '高',
                                            'medium' => '中',
                                            'low' => '低',
                                            default => $customer->priority
                                        } }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $customer->updated_at->format('m/d') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('customers.show', $customer) }}" 
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" 
                                           class="text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('この顧客を削除してもよろしいですか？')"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- ページネーション -->
            <div class="px-6 py-4 bg-gray-50/80 backdrop-blur-xl">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">顧客が見つかりませんでした</h3>
                <p class="mt-1 text-sm text-gray-500">検索条件を変更するか、新しい顧客を登録してください。</p>
                <div class="mt-6">
                    <a href="{{ route('customers.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        新規顧客登録
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function customerFilter() {
    return {
        // リアルタイムフィルタリング用のデータ
        searchTerm: '{{ request('search') }}',
        selectedTemperatureRating: '{{ request('temperature_rating') }}',
        selectedArea: '{{ request('area') }}',
        selectedIndustry: '{{ request('industry') }}',
        
        // デバウンス用のタイマー
        searchTimer: null,
        
        init() {
            // 検索フィールドのリアルタイム更新
            this.$watch('searchTerm', () => {
                this.debounceSearch();
            });
            
            this.$watch('selectedTemperatureRating', () => {
                this.updateFilters();
            });
            
            this.$watch('selectedArea', () => {
                this.debounceSearch();
            });
            
            this.$watch('selectedIndustry', () => {
                this.debounceSearch();
            });
        },
        
        // デバウンス検索（500ms遅延）
        debounceSearch() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.updateFilters();
            }, 500);
        },
        
        // フィルター条件をURLパラメータに反映
        updateFilters() {
            const params = new URLSearchParams();
            
            if (this.searchTerm && this.searchTerm.trim()) {
                params.append('search', this.searchTerm.trim());
            }
            
            if (this.selectedTemperatureRating) {
                params.append('temperature_rating', this.selectedTemperatureRating);
            }
            
            if (this.selectedArea && this.selectedArea.trim()) {
                params.append('area', this.selectedArea.trim());
            }
            
            if (this.selectedIndustry && this.selectedIndustry.trim()) {
                params.append('industry', this.selectedIndustry.trim());
            }
            
            // URLを更新（ページ遷移なし）
            const newUrl = '{{ route('customers.index') }}' + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);
        },
        
        // フィルターをクリア
        clearFilters() {
            this.searchTerm = '';
            this.selectedTemperatureRating = '';
            this.selectedArea = '';
            this.selectedIndustry = '';
            
            // フォームをクリアして送信
            document.getElementById('search').value = '';
            document.getElementById('temperature_rating').value = '';
            document.getElementById('area').value = '';
            document.getElementById('industry').value = '';
            
            window.location.href = '{{ route('customers.index') }}';
        },
        
        // 温度感の色クラスを取得
        getTemperatureRatingClass(rating) {
            const classes = {
                'A': 'bg-red-100 text-red-800',
                'B': 'bg-orange-100 text-orange-800',
                'C': 'bg-yellow-100 text-yellow-800',
                'D': 'bg-blue-100 text-blue-800',
                'E': 'bg-green-100 text-green-800',
                'F': 'bg-gray-100 text-gray-800'
            };
            return classes[rating] || 'bg-gray-100 text-gray-800';
        },
        
        // ステータスの色クラスを取得
        getStatusClass(status) {
            const classes = {
                'new': 'bg-blue-100 text-blue-800',
                'contacted': 'bg-yellow-100 text-yellow-800',
                'interested': 'bg-green-100 text-green-800',
                'not_interested': 'bg-red-100 text-red-800',
                'callback_scheduled': 'bg-purple-100 text-purple-800',
                'closed': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        // 優先度の色クラスを取得
        getPriorityClass(priority) {
            const classes = {
                'high': 'bg-red-100 text-red-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'low': 'bg-green-100 text-green-800'
            };
            return classes[priority] || 'bg-gray-100 text-gray-800';
        }
    }
}
</script>
@endsection