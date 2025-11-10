# F007 CLAUDE_TASK.md バックアップ

**保存日時**: 2025-11-10  
**元ブランチ**: feat/f007-crud-01-spec  
**目的**: 削除前のCLAUDE_TASK.mdの内容を記録

---

# CLAUDE_TASK.md — feat/f007-crud-01-spec

## 固定前提（共通）
前提: Laravel 11 / PHP 8.2 / MySQL 8 / Blade+Tailwind / JST。Controller薄く。N+1禁止。
出力順: ①差分 ②新規 ③実行コマンド ④テスト ⑤自己チェック。疑似コード禁止。

## このブランチの目的
F007: 顧客CRUD機能の完全実装（仕様→実装→テスト）

## Scope
- **In**: 仕様書、Model、FormRequest、Controller、Policy、UI、テスト
- **Out**: 他機能への影響（call_logs等との連携は後続フェーズ）

## DoD（Definition of Done）
- [x] 機能仕様書完成
- [x] 受入基準明確化
- [x] バリデーションルール定義
- [x] UI/UX要件整理

## Next Prompt（コピペ用）
```
依頼: F007-02: customersテーブルにDB制約（UNIQUE/INDEX）を追加
要件: email unique制約、phone部分インデックス、company_name検索インデックス
前提: Laravel 11 / PHP 8.2 / PSR-12 / 後方互換性考慮
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
```

## 進捗・決定事項
- 2025-11-08 作成: ブランチ作成、仕様策定開始
- 2025-11-08 実装: Model、FormRequest、Controller、Policy実装完了
- 2025-11-09 完成: UI実装、テスト完了、業界enum化、PHPDoc追加
- 2025-11-10 マージ: PR #1作成・マージ完了

## 最終実装内容
- ✅ Customer Model（リレーション、スコープ）
- ✅ FormRequest（Store/Update、バリデーション、正規化）
- ✅ CustomerController（7メソッド完全実装）
- ✅ CustomerPolicy（認証・認可）
- ✅ 4画面UI（index/create/show/edit）
- ✅ 17件Feature test（100%通過）
- ✅ 業界フィールドenum化
- ✅ PHPDoc完全対応
- ✅ UI改善（スタイル統一）

## 品質指標
- **テスト**: 17/17通過（52 assertions）
- **コード品質**: PSR-12準拠（Laravel Pint通過）
- **セキュリティ**: 認証・認可・CSRF対策完備
- **パフォーマンス**: N+1クエリ対策済み