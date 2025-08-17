<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * パスワードのバリデーション強化
     */
    public function boot(): void
{
    Password::defaults(function () {
        // 本番は漏えいチェックあり、開発はネットワーク事情で外す例
        $base = Password::min(12)
            ->letters()     // 英字
            ->mixedCase()   // 大文字・小文字
            ->numbers()     // 数字
            ->symbols();    // 記号

        return app()->isProduction()
            ? $base->uncompromised(3) // 3回以上漏えいで拒否（閾値は任意）
            : $base;                   // 開発環境はオフでも可
    });

    // 2) レートリミッタ（login / register など必要分）
    RateLimiter::for('login', function (Request $request) {
        $byEmailAndIp = strtolower((string)$request->input('email')).'|'.$request->ip();
        return [
            Limit::perMinute(5)->by($byEmailAndIp)->response(function () {
                return back()->withErrors(['email' => __('auth.throttle')])->setStatusCode(429);
            }),
            Limit::perMinute(20)->by($request->ip()),
        ];
    });

    RateLimiter::for('register', function (Request $request) {
        return [
            Limit::perMinute(3)->by($request->ip())->response(function () {
                return back()->withErrors(['email' => __('auth.throttle')])->setStatusCode(429);
            }),
        ];
    });
}
}
