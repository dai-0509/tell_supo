# Development Setup - TellSupo（テルサポ）

**作成日**: 2025年7月31日  
**バージョン**: 2.0（Docker + MySQL統一版）  
**対象**: Laravel学習最適化 + 実務環境準備

-----

## 🎯 セットアップ方針（修正版）

### 実務志向の環境構築
- **Docker + Laravel Sail**: 現代的開発環境の実践学習
- **MySQL統一**: 開発・本番同一技術スタックで環境差異排除
- **実務レベルのツール構成**: チーム開発・本番運用を見据えた設定
- **M4 Mac最適化**: Apple Silicon専用の効率的セットアップ

### 学習効果とDocker経験の両立
- **Laravel極め**: Service Layer等の深掘り学習
- **Docker実務経験**: コンテナ開発環境の習得
- **MySQL実践**: 本番レベルのデータベース操作
- **Cursor AI連携**: 最新開発手法の実践

-----

## 📋 前提条件（M4 Mac向け）

### 必要なソフトウェア

#### 基盤環境（M4 Mac最適化）
- **Docker Desktop**: 4.20+ （Apple Silicon最適化版）
- **Git**: 2.30+ （Xcode Command Line Tools）
- **Cursor**: 最新版 （AI統合エディタ）

#### 確認コマンド
```bash
# M4 Mac アーキテクチャ確認
arch
# 結果: arm64

# Docker Desktop確認
docker --version
docker compose version

# Git確認
git --version
```

### M4 Mac事前準備

#### Homebrew（Apple Silicon版）
```bash
# Homebrew インストール（Apple Silicon用）
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# PATH設定確認（~/.zshrc または ~/.bash_profile）
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zshrc
source ~/.zshrc

# Homebrew動作確認
brew --version
```

#### Docker Desktop for Mac（Apple Silicon）
```bash
# Docker Desktop インストール
# https://www.docker.com/products/docker-desktop/ からApple Silicon版をダウンロード

# インストール後の動作確認
docker run hello-world

# M4向け最適化確認
docker info | grep Architecture
# 結果: aarch64
```

-----

## 🚀 ステップバイステップ環境構築

### Step 1: Laravel Sail プロジェクト作成（10分）

#### 1.1 プロジェクト初期化（Docker使用）
```bash
# TellSupoプロジェクト作成（Laravel Sailテンプレート）
curl -s "https://laravel.build/telsupo?with=mysql,redis" | bash

cd telsupo

# 権限設定（重要）
chmod +x vendor/bin/sail

# Sailエイリアス設定（推奨）
echo 'alias sail="[ -f sail ] && sh sail || sh vendor/bin/sail"' >> ~/.zshrc
source ~/.zshrc
```

#### 1.2 Docker環境起動
```bash
# Docker コンテナ起動（初回は時間がかかる）
sail up -d

# 起動確認
sail ps

# Laravel バージョン確認
sail artisan --version
# Laravel Framework 12.x.x
```

#### 1.3 基本設定
```bash
# アプリケーションキー生成
sail artisan key:generate

# .env設定確認・編集
cp .env.example .env

# 重要な.env設定
APP_NAME="TellSupo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# データベース設定（MySQL統一）
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=telsupo
DB_USERNAME=sail
DB_PASSWORD=password

# セッション設定（セキュリティレベルB）
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### Step 2: Laravel Breeze認証システム（15分）

#### 2.1 Laravel Breeze インストール
```bash
# Laravel Breeze追加
sail composer require laravel/breeze --dev

# Blade版Breezeインストール
sail artisan breeze:install blade

# 依存関係インストール・ビルド
sail npm install
sail npm run build
```

#### 2.2 認証強化設定（セキュリティレベルB）
```php
// config/auth.php（コンテナ内で編集）
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],

// パスワード強度設定
'password_timeout' => 10800, // 3時間
```

#### 2.3 セキュリティ設定強化
```php
// config/session.php
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => true,
'encrypt' => true,
'http_only' => true,
'same_site' => 'strict',

// config/hashing.php
'driver' => 'argon2id',

// config/database.php（MySQL最適化）
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
```

### Step 3: MySQL データベースセットアップ（10分）

#### 3.1 MySQL接続確認
```bash
# MySQLコンテナへの接続確認
sail mysql

