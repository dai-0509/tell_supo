@extends('layouts.app')

@section('content')
<div class="p-6" x-data="{
    result: '{{ request('result') }}',
    dateFrom: '{{ request('date_from') }}',
    dateTo: '{{ request('date_to') }}',
    customerId: '{{ request('customer_id') }}',
    search: '{{ request('search') }}',
    
    filterUrl() {
        const params = new URLSearchParams();
        if (this.result) params.append('result', this.result);
        if (this.dateFrom) params.append('date_from', this.dateFrom);
        if (this.dateTo) params.append('date_to', this.dateTo);
        if (this.customerId) params.append('customer_id', this.customerId);
        if (this.search) params.append('search', this.search);
        
        return '{{ route('call-logs.index') }}' + (params.toString() ? '?' + params.toString() : '');
    },
    
    clearFilters() {
        this.result = '';
        this.dateFrom = '';
        this.dateTo = '';
        this.customerId = '';
        this.search = '';
        window.location.href = '{{ route('call-logs.index') }}';
    }
}">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">架電履歴</h1>
                <p class="text-gray-600 mt-1">顧客への架電記録の管理</p>
            </div>
            <a href="{{ route('call-logs.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                新規架電記録
            </a>
        </div>
    </div>

    <!-- フィルター -->
    <div class="bg-white/90 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 検索 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">検索</label>
                <input type="text" x-model="search" 
                       placeholder="会社名・担当者名で検索"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- 結果フィルター -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">結果</label>
                <select x-model="result" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">全て</option>
                    <option value="success">成功</option>
                    <option value="no_answer">不在</option>
                    <option value="busy">話し中</option>
                    <option value="appointment">アポ取得</option>
                    <option value="callback">折り返し</option>
                    <option value="not_interested">関心なし</option>
                    <option value="invalid_number">番号無効</option>
                </select>
            </div>

            <!-- 日付From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">開始日</label>
                <input type="date" x-model="dateFrom" 
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- 日付To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">終了日</label>
                <input type="date" x-model="dateTo" 
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- 顧客選択 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">顧客</label>
                <select x-model="customerId" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">全顧客</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">
                            {{ $customer->company_name }}
                            @if($customer->contact_name)
                                ({{ $customer->contact_name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <button @click="window.location.href = filterUrl()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                フィルター適用
            </button>
            <button @click="clearFilters()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                クリア
            </button>
        </div>
    </div>

    <!-- 架電履歴一覧 -->
    <div class="bg-white/90 backdrop-blur-xl rounded-xl border border-white/20 shadow-lg overflow-hidden">
        @if($callLogs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                架電日時
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                顧客情報
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                結果
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                次回架電予定
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                メモ
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($callLogs as $callLog)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $callLog->called_at->format('Y/m/d') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $callLog->called_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $callLog->customer->company_name }}
                                    </div>
                                    @if($callLog->customer->contact_name)
                                        <div class="text-sm text-gray-500">
                                            {{ $callLog->customer->contact_name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $resultBadges = [
                                            'success' => 'bg-green-100 text-green-800',
                                            'appointment' => 'bg-blue-100 text-blue-800',
                                            'callback' => 'bg-yellow-100 text-yellow-800',
                                            'no_answer' => 'bg-gray-100 text-gray-800',
                                            'busy' => 'bg-orange-100 text-orange-800',
                                            'not_interested' => 'bg-red-100 text-red-800',
                                            'invalid_number' => 'bg-purple-100 text-purple-800'
                                        ];
                                        $resultLabels = [
                                            'success' => '成功',
                                            'appointment' => 'アポ取得',
                                            'callback' => '折り返し',
                                            'no_answer' => '不在',
                                            'busy' => '話し中',
                                            'not_interested' => '関心なし',
                                            'invalid_number' => '番号無効'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $resultBadges[$callLog->result] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $resultLabels[$callLog->result] ?? $callLog->result }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($callLog->next_call_date)
                                        {{ \Carbon\Carbon::parse($callLog->next_call_date)->format('Y/m/d') }}
                                    @else
                                        <span class="text-gray-400">未設定</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($callLog->notes)
                                        <div class="text-sm text-gray-900 max-w-xs truncate">
                                            {{ $callLog->notes }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">メモなし</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('call-logs.show', $callLog) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors">
                                            詳細
                                        </a>
                                        <a href="{{ route('call-logs.edit', $callLog) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                            編集
                                        </a>
                                        <form method="POST" action="{{ route('call-logs.destroy', $callLog) }}" 
                                              class="inline"
                                              onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors">
                                                削除
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
            @if($callLogs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/80">
                    {{ $callLogs->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">架電履歴がありません</h3>
                <p class="text-gray-500 mb-4">新しい架電記録を追加してください。</p>
                <a href="{{ route('call-logs.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    最初の架電記録を追加
                </a>
            </div>
        @endif
    </div>
</div>
@endsection