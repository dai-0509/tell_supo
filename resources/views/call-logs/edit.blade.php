@extends('layouts.app')

@section('content')
<div class="p-6" x-data="{
    selectedCustomerId: '{{ old('customer_id', $callLog->customer_id) }}',
    result: '{{ old('result', $callLog->result) }}',
    
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
        
        // 初期表示制御
        this.$nextTick(() => {
            const nextCallField = document.getElementById('next_call_date_field');
            if (this.result === 'callback' || this.result === 'appointment' || this.result === 'not_interested') {
                nextCallField.style.display = 'block';
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
                <h1 class="text-3xl font-bold text-gray-900">架電記録編集</h1>
                <p class="text-gray-600 mt-1">{{ $callLog->customer->company_name }} への架電記録を編集</p>
            </div>
        </div>
    </div>

    <div class="bg-white/90 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
        <form method="POST" action="{{ route('call-logs.update', $callLog) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
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
                                @if(old('customer_id', $callLog->customer_id) == $customer->id) selected @endif>
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
                           value="{{ old('called_at', $callLog->called_at->format('Y-m-d\TH:i')) }}" 
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
                        <option value="success" @if(old('result', $callLog->result) == 'success') selected @endif>成功</option>
                        <option value="appointment" @if(old('result', $callLog->result) == 'appointment') selected @endif>アポ取得</option>
                        <option value="callback" @if(old('result', $callLog->result) == 'callback') selected @endif>折り返し</option>
                        <option value="no_answer" @if(old('result', $callLog->result) == 'no_answer') selected @endif>不在</option>
                        <option value="busy" @if(old('result', $callLog->result) == 'busy') selected @endif>話し中</option>
                        <option value="not_interested" @if(old('result', $callLog->result) == 'not_interested') selected @endif>関心なし</option>
                        <option value="invalid_number" @if(old('result', $callLog->result) == 'invalid_number') selected @endif>番号無効</option>
                    </select>
                    @error('result')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 次回架電予定日 -->
            <div id="next_call_date_field">
                <label for="next_call_date" class="block text-sm font-medium text-gray-700 mb-2">
                    次回架電予定日
                </label>
                <input type="date" 
                       id="next_call_date" 
                       name="next_call_date" 
                       value="{{ old('next_call_date', $callLog->next_call_date?->format('Y-m-d')) }}" 
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
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $callLog->notes) }}</textarea>
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

            <!-- 記録詳細情報 -->
            <div class="bg-blue-50/50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-700 mb-2">記録情報</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-600">
                    <div>
                        <span class="font-medium">作成日:</span> 
                        {{ $callLog->created_at->format('Y/m/d H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">最終更新:</span> 
                        {{ $callLog->updated_at->format('Y/m/d H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">記録ID:</span> 
                        #{{ $callLog->id }}
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    更新を保存
                </button>
                <a href="{{ route('call-logs.show', $callLog) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    詳細表示
                </a>
                <a href="{{ route('call-logs.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    キャンセル
                </a>
            </div>
        </form>
    </div>

    <!-- 編集履歴やヒント -->
    <div class="mt-8 bg-yellow-50/80 backdrop-blur-xl rounded-xl p-6 border border-yellow-200/50">
        <h3 class="text-lg font-semibold text-yellow-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            編集時の注意点
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-yellow-800">
            <div class="space-y-2">
                <div><strong>編集が推奨される場合:</strong></div>
                <ul class="ml-4 space-y-1">
                    <li>• 記録内容に誤りがあった場合</li>
                    <li>• 追加情報が得られた場合</li>
                    <li>• 結果や次回予定に変更があった場合</li>
                    <li>• より詳細なメモを追加したい場合</li>
                </ul>
            </div>
            <div class="space-y-2">
                <div><strong>変更履歴:</strong></div>
                <div class="text-xs bg-yellow-100 p-2 rounded">
                    <div>最終更新: {{ $callLog->updated_at->format('Y年m月d日 H:i') }}</div>
                    @if($callLog->created_at->ne($callLog->updated_at))
                        <div>作成日時: {{ $callLog->created_at->format('Y年m月d日 H:i') }}</div>
                        <div class="text-yellow-700 font-medium mt-1">※ この記録は編集されています</div>
                    @else
                        <div class="text-yellow-700">※ 未編集</div>
                    @endif
                </div>
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