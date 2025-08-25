# CLAUDE.md - テルサポプロジェクト（簡潔版）

Claude Code用の開発ガイド

## プロジェクト概要
**TellSupo**: テレアポ業務効率化Webダッシュボード  
**技術スタック**: Laravel 12.0 + TailwindCSS + AlpineJS + Chart.js  
**コンセプト**: "架電業務を見える化し、効率的なテレアポ営業を支援"

## 基本構成
```
telsupo/
├── app/Http/Controllers/     # コントローラー層
├── app/Models/              # Eloquent Models
├── app/Services/            # ビジネスロジック層
├── resources/views/         # Blade Templates
├── resources/js/           # Vanilla JS + AlpineJS
├── resources/css/          # TailwindCSS
└── database/migrations/    # データベース定義
```

## 開発コマンド
```bash
# 開発環境
php artisan serve
npm run dev

# データベース
php artisan migrate:fresh --seed

# テスト
php artisan test
```

## コアエンティティ
- **Customer**: 顧客情報
- **CallLog**: 架電履歴
- **KpiTarget**: KPI目標設定
- **Script**: 架電スクリプト
- **User**: システム利用者

## 設計方針
1. **MVCアーキテクチャ + Service Layer**
2. **直感的なUI（3クリック以内での操作完了）**
3. **リアルタイム更新**
4. **データ整合性重視**

## 主要機能（優先度順）
### Phase 1（高）
- 認証システム（Laravel Breeze）
- 架電数カウンター
- ダッシュボード画面
- 顧客管理（一覧、追加、編集）
- 架電履歴管理
- KPI管理

### Phase 2（中）
- 検索・フィルタリング
- Chart.jsグラフ表示
- CSV入出力
- スクリプト管理

### Phase 3（低）
- パフォーマンス最適化
- SEO対策
- 本番デプロイ

## 注意点
- N+1問題対策：必ずEager Loading使用
- セキュリティ：CSRF、XSS対策必須
- テスト：優先度「高」機能は必ずテスト作成
- DB変更：マイグレーション経由必須