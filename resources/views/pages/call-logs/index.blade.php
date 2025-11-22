<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            架電記録一覧
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
                    @if($callLogs->count() > 0)
                        <!-- 新規登録ボタン -->
                        <div class="mb-6">
                            <a href="{{ route('call-logs.create') }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                               style="background-color: #10b981 !important; color: white !important;">
                                + 新規架電記録
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <caption class="sr-only">架電記録一覧テーブル。横スクロール可能。</caption>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            日時
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            顧客
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            時間
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            結果
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
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $callLog->started_at->format('m/d H:i') }}
                                                </div>
                                                @if($callLog->ended_at)
                                                    <div class="text-xs text-gray-500">
                                                        終了: {{ $callLog->ended_at->format('H:i') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $callLog->customer->company_name }}
                                                </div>
                                                @if($callLog->customer->contact_name)
                                                    <div class="text-sm text-gray-500">
                                                        {{ $callLog->customer->contact_name }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $callLog->formatted_duration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($callLog->result === 'connected') bg-green-100 text-green-800
                                                    @elseif($callLog->result === 'no_answer') bg-yellow-100 text-yellow-800
                                                    @elseif($callLog->result === 'busy') bg-orange-100 text-orange-800
                                                    @elseif($callLog->result === 'failed') bg-red-100 text-red-800
                                                    @elseif($callLog->result === 'voicemail') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $callLog->result_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($callLog->notes)
                                                    <span class="truncate max-w-xs block">{{ Str::limit($callLog->notes, 50) }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <a href="{{ route('call-logs.show', $callLog) }}" 
                                                   class="text-blue-600 hover:text-blue-900">詳細</a>
                                                <a href="{{ route('call-logs.edit', $callLog) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">編集</a>
                                                <form action="{{ route('call-logs.destroy', $callLog) }}" 
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
                            {{ $callLogs->links() }}
                        </div>
                    @else
                        <div class="py-16">
                            <p class="text-lg text-gray-600 mb-6">登録中の架電記録がありません。</p>
                            <a href="{{ route('call-logs.create') }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded inline-block"
                               style="background-color: #10b981 !important; color: white !important; text-decoration: none;">
                                + 新規架電記録
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>