@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- ヘッダー -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('customers.index') }}" 
               class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">新規顧客登録</h1>
                <p class="text-gray-600 mt-1">新しい顧客情報を登録します</p>
            </div>
        </div>
    </div>

    <!-- エラー表示 -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">入力に問題があります</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- フォーム -->
    <form method="POST" action="{{ route('customers.store') }}" class="space-y-8">
        @csrf
        
        <!-- 基本情報セクション -->
        <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">基本情報</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 会社名 -->
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">会社名 <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="company_name" 
                           id="company_name"
                           value="{{ old('company_name') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('company_name') ? 'border-red-300' : '' }}">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 担当者名 -->
                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">担当者名</label>
                    <input type="text" 
                           name="contact_name" 
                           id="contact_name"
                           value="{{ old('contact_name') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('contact_name') ? 'border-red-300' : '' }}">
                    @error('contact_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- メールアドレス -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">メールアドレス</label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('email') ? 'border-red-300' : '' }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 電話番号 -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">電話番号</label>
                    <input type="tel" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('phone') ? 'border-red-300' : '' }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 戦略タグセクション -->
        <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">戦略タグ</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 温度感 -->
                <div>
                    <label for="temperature_rating" class="block text-sm font-medium text-gray-700 mb-2">温度感レーティング</label>
                    <select name="temperature_rating" 
                            id="temperature_rating"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('temperature_rating') ? 'border-red-300' : '' }}">
                        <option value="">選択してください</option>
                        <option value="A" {{ old('temperature_rating') == 'A' ? 'selected' : '' }}>A（最高 - 即決見込み）</option>
                        <option value="B" {{ old('temperature_rating') == 'B' ? 'selected' : '' }}>B（高 - 強い関心）</option>
                        <option value="C" {{ old('temperature_rating') == 'C' ? 'selected' : '' }}>C（中 - 検討中）</option>
                        <option value="D" {{ old('temperature_rating') == 'D' ? 'selected' : '' }}>D（低 - 情報収集段階）</option>
                        <option value="E" {{ old('temperature_rating') == 'E' ? 'selected' : '' }}>E（最低 - 関心薄）</option>
                        <option value="F" {{ old('temperature_rating') == 'F' ? 'selected' : '' }}>F（要検討 - 再アプローチ必要）</option>
                    </select>
                    @error('temperature_rating')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- エリア -->
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 mb-2">エリア</label>
                    <input type="text" 
                           name="area" 
                           id="area"
                           value="{{ old('area') }}"
                           placeholder="例：東京都、大阪府、関東地方"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('area') ? 'border-red-300' : '' }}">
                    @error('area')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 業界 -->
                <div>
                    <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">業界</label>
                    <input type="text" 
                           name="industry" 
                           id="industry"
                           value="{{ old('industry') }}"
                           placeholder="例：IT、製造業、金融業"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('industry') ? 'border-red-300' : '' }}">
                    @error('industry')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- ステータス・優先度セクション -->
        <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">ステータス・優先度</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- ステータス -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">ステータス <span class="text-red-500">*</span></label>
                    <select name="status" 
                            id="status"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('status') ? 'border-red-300' : '' }}">
                        <option value="">選択してください</option>
                        <option value="new" {{ old('status', 'new') == 'new' ? 'selected' : '' }}>新規</option>
                        <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>連絡済み</option>
                        <option value="interested" {{ old('status') == 'interested' ? 'selected' : '' }}>興味あり</option>
                        <option value="not_interested" {{ old('status') == 'not_interested' ? 'selected' : '' }}>興味なし</option>
                        <option value="callback_scheduled" {{ old('status') == 'callback_scheduled' ? 'selected' : '' }}>コールバック予定</option>
                        <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>クローズ</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 優先度 -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">優先度 <span class="text-red-500">*</span></label>
                    <select name="priority" 
                            id="priority"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('priority') ? 'border-red-300' : '' }}">
                        <option value="">選択してください</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>高</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>中</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>低</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- メモセクション -->
        <div class="bg-white/80 backdrop-blur-xl rounded-xl p-8 border border-white/20 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 pb-2 border-b border-gray-200">メモ</h2>
            
            <div>
                <label for="memo" class="block text-sm font-medium text-gray-700 mb-2">備考・メモ</label>
                <textarea name="memo" 
                          id="memo"
                          rows="4"
                          placeholder="顧客に関する重要な情報や特記事項があれば記入してください..."
                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('memo') ? 'border-red-300' : '' }}">{{ old('memo') }}</textarea>
                @error('memo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- ボタン群 -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('customers.index') }}" 
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200">
                キャンセル
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>登録する</span>
            </button>
        </div>
    </form>
</div>
@endsection