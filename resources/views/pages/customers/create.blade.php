<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            顧客登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('customers.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- 会社名 -->
                        <div class="space-y-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700">
                                会社名 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="company_name" 
                                   id="company_name" 
                                   value="{{ old('company_name') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('company_name') border-red-500 @enderror"
                                   required>
                            @error('company_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 担当者名 -->
                        <div class="space-y-2">
                            <label for="contact_name" class="block text-sm font-medium text-gray-700">
                                担当者名
                            </label>
                            <input type="text" 
                                   name="contact_name" 
                                   id="contact_name" 
                                   value="{{ old('contact_name') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('contact_name') border-red-500 @enderror">
                            @error('contact_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- メールアドレス・電話番号 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    メールアドレス
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    電話番号
                                </label>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="090-1234-5678"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 業界・エリア -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="industry" class="block text-sm font-medium text-gray-700">
                                    業界
                                </label>
                                <select name="industry" 
                                        id="industry" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('industry') border-red-500 @enderror">
                                    <option value="">選択してください</option>
                                    <option value="IT" {{ old('industry') === 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="製造業" {{ old('industry') === '製造業' ? 'selected' : '' }}>製造業</option>
                                    <option value="小売業" {{ old('industry') === '小売業' ? 'selected' : '' }}>小売業</option>
                                    <option value="金融業" {{ old('industry') === '金融業' ? 'selected' : '' }}>金融業</option>
                                    <option value="医療・福祉" {{ old('industry') === '医療・福祉' ? 'selected' : '' }}>医療・福祉</option>
                                    <option value="教育" {{ old('industry') === '教育' ? 'selected' : '' }}>教育</option>
                                    <option value="建設・不動産" {{ old('industry') === '建設・不動産' ? 'selected' : '' }}>建設・不動産</option>
                                    <option value="運輸・物流" {{ old('industry') === '運輸・物流' ? 'selected' : '' }}>運輸・物流</option>
                                    <option value="飲食・宿泊" {{ old('industry') === '飲食・宿泊' ? 'selected' : '' }}>飲食・宿泊</option>
                                    <option value="士業・コンサル" {{ old('industry') === '士業・コンサル' ? 'selected' : '' }}>士業・コンサル</option>
                                    <option value="その他" {{ old('industry') === 'その他' ? 'selected' : '' }}>その他</option>
                                </select>
                                @error('industry')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="area" class="block text-sm font-medium text-gray-700">
                                    エリア
                                </label>
                                <input type="text" 
                                       name="area" 
                                       id="area" 
                                       value="{{ old('area') }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('area') border-red-500 @enderror">
                                @error('area')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 温度感・優先度 -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="temperature_rating" class="block text-sm font-medium text-gray-700">
                                    温度感
                                </label>
                                <select name="temperature_rating" 
                                        id="temperature_rating" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('temperature_rating') border-red-500 @enderror">
                                    <option value="">選択してください</option>
                                    <option value="A" {{ old('temperature_rating') === 'A' ? 'selected' : '' }}>A (最高)</option>
                                    <option value="B" {{ old('temperature_rating') === 'B' ? 'selected' : '' }}>B (高)</option>
                                    <option value="C" {{ old('temperature_rating') === 'C' ? 'selected' : '' }}>C (中)</option>
                                    <option value="D" {{ old('temperature_rating') === 'D' ? 'selected' : '' }}>D (低)</option>
                                    <option value="E" {{ old('temperature_rating') === 'E' ? 'selected' : '' }}>E (最低)</option>
                                    <option value="F" {{ old('temperature_rating') === 'F' ? 'selected' : '' }}>F (対象外)</option>
                                </select>
                                @error('temperature_rating')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="priority" class="block text-sm font-medium text-gray-700">
                                    優先度
                                </label>
                                <select name="priority" 
                                        id="priority" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-500 @enderror">
                                    <option value="">選択してください</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('priority') == $i ? 'selected' : ($i == 3 ? 'selected' : '') }}>
                                            {{ $i }}{{ $i == 1 ? ' (最高)' : ($i == 5 ? ' (最低)' : '') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('priority')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- ステータス -->
                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                ステータス
                            </label>
                            <input type="text" 
                                   name="status" 
                                   id="status" 
                                   value="{{ old('status', 'new') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- メモ -->
                        <div class="space-y-2">
                            <label for="memo" class="block text-sm font-medium text-gray-700">
                                メモ
                            </label>
                            <textarea name="memo" 
                                      id="memo" 
                                      rows="4" 
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('memo') border-red-500 @enderror">{{ old('memo') }}</textarea>
                            @error('memo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('customers.index') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    キャンセル
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        style="background-color: #2563eb !important; color: white !important;">
                                    登録
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>