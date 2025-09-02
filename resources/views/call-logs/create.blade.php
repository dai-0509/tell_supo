@extends('layouts.app')

@section('content')
<div class="p-6" x-data="{
    selectedCustomerId: '{{ $selectedCustomer?->id ?? old('customer_id') }}',
    result: '{{ old('result') }}',
    
    init() {
        // 結果に応じて次回架電日の表示/非表示を制御
        this.$watch('result', (value) => {
            const nextCallField = document.getElementById('next_call_date_field');
            if (value === 'callback' || value === 'appointment' || value === 'not_interested') {
                nextCallField.style.display = 'block';
            } else {
                nextCallField.style.display = 'none';
                document.getElementById('next_call_date').value = '';
            }
        });
    }
}">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('call-logs.index') }}" 
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">新規架電記録</h1>
                <p class="text-gray-600 mt-1">顧客への架電記録を追加</p>
            </div>
        </div>
    </div>

    <div class="bg-white/90 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
        <form method="POST" action="{{ route('call-logs.store') }}" class="space-y-6">
            @csrf
            
            <!-- 顧客選択 -->
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                    顧客 <span class="text-red-500">*</span>
                </label>
                <select id="customer_id" name="customer_id" x-model="selectedCustomerId" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('customer_id') border-red-500 @enderror">
                    <option value="">顧客を選択してください</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" 
                                @if($selectedCustomer && $selectedCustomer->id == $customer->id) selected @endif>
                            {{ $customer->company_name }}
                            @if($customer->contact_name)
                                ({{ $customer->contact_name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 架電日時 -->
                <div>
                    <label for="called_at" class="block text-sm font-medium text-gray-700 mb-2">
                        架電日時 <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="called_at" 
                           name="called_at" 
                           value="{{ old('called_at', now()->format('Y-m-d\TH:i')) }}" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('called_at') border-red-500 @enderror">
                    @error('called_at')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 結果 -->
                <div>
                    <label for="result" class="block text-sm font-medium text-gray-700 mb-2">
                        結果 <span class="text-red-500">*</span>
                    </label>
                    <select id="result" name="result" x-model="result" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('result') border-red-500 @enderror">
                        <option value="">結果を選択してください</option>
                        <option value="success" @if(old('result') == 'success') selected @endif>成功</option>
                        <option value="appointment" @if(old('result') == 'appointment') selected @endif>アポ取得</option>
                        <option value="callback" @if(old('result') == 'callback') selected @endif>折り返し</option>
                        <option value="no_answer" @if(old('result') == 'no_answer') selected @endif>不在</option>
                        <option value="busy" @if(old('result') == 'busy') selected @endif>話し中</option>
                        <option value="not_interested" @if(old('result') == 'not_interested') selected @endif>関心なし</option>
                        <option value="invalid_number" @if(old('result') == 'invalid_number') selected @endif>番号無効</option>
                    </select>
                    @error('result')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 次回架電予定日 -->
            <div id="next_call_date_field" style="display: none;">
                <label for="next_call_date" class="block text-sm font-medium text-gray-700 mb-2">
                    次回架電予定日
                </label>
                <input type="date" 
                       id="next_call_date" 
                       name="next_call_date" 
                       value="{{ old('next_call_date') }}" 
                       min="{{ date('Y-m-d', strtotime('tomorrow')) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('next_call_date') border-red-500 @enderror">
                <p class="text-sm text-gray-500 mt-1">
                    アポ取得、折り返し、関心なしの場合に設定してください
                </p>
                @error('next_call_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- メモ -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    メモ
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="4" 
                          placeholder="架電時の詳細な内容、顧客の反応、次回のアクション等を記録してください..."
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">
                    最大1000文字まで入力可能です
                </p>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 顧客情報プレビュー -->
            <div x-show="selectedCustomerId" class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">選択中の顧客情報</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    @foreach($customers as $customer)
                        <div x-show="selectedCustomerId == '{{ $customer->id }}'" class="space-y-1">
                            <div><span class="font-medium">会社名:</span> {{ $customer->company_name }}</div>
                            @if($customer->contact_name)
                                <div><span class="font-medium">担当者:</span> {{ $customer->contact_name }}</div>
                            @endif
                            @if($customer->phone_number)
                                <div><span class="font-medium">電話番号:</span> {{ $customer->phone_number }}</div>
                            @endif
                            @if($customer->temperature_rating)
                                <div><span class="font-medium">温度感:</span> 
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($customer->temperature_rating === 'A') bg-red-100 text-red-800
                                        @elseif($customer->temperature_rating === 'B') bg-orange-100 text-orange-800
                                        @elseif($customer->temperature_rating === 'C') bg-yellow-100 text-yellow-800
                                        @elseif($customer->temperature_rating === 'D') bg-green-100 text-green-800
                                        @elseif($customer->temperature_rating === 'E') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $customer->temperature_rating }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    架電記録を保存
                </button>
                <a href="{{ route('call-logs.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    キャンセル
                </a>
            </div>
        </form>
    </div>

    <!-- 架電のコツ（サイドパネル） -->
    <div class="mt-8 bg-blue-50/80 backdrop-blur-xl rounded-xl p-6 border border-blue-200/50">
        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            架電記録のポイント
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
            <div class="space-y-2">
                <div><strong>結果の選択:</strong></div>
                <ul class="ml-4 space-y-1">
                    <li>• <strong>成功:</strong> 商談や提案に進展</li>
                    <li>• <strong>アポ取得:</strong> 具体的な面談予定が確定</li>
                    <li>• <strong>折り返し:</strong> 相手から連絡をもらう約束</li>
                    <li>• <strong>不在:</strong> 担当者が不在だった</li>
                </ul>
            </div>
            <div class="space-y-2">
                <div><strong>メモの書き方:</strong></div>
                <ul class="ml-4 space-y-1">
                    <li>• 顧客の反応や関心度</li>
                    <li>• 提供した情報や資料</li>
                    <li>• 次回のアクション</li>
                    <li>• 特記事項や気づき</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 初期状態で結果に応じた次回架電日の表示制御
    const resultField = document.getElementById('result');
    const nextCallField = document.getElementById('next_call_date_field');
    
    function toggleNextCallDate() {
        const value = resultField.value;
        if (value === 'callback' || value === 'appointment' || value === 'not_interested') {
            nextCallField.style.display = 'block';
        } else {
            nextCallField.style.display = 'none';
            document.getElementById('next_call_date').value = '';
        }
    }
    
    // 初期表示
    toggleNextCallDate();
    
    // 変更時の制御
    resultField.addEventListener('change', toggleNextCallDate);
});
</script>
@endsection