# 接続後の確認コマンド
mysql> SHOW DATABASES;
mysql> USE telsupo;
mysql> SHOW TABLES;
mysql> exit;
```

#### 3.2 初期マイグレーション実行
```bash
# マイグレーション実行
sail artisan migrate

# 確認
sail artisan migrate:status

# MySQL内でのテーブル確認
sail mysql
mysql> USE telsupo;
mysql> SHOW TABLES;
mysql> DESCRIBE users;
mysql> exit;
```

#### 3.3 本番MySQL設定準備
```bash
# .env.production 準備
cp .env .env.production

# 本番用MySQL設定例
DB_CONNECTION=mysql
DB_HOST=your-production-host
DB_PORT=3306
DB_DATABASE=telsupo_production
DB_USERNAME=telsupo_prod_user
DB_PASSWORD=your-secure-password
```

### Step 4: フロントエンド環境（TailwindCSS + AlpineJS）（20分）

#### 4.1 TailwindCSS設定確認・最適化
```javascript
// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'tellsupo': {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
            },
        },
    },

    plugins: [forms],
};
```

#### 4.2 AlpineJS + Chart.js インストール
```bash
# Alpine.js追加
sail npm install alpinejs

# Chart.js追加（KPIダッシュボード用）
sail npm install chart.js

# その他UI強化ライブラリ
sail npm install @tailwindcss/typography
sail npm install @tailwindcss/aspect-ratio
```

#### 4.3 アセット設定
```javascript
// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// Chart.jsのデフォルト設定
Chart.defaults.font.family = 'Figtree, ui-sans-serif, system-ui';
Chart.defaults.color = '#374151';

// グローバル変数として設定
window.Alpine = Alpine;
window.Chart = Chart;

// TellSupo固有の初期化
document.addEventListener('DOMContentLoaded', function() {
    // KPIダッシュボード用の初期化コード
    console.log('TellSupo initialized with Alpine.js and Chart.js');
});

Alpine.start();
```

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* TellSupoカスタムコンポーネント */
@layer components {
    .btn-primary {
        @apply bg-tellsupo-600 hover:bg-tellsupo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out shadow-md hover:shadow-lg;
    }
    
    .btn-secondary {
        @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out;
    }
    
    .card {
        @apply bg-white shadow-lg rounded-lg p-6 border border-gray-200;
    }
    
    .form-input {
        @apply block w-full rounded-md border-gray-300 shadow-sm focus:border-tellsupo-500 focus:ring-tellsupo-500 transition duration-150 ease-in-out;
    }
    
    .stats-card {
        @apply bg-gradient-to-br from-tellsupo-500 to-tellsupo-600 text-white p-6 rounded-lg shadow-lg;
    }
    
    .kpi-progress {
        @apply w-full bg-gray-200 rounded-full h-2.5;
    }
}

/* ダークモード対応準備 */
@media (prefers-color-scheme: dark) {
    .card {
        @apply bg-gray-800 border-gray-700;
    }
}
```

### Step 5: Vite + Docker最適化（15分）

#### 5.1 Vite設定（Docker対応）
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost'
        },
        watch: {
            usePolling: true,
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                    charts: ['chart.js'],
                    tailwind: ['tailwindcss']
                }
            }
        }
    }
});
```

#### 5.2 アセットビルド確認
```bash
# 開発用ビルド
sail npm run dev

# ファイル監視（開発時）
sail npm run watch

# 本番用ビルド
sail npm run build

# ビルド結果確認
ls -la public/build/
```

#### 5.3 Docker Compose最適化
```yaml
# docker-compose.yml（Laravel Sailベース）のカスタマイズ例
version: '3'
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.2
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: sail-8.2/app
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - mysql
            - redis
    mysql:
        image: 'mysql/mysql-server:8.0'
        ports:
            - '${FORWARD_DB_PORT:-3306}:3306'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ROOT_HOST: "%"
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ALLOW_EMPTY_PASSWORD: 1
        volumes:
            - 'sail-mysql:/var/lib/mysql'
            - './vendor/laravel/sail/database/mysql/create-testing-database.sql:/docker-entrypoint-initdb.d/10-create-testing-database.sql'
        networks:
            - sail
        healthcheck:
            test: ["CMD", "mysqladmin", "ping", "-p${DB_PASSWORD}"]
            retries: 3
            timeout: 5s
    redis:
        image: 'redis:alpine'
        ports:
            - '${FORWARD_REDIS_PORT:-6379}:6379'
        volumes:
            - 'sail-redis:/data'
        networks:
            - sail
        healthcheck:
            test: ["CMD", "redis-cli", "ping"]
            retries: 3
            timeout: 5s
