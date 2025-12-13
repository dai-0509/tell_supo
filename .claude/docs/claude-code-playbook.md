# Claude コード実装プレイブック（TellSupo）

最終更新: 2025-11-04  
対象: Claude実装依頼時のテンプレート集

## 🎯 このドキュメントの目的

Claude に実装を依頼する際の**テンプレート集**と**自己チェック項目**を提供し、一貫性のある高品質なコード生成を実現する。

## 📋 依頼テンプレート

### テンプレート1: FormRequest実装

```
依頼: F{番号}-{段階}: {Model}Request（Store/Update）を実装

要件:
- 必須項目: {field1}, {field2}, {field3}
- オプション項目: {field4}, {field5}
- バリデーション: {特殊ルール}
- 前処理: {データ正規化処理}
- エラーメッセージ: 日本語

前提: Laravel 11 / PHP 8.2 / PSR-12 / N+1禁止
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

**使用例:**
```
依頼: F007-03: CustomerRequest（Store/Update）を実装

要件:
- 必須項目: name, phone, email
- オプション項目: address, notes
- バリデーション: phone uniqueチェック（Updateは自分以外）
- 前処理: 電話番号のハイフン自動除去
- エラーメッセージ: 日本語

前提: Laravel 11 / PHP 8.2 / PSR-12 / N+1禁止
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

### テンプレート2: Controller実装

```
依頼: F{番号}-{段階}: {Model}Controller の {actions} を実装

要件:
- アクション: {index/show/create/store/edit/update/destroy}
- ページネーション: {件数}
- N+1対策: {with句指定}
- リダイレクト: {成功時の遷移先}
- フラッシュメッセージ: 日本語

前提: Laravel 11 / PHP 8.2 / Controller薄く / Service層使用
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

**使用例:**
```
依頼: F007-04: CustomerController の create/store/edit/update を実装

要件:
- アクション: create, store, edit, update
- ページネーション: 不要
- N+1対策: 不要（単体操作）
- リダイレクト: 成功時は顧客詳細画面へ
- フラッシュメッセージ: 日本語

前提: Laravel 11 / PHP 8.2 / Controller薄く / Service層使用
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

### テンプレート3: Service実装

```
依頼: F{番号}-{段階}: {Model}Service の実装

要件:
- メソッド: {method1}, {method2}, {method3}
- トランザクション: {必要/不要}
- 業務ルール: {詳細なビジネスロジック}
- 例外処理: {エラーケース}
- 戻り値: {型指定}

前提: Laravel 11 / PHP 8.2 / 単一責任原則 / 型宣言必須
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

### テンプレート4: Blade UI実装

```
依頼: F{番号}-{段階}: {画面名} の UI実装

要件:
- レイアウト: {app.blade.php継承}
- 表示項目: {field1, field2, field3}
- 操作ボタン: {新規, 編集, 削除等}
- 状態表示: {空状態, ローディング等}
- レスポンシブ: モバイル対応

前提: Blade + Tailwind CSS + Alpine.js / アクセシビリティ考慮
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

### テンプレート5: Migration実装

```
依頼: F{番号}-{段階}: {table_name} テーブルの実装

要件:
- カラム: {詳細なカラム定義}
- インデックス: {検索対象カラム}
- 外部キー: {関連テーブル}
- 制約: {UNIQUE, NOT NULL等}

前提: MySQL 8.0 / Laravel migration / 後方互換性考慮
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

### テンプレート6: Feature Test実装

```
依頼: F{番号}-{段階}: {機能名} のFeatureテスト実装

要件:
- 正常系: {成功ケース}
- 異常系: {バリデーションエラー, 権限エラー等}
- 境界値: {最大文字数, 最小値等}
- データ: Factory使用
- アサーション: DB確認, レスポンス確認

前提: PHPUnit / RefreshDatabase / 日本語テスト名
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
疑似コード禁止、実行可能コードのみ
```

## 🔧 Claude向け出力フォーマット指定

### 標準出力順序
```
出力順序:
1. 【既存ファイル差分】- Edit tool使用
2. 【新規ファイル】- Write tool使用  
3. 【実行コマンド】- migration, route, composer等
4. 【テストコード】- Feature/Unit test
5. 【自己チェックリスト】- 実装確認項目

注意:
- 疑似コードは一切禁止
- 実行可能なコードのみ出力
- ファイルパスは絶対パス指定
- Laravel規約に準拠
```

### エラー時の対応指示
```
エラー対応:
- バリデーションエラー: 具体的なルール修正を提示
- N+1クエリ: with句の追加を提示
- テスト失敗: 修正版テストコードを出力
- Pint エラー: PSR-12準拠版を再出力

