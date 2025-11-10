<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                顧客詳細: {{ $customer->company_name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('customers.edit', $customer) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                   style="background-color: #3b82f6 !important; color: white !important;">
                    編集
                </a>
                <a href="{{ route('customers.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                   style="background-color: #6b7280 !important; color: white !important;">
                    一覧に戻る
                </a>
            </div>
        </div>
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
                        <!-- 基本情報 -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">基本情報</h3>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">会社名</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->company_name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">担当者名</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->contact_name ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $customer->email }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">電話番号</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $customer->phone }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">業界</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->industry ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">エリア</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->area ?? '-' }}</dd>
                            </div>
                        </div>

                        <!-- 管理情報 -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">管理情報</h3>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">温度感</dt>
                                <dd class="mt-1">
                                    @if($customer->temperature_rating)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($customer->temperature_rating === 'A') bg-red-100 text-red-800
                                            @elseif($customer->temperature_rating === 'B') bg-orange-100 text-orange-800
                                            @elseif($customer->temperature_rating === 'C') bg-yellow-100 text-yellow-800
                                            @elseif($customer->temperature_rating === 'D') bg-green-100 text-green-800
                                            @elseif($customer->temperature_rating === 'E') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $customer->temperature_rating }}
                                            @if($customer->temperature_rating === 'A') (最高)
                                            @elseif($customer->temperature_rating === 'B') (高)
                                            @elseif($customer->temperature_rating === 'C') (中)
                                            @elseif($customer->temperature_rating === 'D') (低)
                                            @elseif($customer->temperature_rating === 'E') (最低)
                                            @elseif($customer->temperature_rating === 'F') (対象外)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">優先度</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($customer->priority)
                                        {{ $customer->priority }}
                                        @if($customer->priority == 1) (最高)
                                        @elseif($customer->priority == 5) (最低)
                                        @endif
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">ステータス</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $customer->status }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">登録日時</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->created_at }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">更新日時</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->updated_at }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- メモ -->
                    @if($customer->memo)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">メモ</h3>
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $customer->memo }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- アクション -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between">
                            <div>
                                <form action="{{ route('customers.destroy', $customer) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('本当に削除しますか？この操作は元に戻せません。')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                            style="background-color: #ef4444 !important; color: white !important;">
                                        削除
                                    </button>
                                </form>
                            </div>
                            <div class="space-x-2">
                                <a href="{{ route('customers.edit', $customer) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                   style="background-color: #3b82f6 !important; color: white !important;">
                                    編集
                                </a>
                                <a href="{{ route('customers.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                   style="background-color: #6b7280 !important; color: white !important;">
                                    一覧に戻る
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>