networks:
    sail:
        driver: bridge
volumes:
    sail-mysql:
        driver: local
    sail-redis:
        driver: local
```

-----

## 🔧 Docker + Laravel Sail開発コマンド集

### 日常的な開発コマンド

#### コンテナ管理
```bash
# 開発環境起動
sail up -d                      # バックグラウンド起動
sail up                         # フォアグラウンド起動（ログ表示）

# 開発環境停止
sail down                       # コンテナ停止・削除
sail stop                       # コンテナ停止（保持）

# コンテナ状態確認
sail ps                         # 起動中のコンテナ一覧
sail logs                       # ログ表示
sail logs -f laravel.test       # 特定サービスのログ監視
```

#### Laravel開発コマンド
```bash
# Artisanコマンド
sail artisan serve              # 不要（すでにサーバー起動済み）
sail artisan migrate            # マイグレーション実行
sail artisan migrate:fresh --seed # DB再作成＋シーダー実行
sail artisan queue:work         # キュー処理（将来使用）

# Composer操作
sail composer install          # 依存関係インストール
sail composer require package  # パッケージ追加
sail composer update          # 依存関係更新

# NPM操作
sail npm install              # パッケージインストール
sail npm run dev              # 開発ビルド
sail npm run watch            # ファイル監視
sail npm run build            # 本番ビルド
```

#### データベース操作
```bash
# MySQL接続
sail mysql                     # MySQLクライアント起動
sail mysql telsupo            # 特定DB指定

# ダンプ・リストア
sail exec mysql mysqldump -u sail -p telsupo > backup.sql
sail exec mysql mysql -u sail -p telsupo < backup.sql

# データベース初期化
sail artisan migrate:fresh
sail artisan db:seed
```

#### デバッグ・テスト
```bash
# ログ確認
sail logs laravel.test         # アプリケーションログ
sail logs mysql               # MySQLログ

# Tinker（Laravel REPL）
sail artisan tinker
>>> User::factory(10)->create();
>>> Customer::with('callLogs')->first();

# テスト実行
sail artisan test             # 全テスト実行
sail artisan test --parallel  # 並列テスト実行
sail artisan test --coverage  # カバレッジ確認
```

### モデル・マイグレーション（学習重点）

#### Laravel開発コマンド（Service Layer重視）
```bash
# モデル作成（フル装備）
sail artisan make:model Customer -mfs    # Model + Migration + Factory + Seeder
sail artisan make:model CallLog -a       # 全関連ファイル

# コントローラー作成
sail artisan make:controller CustomerController --resource
sail artisan make:controller Api/CustomerController --api

# サービス作成（手動）
mkdir -p app/Services
sail artisan make:class Services/CustomerService

# リクエスト作成
sail artisan make:request CustomerRequest
sail artisan make:request StoreCallLogRequest

# マイグレーション操作
sail artisan make:migration create_customers_table
sail artisan make:migration add_indexes_to_call_logs --table=call_logs
```

-----

## 🎯 Cursor エディタ設定（AI統合開発）

### Cursor推奨設定

#### 1. Laravel開発用拡張機能
```json
// .vscode/extensions.json（Cursorでも使用可能）
{
    "recommendations": [
        "bmewburn.vscode-intelephense-client",
        "onecentlin.laravel-blade",
        "open-southeners.laravel-pint",
        "bradlc.vscode-tailwindcss",
        "ms-azuretools.vscode-docker",
        "esbenp.prettier-vscode"
    ]
}
```

#### 2. Cursor設定（settings.json）
```json
{
    "php.validate.executablePath": "./vendor/bin/sail php",
    "php.suggest.basic": false,
    "intelephense.files.maxSize": 5000000,
    "intelephense.environment.includePaths": [
        "vendor/laravel/framework/src"
    ],
    "blade.format.enable": true,
    "tailwindCSS.includeLanguages": {
        "blade": "html"
    },
    "tailwindCSS.experimental.classRegex": [
        ["class:\\s*['\"]([^'\"]*)['\"]", "['\"]([^'\"]*)['\"]"]
    ],
    "emmet.includeLanguages": {
        "blade": "html"
    },
    "cursor.ai.enableCodeGeneration": true,
    "cursor.ai.enableCompletion": true
}
```

#### 3. Cursor AI活用方法
```bash
# Laravel Service Layer実装例
# Cursorでのプロンプト例：
"Laravel 12.0でCustomerServiceクラスを作成して。
Repository Pattern不使用、Eloquent直接使用。
create, update, delete, findByIdメソッド実装。
依存性注入対応。トランザクション使用。"

# Blade + TailwindCSS実装例
"Laravel Bladeで顧客一覧画面作成。
TailwindCSSでモダンなカード表示。
AlpineJSで検索・フィルタ機能。
レスポンシブ対応。"
```

-----

## 🛡️ セキュリティ設定（レベルB・Docker対応）

### Docker環境でのセキュリティ

#### 1. コンテナセキュリティ
```bash
# Docker セキュリティスキャン
docker scout cves

# 最新イメージ使用確認
sail pull
sail build --no-cache
```

#### 2. MySQL セキュリティ強化
```sql
-- MySQL設定強化（root以外でのアクセス）
-- .env設定でのユーザー権限制限
DB_USERNAME=sail
DB_PASSWORD=password  # 本番では強力なパスワード使用

-- 本番環境用MySQL設定例
CREATE USER 'telsupo_app'@'%' IDENTIFIED BY 'your-strong-password';
GRANT SELECT, INSERT, UPDATE, DELETE ON telsupo.* TO 'telsupo_app'@'%';
FLUSH PRIVILEGES;
```

#### 3. Laravel セキュリティミドルウェア
```php
// app/Http/Middleware/SecurityHeaders.php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // セキュリティヘッダー設定
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}

// app/Http/Kernel.php に追加
protected $middleware = [
    // ...
    \App\Http\Middleware\SecurityHeaders::class,
];
```

#### 4. 暗号化・ログ設定
```php
// config/app.php
'cipher' => 'AES-256-CBC',

// config/logging.php（Docker対応）
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'stdout'],
        'ignore_exceptions' => false,
    ],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'stdout' => [
        'driver' => 'monolog',
        'handler' => StreamHandler::class,
        'formatter' => env('LOG_STDERR_FORMATTER'),
        'with' => [
            'stream' => 'php://stdout',
        ],
    ],
    'activity' => [
        'driver' => 'daily',
        'path' => storage_path('logs/activity.log'),
        'level' => 'info',
        'days' => 90,
    ],
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 365,
    ],
],
```

-----

## 🧪 テスト環境設定（Docker + MySQL）

### Docker対応テスト設定

#### 1. テスト用データベース
```php
// phpunit.xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </coverage>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="mysql"/>
        <env name="DB_DATABASE" value="testing"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

#### 2. テスト実行環境
```bash
# テスト用データベース作成
sail mysql
mysql> CREATE DATABASE testing;
mysql> exit;

# テスト実行
sail artisan test
sail artisan test --parallel --processes=4
sail artisan test --coverage --min=80

# 特定テストの実行
sail artisan test --filter=CustomerTest
sail artisan test tests/Feature/CustomerTest.php
```

#### 3. Docker内でのテストデータ管理
```php
// tests/TestCase.php
<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Docker環境でのテスト最適化
        $this->artisan('config:clear');
        $this->artisan('cache:clear');
    }
}
```

-----

## 🚀 動作確認手順（Docker環境）

### Step 1: Docker環境確認
```bash
# 1. コンテナ起動確認
sail up -d
sail ps

# 結果例:
# Name                    State    Ports
# telsupo_laravel.test_1  Up       0.0.0.0:80->80/tcp, :::80->80/tcp, 0.0.0.0:5173->5173/tcp, :::5173->5173/tcp
# telsupo_mysql_1         Up       0.0.0.0:3306->3306/tcp, :::3306->3306/tcp, 33060/tcp
# telsupo_redis_1         Up       0.0.0.0:6379->6379/tcp, :::6379->6379/tcp

# 2. アプリケーション確認
curl http://localhost
# → Laravelウェルカムページが表示されるか確認
```

### Step 2: MySQL接続確認
```bash
# 1. MySQL接続テスト
sail mysql
mysql> SELECT VERSION();
mysql> SHOW DATABASES;
mysql> USE telsupo;
mysql> SHOW TABLES;
mysql> exit;

# 2. Laravel経由でのDB接続確認
sail artisan tinker
>>> DB::connection()->getPdo();
>>> User::count();
>>> exit;
```

### Step 3: 認証機能確認
```bash
# 1. マイグレーション実行
sail artisan migrate

# 2. 認証画面確認
open http://localhost/register
open http://localhost/login

# 3. ユーザー登録・ログインテスト
# → ブラウザで実際に操作確認
```

### Step 4: フロントエンド確認
```bash
# 1. アセットビルド確認
sail npm run dev

# 2. TailwindCSS動作確認
# → http://localhost でスタイルが適用されているか確認

# 3. AlpineJS動作確認
# → ブラウザの開発者ツールでAlpineが読み込まれているか確認
```

### Step 5: 本番準備確認
```bash
# 1. 本番ビルド確認
sail npm run build

# 2. 最適化確認
sail artisan config:cache
sail artisan route:cache
sail artisan view:cache

# 3. セキュリティ確認
sail artisan tinker
>>> encrypt('test data');
>>> decrypt($result);
>>> exit;
```

-----

## 🔧 よくあるトラブルと解決方法（Docker版）

### Docker関連トラブル

#### 1. コンテナ起動失敗
```bash
# エラー: ポートが既に使用中
# 解決: ポート使用状況確認・変更
lsof -i :80
lsof -i :3306

# .env でポート変更
APP_PORT=8080
FORWARD_DB_PORT=3307

# 再起動
sail down
sail up -d
```

#### 2. M4 Mac固有の問題
```bash
# エラー: platform linux/amd64 requested
# 解決: Apple Silicon用イメージ使用
export DOCKER_DEFAULT_PLATFORM=linux/amd64

# または docker-compose.yml で明示的に指定
platform: linux/amd64  # 各サービスに追加
```

#### 3. MySQL接続エラー
```bash
# エラー: Connection refused
# 解決: MySQLコンテナの起動待ち
sail down
sail up -d mysql
sleep 30  # MySQL起動完了まで待機
sail up -d
```

### Laravel + Docker統合トラブル

#### 1. アセットビルドエラー
```bash
# エラー: Vite manifest not found
# 解決: ホットリロード設定確認
# vite.config.js の server.host: '0.0.0.0' 確認

# キャッシュクリア後再ビルド
sail npm run build
sail artisan view:clear
```

#### 2. ファイル権限エラー
```bash
# エラー: Permission denied
# 解決: Laravel Sailユーザー権限調整
sail artisan storage:link
sudo chown -R $USER:$USER storage/
sudo chown -R $USER:$USER bootstrap/cache/
```

#### 3. MySQL文字化け
```bash
# エラー: 文字エンコーディング問題
# 解決: MySQL設定確認
sail mysql
mysql> SHOW VARIABLES LIKE 'character_set%';
mysql> SHOW VARIABLES LIKE 'collation%';

# すべてutf8mb4であることを確認
```

### パフォーマンス最適化

#### 1. Docker起動速度改善
```bash
# .dockerignore作成
echo "
node_modules
.git
.env.local
.env.production
storage/logs
tests
" > .dockerignore
```

#### 2. M4 Mac最適化
```bash
# Docker Desktop設定
# Settings > General > "Use Virtualization framework" チェック
# Settings > General > "Use Rosetta for x86/amd64 emulation" チェック

# リソース割り当て最適化
# Settings > Resources > Advanced
# CPUs: 4-6 cores
# Memory: 8-12 GB
# Swap: 2 GB
```

-----

## 📋 環境構築チェックリスト（Docker版）

### 基盤環境確認
- [ ] M4 Mac アーキテクチャ確認（arm64）
- [ ] Docker Desktop for Mac（Apple Silicon版）インストール
- [ ] Homebrew（Apple Silicon版）インストール
- [ ] Cursor エディタ インストール・設定

### Laravel Sail環境確認
- [ ] Laravel Sail プロジェクト作成完了
- [ ] Docker コンテナ起動確認（laravel.test, mysql, redis）
- [ ] Laravel 12.0 動作確認
- [ ] Laravel Breeze インストール完了

### MySQL環境確認
- [ ] MySQL コンテナ動作確認
- [ ] データベース接続確認（sail mysql）
- [ ] 初回マイグレーション実行完了
- [ ] 本番MySQL設定準備完了

### フロントエンド確認
- [ ] TailwindCSS セットアップ完了
- [ ] AlpineJS インストール完了
- [ ] Chart.js インストール完了
- [ ] Vite ビルド動作確認（sail npm run dev）

### セキュリティ確認（レベルB）
- [ ] セッション設定（2時間タイムアウト）
- [ ] パスワードハッシュ化（argon2id）
- [ ] セキュリティヘッダー設定
- [ ] データ暗号化機能確認

### 開発効率確認
- [ ] Cursor AI連携設定
- [ ] Laravel開発用拡張機能インストール
- [ ] Docker開発コマンド動作確認
- [ ] テスト環境動作確認

-----

## 🎯 次のステップ（Claude Code実装準備）

### Phase 1実装準備完了

この Docker + MySQL統一環境により、以下の実装が最適化されます：

#### 1. Database Design実装
```bash
# Claude Codeでの実装指示例
sail artisan make:migration create_customers_table
sail artisan make:model Customer -fs
sail artisan make:migration create_call_logs_table
sail artisan make:model CallLog -fs
# ... 全8テーブル実装
```

#### 2. Service Layer実装
```bash
# Laravel学習重点の実装
mkdir -p app/Services
# CustomerService, CallLogService, KpiCalculationService実装
```

#### 3. セキュリティ実装
```bash
# encrypted cast実装
# 監査ログ機能実装
# セキュリティミドルウェア実装
```

### Docker開発での学習効果

#### 1. 実務レベルの開発環境経験
- **チーム開発**: 環境統一による協働開発体験
- **本番準備**: Production-Readyな設定経験
- **コンテナ技術**: 現代開発の標準スキル習得

#### 2. MySQL実践経験
- **本番同等環境**: 開発・本番差異のないDB操作
- **パフォーマンス**: インデックス・クエリ最適化実践
- **セキュリティ**: 企業レベルのDB運用経験

### Claude Code連携の最適化

#### 1. 明確な実装指示
- **技術スタック**: Docker + Laravel 12.0 + MySQL
- **開発環境**: 完全セットアップ済み
- **セキュリティ**: レベルB準拠の具体的実装方針

#### 2. 学習最適化
- **Laravel極め**: Service Layer中心の実装学習
- **Docker実務**: コンテナ開発環境の習得
- **実務経験**: 企業標準レベルの開発体験

-----

## 📊 技術スタック最終確認

### 確定した構成
```
【開発環境】
- Docker + Laravel Sail
- MySQL 8.0（開発・本番統一）
- Redis（キャッシュ・セッション）

【バックエンド】
- Laravel 12.0 + PHP 8.2
- Laravel Breeze（認証）
- Service Layer Pattern

【フロントエンド】
- Blade Templates
- TailwindCSS + AlpineJS
- Chart.js + Vite

【開発ツール】
- Cursor（AI統合エディタ）
- M4 Mac最適化設定
- Docker Desktop for Mac

【セキュリティ】
- レベルB（企業標準）
- MySQL暗号化対応
- セキュリティヘッダー
- 監査ログ機能
```

### 学習目標の実現
- **Laravel極め戦略**: ✅ 実務レベル環境完成
- **Docker経験積み**: ✅ コンテナ開発環境構築
- **実務志向**: ✅ 本番レベル技術スタック
- **AI連携開発**: ✅ Cursor + Claude Code最適化

-----

**この Development Setup（Docker + MySQL統一版）により、TellSupoプロジェクトの実装環境が実務レベルで完成しました。Laravel学習とDocker経験を両立させながら、Claude Codeとの協働開発で最高の学習効果を実現する準備が整っています。**

**🚀 いよいよMVP実装開始の準備完了です！**