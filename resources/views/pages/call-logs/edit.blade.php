<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            架電記録編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('call-logs.update', $callLog) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- 顧客選択 -->
                        <div class="space-y-2">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">
                                顧客 <span class="text-red-500">*</span>
                            </label>
                            <select id="customer_id" 
                                    name="customer_id" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                <option value="">顧客を選択してください</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $callLog->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                        @if($customer->contact_name)
                                            ({{ $customer->contact_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 開始時刻 -->
                        <div class="space-y-2">
                            <label for="started_at" class="block text-sm font-medium text-gray-700">
                                開始時刻 <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" 
                                   id="started_at" 
                                   name="started_at" 
                                   value="{{ old('started_at', $callLog->started_at->format('Y-m-d\TH:i')) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   required>
                            @error('started_at')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 終了時刻 -->
                        <div class="space-y-2">
                            <label for="ended_at" class="block text-sm font-medium text-gray-700">
                                終了時刻
                            </label>
                            <input type="datetime-local" 
                                   id="ended_at" 
                                   name="ended_at" 
                                   value="{{ old('ended_at', optional($callLog->ended_at)->format('Y-m-d\TH:i')) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('ended_at')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500">終了時刻を入力すると、通話時間が自動計算されます。</p>
                        </div>

                        <!-- 通話結果 -->
                        <div class="space-y-2">
                            <label for="result" class="block text-sm font-medium text-gray-700">
                                通話結果 <span class="text-red-500">*</span>
                            </label>
                            <select id="result" 
                                    name="result" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                <option value="">結果を選択してください</option>
                                @foreach($resultOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('result', $callLog->result) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('result')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- メモ -->
                        <div class="space-y-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                メモ
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="通話内容や次回のアクションなどを記録..."
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $callLog->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 送信ボタン -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('call-logs.show', $callLog) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                               style="background-color: #6b7280 !important; color: white !important; text-decoration: none;">
                                キャンセル
                            </a>
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                    style="background-color: #10b981 !important; color: white !important;">
                                更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>