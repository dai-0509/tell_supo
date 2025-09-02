@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('call-logs.index') }}" 
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900">架電記録詳細</h1>
                <p class="text-gray-600 mt-1">{{ $callLog->customer->company_name }} への架電記録</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('call-logs.edit', $callLog) }}" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    編集
                </a>
                <form method="POST" action="{{ route('call-logs.destroy', $callLog) }}" 
                      class="inline" onsubmit="return confirm('本当に削除しますか？この操作は取り消せません。');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        削除
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- メインコンテンツ -->
        <div class="xl:col-span-2 space-y-6">
            <!-- 架電記録情報 -->
            <div class="bg-white/90 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    架電記録情報
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 架電日時 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">架電日時</label>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $callLog->called_at->format('Y年m月d日') }}
                        </div>
                        <div class="text-gray-600">
                            {{ $callLog->called_at->format('H:i') }} ({{ $callLog->called_at->format('l') }})
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ $callLog->called_at->diffForHumans() }}
                        </div>
                    </div>

                    <!-- 結果 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">架電結果</label>
                        @php
                            $resultBadges = [
                                'success' => 'bg-green-100 text-green-800 border-green-200',
                                'appointment' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'callback' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'no_answer' => 'bg-gray-100 text-gray-800 border-gray-200',
                                'busy' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'not_interested' => 'bg-red-100 text-red-800 border-red-200',
                                'invalid_number' => 'bg-purple-100 text-purple-800 border-purple-200'
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
                            $resultIcons = [
                                'success' => 'M5 13l4 4L19 7',
                                'appointment' => 'M8 7V3a1 1 0 012 0v4h4a1 1 0 110 2H10v4a1 1 0 11-2 0V9H4a1 1 0 110-2h4z',
                                'callback' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                                'no_answer' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-4-4m3-3V8a2 2 0 00-2-2h-1m-4 0V3a2 2 0 00-2-2H5a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2V8z',
                                'busy' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                'not_interested' => 'M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17.294 15M10 14h4m6.5-4a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z',
                                'invalid_number' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
                            ];
                        @endphp
                        <div class="inline-flex items-center px-4 py-2 rounded-lg border-2 {{ $resultBadges[$callLog->result] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $resultIcons[$callLog->result] ?? 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                            </svg>
                            <span class="font-semibold text-lg">{{ $resultLabels[$callLog->result] ?? $callLog->result }}</span>
                        </div>
                    </div>

                    <!-- 次回架電予定日 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">次回架電予定日</label>
                        @if($callLog->next_call_date)
                            <div class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($callLog->next_call_date)->format('Y年m月d日') }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($callLog->next_call_date)->format('l') }} 
                                ({{ \Carbon\Carbon::parse($callLog->next_call_date)->diffForHumans() }})
                            </div>
                            @if(\Carbon\Carbon::parse($callLog->next_call_date)->isPast())
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    期限超過
                                </div>
                            @elseif(\Carbon\Carbon::parse($callLog->next_call_date)->isToday())
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    本日予定
                                </div>
                            @endif
                        @else
                            <div class="text-gray-400 italic">設定なし</div>
                        @endif
                    </div>

                    <!-- 記録情報 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">記録情報</label>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>記録ID: <span class="font-mono">#{{ $callLog->id }}</span></div>
                            <div>作成日時: {{ $callLog->created_at->format('Y/m/d H:i') }}</div>
                            @if($callLog->created_at->ne($callLog->updated_at))
                                <div>最終更新: {{ $callLog->updated_at->format('Y/m/d H:i') }}</div>
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    編集済み
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- メモ -->
            <div class="bg-white/90 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    架電メモ
                </h2>
                
                @if($callLog->notes)
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-green-500">
                        <div class="whitespace-pre-wrap text-gray-800 leading-relaxed">{{ $callLog->notes }}</div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <p>メモが記録されていません</p>
                        <a href="{{ route('call-logs.edit', $callLog) }}" 
                           class="text-blue-600 hover:text-blue-800 underline mt-2 inline-block">
                            メモを追加する
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- サイドバー -->
        <div class="space-y-6">
            <!-- 顧客情報 -->
            <div class="bg-white/90 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    顧客情報
                </h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 uppercase tracking-wide">会社名</label>
                        <div class="font-semibold text-gray-900">{{ $callLog->customer->company_name }}</div>
                    </div>
                    
                    @if($callLog->customer->contact_name)
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wide">担当者名</label>
                            <div class="text-gray-800">{{ $callLog->customer->contact_name }}</div>
                        </div>
                    @endif
                    
                    @if($callLog->customer->phone_number)
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wide">電話番号</label>
                            <div class="font-mono text-gray-800">{{ $callLog->customer->phone_number }}</div>
                        </div>
                    @endif
                    
                    @if($callLog->customer->email)
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wide">メールアドレス</label>
                            <div class="text-gray-800 break-all">{{ $callLog->customer->email }}</div>
                        </div>
                    @endif
                    
                    @if($callLog->customer->temperature_rating)
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wide">温度感</label>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                @if($callLog->customer->temperature_rating === 'A') bg-red-100 text-red-800
                                @elseif($callLog->customer->temperature_rating === 'B') bg-orange-100 text-orange-800
                                @elseif($callLog->customer->temperature_rating === 'C') bg-yellow-100 text-yellow-800
                                @elseif($callLog->customer->temperature_rating === 'D') bg-green-100 text-green-800
                                @elseif($callLog->customer->temperature_rating === 'E') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $callLog->customer->temperature_rating }}級
                            </span>
                        </div>
                    @endif
                    
                    @if($callLog->customer->area)
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wide">エリア</label>
                            <div class="text-gray-800">{{ $callLog->customer->area }}</div>
                        </div>
                    @endif
                    
                    @if($callLog->customer->industry)
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wide">業界</label>
                            <div class="text-gray-800">{{ $callLog->customer->industry }}</div>
                        </div>
                    @endif
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <a href="{{ route('customers.show', $callLog->customer) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                        顧客詳細を見る
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- アクション -->
            <div class="bg-white/90 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">アクション</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('call-logs.create', ['customer_id' => $callLog->customer_id]) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        同じ顧客に再架電
                    </a>
                    
                    <a href="{{ route('customers.edit', $callLog->customer) }}" 
                       class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        顧客情報を更新
                    </a>
                </div>
            </div>

            <!-- 統計情報 -->
            @if($callLog->customer->callLogs->count() > 1)
                <div class="bg-white/90 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">この顧客の架電統計</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">総架電回数:</span>
                            <span class="font-semibold">{{ $callLog->customer->callLogs->count() }}回</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">初回架電:</span>
                            <span class="text-gray-800">{{ $callLog->customer->callLogs->sortBy('called_at')->first()->called_at->format('Y/m/d') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">最新架電:</span>
                            <span class="text-gray-800">{{ $callLog->customer->callLogs->sortByDesc('called_at')->first()->called_at->format('Y/m/d') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection