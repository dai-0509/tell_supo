@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- ヘッダー -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('customers.index') }}" 
                   class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $customer->company_name }}</h1>
                    <p class="text-gray-600 mt-1">顧客詳細情報</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customers.edit', $customer) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>編集</span>
                </a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>架電</span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 左カラム -->
        <div class="lg:col-span-2 space-y-8">
            <!-- 基本情報 -->
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">基本情報</h2>
                
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">会社名</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $customer->company_name }}</dd>
                    </div>
                    
                    @if($customer->contact_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">担当者名</dt>
                        <dd class="mt-1 text-lg text-gray-900">{{ $customer->contact_name }}</dd>
                    </div>
                    @endif
                    
                    @if($customer->email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                        <dd class="mt-1 text-lg text-gray-900">
                            <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $customer->email }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    
                    @if($customer->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">電話番号</dt>
                        <dd class="mt-1 text-lg text-gray-900">
                            <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:text-blue-800">
                                {{ $customer->phone }}
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- 戦略タグ -->
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">戦略タグ</h2>
                
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">温度感レーティング</dt>
                        <dd class="mt-2">
                            @if($customer->temperature_rating)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $customer->temperature_rating == 'A' ? 'bg-red-100 text-red-800' : 
                                       ($customer->temperature_rating == 'B' ? 'bg-orange-100 text-orange-800' : 
                                       ($customer->temperature_rating == 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($customer->temperature_rating == 'D' ? 'bg-blue-100 text-blue-800' : 
                                       ($customer->temperature_rating == 'E' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')))) }}">
                                    {{ $customer->temperature_rating }}
                                    @switch($customer->temperature_rating)
                                        @case('A') - 最高 @break
                                        @case('B') - 高 @break
                                        @case('C') - 中 @break
                                        @case('D') - 低 @break
                                        @case('E') - 最低 @break
                                        @case('F') - 要検討 @break
                                    @endswitch
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">未設定</span>
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">エリア</dt>
                        <dd class="mt-2">
                            @if($customer->area)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $customer->area }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">未設定</span>
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">業界</dt>
                        <dd class="mt-2">
                            @if($customer->industry)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    {{ $customer->industry }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">未設定</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- メモ -->
            @if($customer->memo)
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">メモ・備考</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $customer->memo }}</p>
                </div>
            </div>
            @endif

            <!-- 架電履歴 -->
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 pb-2 border-b border-gray-200">架電履歴</h2>
                    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm">
                        + 架電記録を追加
                    </button>
                </div>
                
                @if($customer->callLogs && $customer->callLogs->count() > 0)
                    <div class="space-y-4">
                        @foreach($customer->callLogs->take(5) as $callLog)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $callLog->result }}</p>
                                        @if($callLog->notes)
                                            <p class="text-sm text-gray-600 mt-1">{{ $callLog->notes }}</p>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $callLog->called_at->format('Y/m/d H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($customer->callLogs->count() > 5)
                        <div class="mt-4 text-center">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                すべての履歴を表示（{{ $customer->callLogs->count() }}件）
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <p>まだ架電履歴がありません</p>
                        <p class="text-sm">初回の架電を記録しましょう</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- 右カラム -->
        <div class="space-y-6">
            <!-- ステータス・優先度 -->
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ステータス・優先度</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500">ステータス</span>
                        <div class="mt-1">
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
                        </div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500">優先度</span>
                        <div class="mt-1">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- 統計情報 -->
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">統計情報</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">架電回数</span>
                        <span class="font-semibold">{{ $customer->callLogs ? $customer->callLogs->count() : 0 }}回</span>
                    </div>
                    
                    @if($customer->callLogs && $customer->callLogs->count() > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">最終架電</span>
                        <span class="font-semibold">{{ $customer->callLogs->first()->called_at->format('Y/m/d') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">登録日</span>
                        <span class="font-semibold">{{ $customer->created_at->format('Y/m/d') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">最終更新</span>
                        <span class="font-semibold">{{ $customer->updated_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>

            <!-- クイックアクション -->
            <div class="bg-white/80 backdrop-blur-xl rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">クイックアクション</h3>
                
                <div class="space-y-3">
                    <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>架電する</span>
                    </button>
                    
                    @if($customer->email)
                    <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>メール送信</span>
                    </button>
                    @endif
                    
                    <button class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-200 text-sm flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>フォロー予約</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection