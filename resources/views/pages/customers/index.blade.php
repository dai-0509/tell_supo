<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            顧客一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($customers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <!-- モバイル用注記 -->
                                <caption class="sr-only">顧客一覧テーブル。横スクロール可能。</caption>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            会社名
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            担当者
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            電話番号
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            温度感
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ステータス
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            操作
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customers as $customer)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $customer->company_name }}
                                                </div>
                                                @if($customer->email)
                                                    <div class="text-sm text-gray-500">
                                                        {{ $customer->email }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $customer->contact_name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $customer->phone ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($customer->temperature_rating)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($customer->temperature_rating === 'A') bg-red-100 text-red-800
                                                        @elseif($customer->temperature_rating === 'B') bg-orange-100 text-orange-800
                                                        @elseif($customer->temperature_rating === 'C') bg-yellow-100 text-yellow-800
                                                        @elseif($customer->temperature_rating === 'D') bg-green-100 text-green-800
                                                        @elseif($customer->temperature_rating === 'E') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $customer->temperature_rating }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $customer->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <a href="{{ route('customers.show', $customer) }}" 
                                                   class="text-blue-600 hover:text-blue-900">詳細</a>
                                                <a href="{{ route('customers.edit', $customer) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">編集</a>
                                                <form action="{{ route('customers.destroy', $customer) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('本当に削除しますか？')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900">削除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- ページネーション -->
                        <div class="mt-6">
                            {{ $customers->links() }}
                        </div>
                    @else
                        <div class="py-16">
                            <p class="text-lg text-gray-600 mb-6">登録中の顧客データがありません。</p>
                            <a href="{{ route('customers.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded inline-block"
                               style="background-color: #3b82f6 !important; color: white !important; text-decoration: none;">
                                新規登録
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>