必須:
- 修正理由の明記
- 修正後のコード全体を再出力
- 影響範囲の説明
```

## 📝 自己チェック項目テンプレート

### Controller チェックリスト
```markdown
## Controller実装チェック

### 基本実装
- [ ] 型宣言完備（引数・戻り値）
- [ ] 薄いController（Service層移譲）
- [ ] 適切なHTTPステータス
- [ ] フラッシュメッセージ設定

### バリデーション
- [ ] FormRequest使用
- [ ] エラーハンドリング実装
- [ ] 例外処理適切

### パフォーマンス
- [ ] N+1クエリなし
- [ ] ページネーション実装
- [ ] 必要最小限のデータ取得

### セキュリティ
- [ ] CSRF保護
- [ ] 権限チェック
- [ ] SQLインジェクション対策

### テスト
- [ ] 正常系テスト
- [ ] 異常系テスト
- [ ] 境界値テスト
```

### Service チェックリスト
```markdown
## Service実装チェック

### アーキテクチャ
- [ ] 単一責任原則
- [ ] 依存性注入
- [ ] インターフェース使用
- [ ] 型宣言完備

### 業務ロジック
- [ ] トランザクション適切
- [ ] エラーハンドリング
- [ ] ログ出力
- [ ] 戻り値一貫性

### パフォーマンス
- [ ] N+1クエリ回避
- [ ] 無駄なDB アクセス削除
- [ ] キャッシュ活用
- [ ] 適切なインデックス使用

### テスト
- [ ] Unitテスト実装
- [ ] モック使用
- [ ] 境界値テスト
- [ ] 例外テスト
```

### UI チェックリスト
```markdown
## UI実装チェック

### 基本実装
- [ ] Blade継承適切
- [ ] Tailwind CSS使用
- [ ] Alpine.js活用
- [ ] CSRF Token設定

### UX/アクセシビリティ
- [ ] モバイル対応
- [ ] フォーカス管理
- [ ] エラー表示適切
- [ ] ローディング状態

### パフォーマンス
- [ ] 画像最適化
- [ ] CSS/JS最小化
- [ ] 不要なリクエスト削除
- [ ] キャッシュ活用

### テスト
- [ ] 表示テスト
- [ ] 操作テスト
- [ ] レスポンシブテスト
- [ ] エラー状態テスト
```

## 🎨 コード品質ガイドライン

### 命名規約確認
```php
// ✅ 良い例
class CustomerService
{
    public function createCustomer(array $customerData): Customer
    {
        $normalizedPhone = $this->normalizePhoneNumber($customerData['phone']);
        // ...
    }
}

// ❌ 悪い例  
class CustSrv
{
    public function create($data)
    {
        $tel = $this->norm($data['phone']);
        // ...
    }
}
```

### エラーハンドリング確認
```php
// ✅ 良い例
try {
    DB::beginTransaction();
    $customer = $this->customerService->create($data);
    DB::commit();
    
    return redirect()->route('customers.show', $customer)
        ->with('success', '顧客を登録しました');
        
} catch (ValidationException $e) {
    DB::rollBack();
    throw $e;
} catch (Exception $e) {
    DB::rollBack();
    Log::error('Customer creation failed', ['error' => $e->getMessage()]);
    
    return redirect()->back()
        ->withInput()
        ->with('error', '登録に失敗しました');
}
```

## 🚀 効率的な依頼方法

### 段階的実装
```
1. 仕様書作成 → 2. DB設計 → 3. Model実装 → 4. Request実装 
→ 5. Service実装 → 6. Controller実装 → 7. UI実装 → 8. テスト実装
```

### 依頼の具体化
```
❌ 曖昧: 「顧客管理機能を作って」
✅ 具体的: 「F007-03: CustomerRequest（Store/Update）を実装。必須項目name/phone/email、phoneのunique検証、ハイフン自動除去」
```

### レビューポイント
```
- PSR-12準拠確認
- N+1クエリチェック  
- テスト網羅性確認
- エラーハンドリング確認
- セキュリティチェック
```

## 📞 サポート・改善

このプレイブックは実装を通じて継続的に改善します。

- 新しいパターンの追加
- テンプレートの精度向上  
- チェックリストの拡充
- 依頼例の充実

## 🔗 関連ドキュメント

- `claude.md` - 基本的な連携方法
- `claude-coding-standards.md` - 詳細なコーディング規約
- `AGENT_CHAT_INDEX.md` - チャット履歴管理