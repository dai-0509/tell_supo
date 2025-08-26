<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TelSupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テストユーザーを作成（存在しない場合のみ）
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'test@telsupo.com'],
            [
                'name' => 'テスト太郎',
                'role' => 'operator',
                'password' => 'password',
            ]
        );

        // 管理者ユーザーを作成（存在しない場合のみ）
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@telsupo.com'],
            [
                'name' => '管理者',
                'role' => 'admin',
                'password' => 'password',
            ]
        );

        // 顧客データを作成（ユーザーに紐付け）
        $customers = \App\Models\Customer::factory(20)->create([
            'user_id' => $user->id,
        ]);

        // 管理者用の顧客も少し作成
        \App\Models\Customer::factory(5)->create([
            'user_id' => $admin->id,
        ]);

        // 架電履歴を作成
        foreach ($customers as $customer) {
            // 各顧客に対して1-5回の架電履歴を作成
            \App\Models\CallLog::factory(fake()->numberBetween(1, 5))->create([
                'customer_id' => $customer->id,
                'user_id' => $user->id,
            ]);
        }

        // 今日の架電ログを追加で作成
        \App\Models\CallLog::factory(5)->today()->create([
            'customer_id' => fake()->randomElement($customers)->id,
            'user_id' => $user->id,
        ]);

        // アポ獲得の架電ログを作成
        \App\Models\CallLog::factory(3)->appointment()->create([
            'customer_id' => fake()->randomElement($customers)->id,
            'user_id' => $user->id,
        ]);

        // KPI目標を作成
        \App\Models\KpiTarget::factory()->weekly()->create([
            'user_id' => $user->id,
        ]);

        \App\Models\KpiTarget::factory()->monthly()->create([
            'user_id' => $user->id,
        ]);

        // 管理者用のKPI目標も作成
        \App\Models\KpiTarget::factory()->weekly()->create([
            'user_id' => $admin->id,
        ]);

        // スクリプトを作成
        \App\Models\Script::factory()->opening()->create([
            'user_id' => $user->id,
        ]);

        \App\Models\Script::factory()->closing()->create([
            'user_id' => $user->id,
        ]);

        // 追加でランダムなスクリプトを作成
        \App\Models\Script::factory(3)->create([
            'user_id' => $user->id,
        ]);

        // 管理者用のスクリプト
        \App\Models\Script::factory(2)->create([
            'user_id' => $admin->id,
        ]);

        $this->command->info('TelSupoのテストデータが正常に作成されました！');
        $this->command->info("テストユーザー: {$user->email}");
        $this->command->info("管理者: {$admin->email}");
        $this->command->info("顧客数: " . \App\Models\Customer::count());
        $this->command->info("架電履歴: " . \App\Models\CallLog::count());
        $this->command->info("KPI目標: " . \App\Models\KpiTarget::count());
        $this->command->info("スクリプト: " . \App\Models\Script::count());
    }
}
