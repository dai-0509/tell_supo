<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            架電記録詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 顧客情報 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">顧客</label>
                            <div class="p-3 bg-gray-50 rounded-md">
                                <div class="font-medium text-gray-900">{{ $callLog->customer->company_name }}</div>
                                @if($callLog->customer->contact_name)
                                    <div class="text-sm text-gray-500">{{ $callLog->customer->contact_name }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- 開始時刻 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">開始時刻</label>
                            <div class="p-3 bg-gray-50 rounded-md text-gray-900">
                                {{ $callLog->started_at->format('Y年m月d日 H:i') }}
                            </div>
                        </div>

                        <!-- 終了時刻 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">終了時刻</label>
                            <div class="p-3 bg-gray-50 rounded-md text-gray-900">
                                @if($callLog->ended_at)
                                    {{ $callLog->ended_at->format('Y年m月d日 H:i') }}
                                @else
                                    <span class="text-gray-500">未設定</span>
                                @endif
                            </div>
                        </div>

                        <!-- 通話時間 -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">通話時間</label>
                            <div class="p-3 bg-gray-50 rounded-md text-gray-900">
                                {{ $callLog->formatted_duration }}
                            </div>
                        </div>

                        <!-- 通話結果 -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">通話結果</label>
                            <div class="p-3 bg-gray-50 rounded-md">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    @if($callLog->result === 'connected') bg-green-100 text-green-800
                                    @elseif($callLog->result === 'no_answer') bg-yellow-100 text-yellow-800
                                    @elseif($callLog->result === 'busy') bg-orange-100 text-orange-800
                                    @elseif($callLog->result === 'failed') bg-red-100 text-red-800
                                    @elseif($callLog->result === 'voicemail') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $callLog->result_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- メモ -->
                    @if($callLog->notes)
                        <div class="mt-6 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">メモ</label>
                            <div class="p-4 bg-gray-50 rounded-md text-gray-900 whitespace-pre-wrap">{{ $callLog->notes }}</div>
                        </div>
                    @endif

                    <!-- 操作ボタン -->
                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('call-logs.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                           style="background-color: #6b7280 !important; color: white !important; text-decoration: none;">
                            ← 一覧に戻る
                        </a>
                        <div class="space-x-4">
                            <a href="{{ route('call-logs.edit', $callLog) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                               style="background-color: #3b82f6 !important; color: white !important; text-decoration: none;">
                                編集
                            </a>
                            <form action="{{ route('call-logs.destroy', $callLog) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('本当に削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        style="background-color: #ef4444 !important; color: white !important;">
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>