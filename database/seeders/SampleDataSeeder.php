<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\CallLog;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テスト用ユーザーを作成またはダミーユーザーIDを使用
        $user = User::first(); // 最初のユーザーを使用
        
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
            ]);
        }

        // 1. 10件の顧客データ（ページネーションなし）
        $customers = [];
        $customerData = [
            ['株式会社サンプル', '田中 太郎', 'tanaka@sample.com', '03-1111-1111', 'IT', '関東', '高', '1', 'new'],
            ['合同会社テスト', '佐藤 花子', 'sato@test.com', '03-2222-2222', '製造業', '中部', '中', '2', 'contacted'],
            ['株式会社デモ', '鈴木 次郎', 'suzuki@demo.com', '03-3333-3333', 'その他', '近畿', '低', '3', 'qualified'],
            ['有限会社サンプル2', '高橋 美咲', 'takahashi@sample2.com', '03-4444-4444', '小売業', '関東', '高', '2', 'proposal'],
            ['株式会社テクノ', '伊藤 健太', 'ito@techno.com', '03-5555-5555', 'IT', '東北', '中', '1', 'negotiation'],
            ['エンタープライズ株式会社', '渡辺 由美', 'watanabe@enterprise.com', '03-6666-6666', '金融業', '関東', '高', '1', 'closed_won'],
            ['株式会社イノベーション', '山田 隆志', 'yamada@innovation.com', '03-7777-7777', '建設・不動産', '中国', '低', '4', 'new'],
            ['グローバル合同会社', '中村 麻衣', 'nakamura@global.com', '03-8888-8888', 'IT', '九州・沖縄', '中', '3', 'contacted'],
            ['株式会社フューチャー', '小林 大輔', 'kobayashi@future.com', '03-9999-9999', 'その他', '北海道', '高', '2', 'qualified'],
            ['ソリューション株式会社', '加藤 真理', 'kato@solution.com', '03-0000-0000', 'IT', '四国', '中', '1', 'proposal'],
        ];

        foreach ($customerData as $index => $data) {
            $customers[] = Customer::create([
                'user_id' => $user->id,
                'company_name' => $data[0],
                'contact_name' => $data[1],
                'email' => $data[2],
                'phone' => $data[3],
                'industry' => $data[4],
                'area' => $data[5],
                'temperature_rating' => $data[6],
                'priority' => $data[7],
                'status' => $data[8],
                'memo' => "サンプルの顧客データです。{$data[0]}は{$data[4]}の業界で、{$data[5]}地域に所在しています。",
                'created_at' => now()->subDays(10 - $index), // 作成日をずらす
                'updated_at' => now()->subDays(10 - $index),
            ]);
        }

        // 2. 10件の架電記録データ
        $callResults = ['connected', 'no_answer', 'busy', 'failed', 'voicemail'];
        
        foreach ($customers as $index => $customer) {
            $startedAt = now()->subDays(rand(1, 7))->subHours(rand(9, 17));
            $endedAt = $startedAt->copy()->addMinutes(rand(5, 45));
            
            CallLog::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'result' => $callResults[array_rand($callResults)],
                'notes' => "{$customer->company_name}への架電記録です。{$customer->contact_name}様と通話しました。",
                'created_at' => $startedAt,
                'updated_at' => $startedAt,
            ]);
        }

        $this->command->info('✅ 10件の顧客と10件の架電記録を作成しました（ページネーションなし）');
    }

    /**
     * 大量データ作成（ページネーション確認用）
     */
    public function createPaginationData(): void
    {
        $user = User::first();
        
        // 追加で20件の顧客（合計30件でページネーション表示）
        $additionalCustomers = Customer::factory(20)->create([
            'user_id' => $user->id,
        ]);

        // 追加で20件の架電記録（合計30件でページネーション表示）
        foreach ($additionalCustomers as $customer) {
            CallLog::factory()->create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
            ]);
        }

        $this->command->info('✅ 追加で20件の顧客と20件の架電記録を作成しました（ページネーション表示）');
    }
}