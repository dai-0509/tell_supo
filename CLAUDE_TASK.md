# F005 顧客一覧検索・ソート・フィルタ機能

## 🎯 目的
顧客一覧画面に検索・ソート・フィルタ機能を実装し、UI改修で配置済みのフィルター要素を動作させる

## 📋 スコープ

### In Scope
- 会社名・担当者名での検索機能
- ステータス・温度感・業界でのフィルタ機能
- 作成日・更新日・会社名でのソート機能
- URLクエリパラメータでの状態維持
- ページネーション連携

### Out of Scope
- 新しいUI要素の追加（既存UI活用）
- 全文検索やあいまい検索
- エクスポート機能
- 一括操作機能

## ✅ Definition of Done

### 技術要件
- [x] Laravel Eloquentスコープで検索・フィルタ実装
- [x] N+1クエリ発生なし
- [x] URLクエリパラメータで状態保持
- [x] ページネーション連携動作
- [x] Laravel Pint差分なし

### 機能要件
- [x] 検索: 会社名・担当者名の部分一致検索
- [x] フィルタ: ステータス（new/contacted/qualified/proposal/negotiation/closed_won）
- [x] フィルタ: 温度感（高/中/低）
- [x] フィルタ: 業界（IT/製造業/小売業等）
- [x] ソート: 作成日（新しい順/古い順）・会社名（あいうえお順）・更新日
- [x] 「全て」選択で初期状態に戻る

### UX要件
- [x] フィルタ適用時に件数表示更新
- [x] 検索結果0件時の適切なメッセージ
- [x] フィルタクリア機能
- [x] ページネーション状態維持

### 品質要件
- [x] 既存テスト全て通過（87テスト・246アサーション）
- [x] 新機能のFeatureテスト追加（11テスト追加）
- [x] 境界値テスト（空文字・特殊文字・大量データ）

## 🏗️ 実装計画

### Phase 1: バックエンド基盤
1. Customer Eloquentモデルにスコープ追加
   - `scopeSearch()` - 会社名・担当者名検索
   - `scopeFilterByStatus()` - ステータスフィルタ
   - `scopeFilterByTemperature()` - 温度感フィルタ
   - `scopeFilterByIndustry()` - 業界フィルタ
   - `scopeSortBy()` - ソート機能

2. CustomerController index()メソッド修正
   - リクエストパラメータ受け取り
   - スコープチェーン適用
   - ページネーション連携

### Phase 2: フロントエンド連携
1. 顧客一覧画面の既存フィルタUIと連携
   - 検索フォーム送信処理
   - フィルタselect変更時処理
   - ソート変更時処理

2. URL状態管理
   - クエリパラメータでの状態保持
   - ページネーション連携
   - ブラウザ戻る/進むボタン対応

### Phase 3: テスト実装
1. Unit Test: Customerモデルスコープテスト
2. Feature Test: 検索・フィルタ・ソート・ページネーション組み合わせテスト

## 🎨 UI/UX仕様

### 既存UI活用
```html
<!-- UI改修で既に配置済みの要素を活用 -->
<div class="toolbar">
  <input type="text" name="search" placeholder="会社名・担当者名で検索">
  <select name="status">全て/new/contacted/qualified...</select>
  <select name="temperature">全て/高/中/低</select>
  <select name="industry">全て/IT/製造業...</select>
  <select name="sort">作成日（新）/作成日（古）/会社名</select>
</div>
```

### 状態表示
- フィルタ適用中: `検索結果 15件`
- フィルタなし: `全 25件`
- 検索結果0件: `条件に一致する顧客が見つかりません`

## 📊 データベース設計

### 活用するINDEX
```sql
-- 既存のインデックスを活用
customers (user_id, created_at)   -- 基本検索＋ソート
customers (user_id, status)       -- ステータスフィルタ  
customers (user_id, industry)     -- 業界フィルタ
```

### クエリ最適化
```php
// 想定クエリパフォーマンス
Customer::where('user_id', auth()->id())
    ->when($search, fn($q) => $q->where(function($q) use ($search) {
        $q->where('company_name', 'like', "%{$search}%")
          ->orWhere('contact_name', 'like', "%{$search}%");
    }))
    ->when($status, fn($q) => $q->where('status', $status))
    ->when($temperature, fn($q) => $q->where('temperature_rating', $temperature))
    ->orderBy($sort, $direction)
    ->paginate(20);
```

## 🔧 技術仕様

### Request Validation
```php
$request->validate([
    'search' => 'nullable|string|max:255',
    'status' => 'nullable|in:new,contacted,qualified,proposal,negotiation,closed_won',
    'temperature' => 'nullable|in:高,中,低',
    'industry' => 'nullable|in:IT,製造業,小売業,...',
    'sort' => 'nullable|in:created_at,updated_at,company_name',
    'direction' => 'nullable|in:asc,desc',
]);
```

### Eloquent Scope例
```php
public function scopeSearch($query, $search)
{
    if (empty($search)) return $query;
    
    return $query->where(function($q) use ($search) {
        $q->where('company_name', 'like', "%{$search}%")
          ->orWhere('contact_name', 'like', "%{$search}%");
    });
}
```

## 📈 成功指標

### 技術指標
- 検索レスポンス時間 < 500ms（1000件規模）
- N+1クエリ発生 = 0件
- テストカバレッジ = 100%

### UX指標
- フィルタ操作の直感性
- 検索結果の適切性
- ページネーション動作の滑らかさ

## 🚀 次のステップ

1. **Phase 1実装** - Eloquentスコープとコントローラー修正
2. **Phase 2実装** - フロントエンド連携とURL状態管理
3. **Phase 3実装** - 包括的テストスイート
4. **QAテスト** - 動作確認とパフォーマンステスト
5. **PR作成・マージ** - コードレビュー後本番反映

---

**開始日**: 2025-11-25  
**担当**: Claude + 開発チーム  
**前提条件**: UI改修完了（feat/ui-improvement-complete マージ済み）
**期限**: 2日以内（高優先度機能のため）