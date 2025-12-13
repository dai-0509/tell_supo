<?php

namespace Database\Seeders;

use App\Models\CallLog;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CallLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // user_id = 2の顧客を取得
        $customers = Customer::where('user_id', 2)->get();

        if ($customers->isEmpty()) {
            $this->command->info('顧客データが存在しません。CustomerSeederを先に実行してください。');
            return;
        }

        $results = ['通話成功', '受けブロ', '会話のみ', '見込みあり'];
        $call_logs = [];

        foreach ($customers as $customer) {
            // 各顧客に対して1〜5件の架電記録を作成
            $call_count = fake()->numberBetween(1, 5);
            
            for ($i = 0; $i < $call_count; $i++) {
                $started_at = fake()->dateTimeBetween('-2 months', 'now');
                $result = fake()->randomElement($results);
                
                // 通話成功と見込みありの場合は終了時間を設定
                $ended_at = null;
                if (in_array($result, ['通話成功', '見込みあり'])) {
                    $ended_at = Carbon::parse($started_at)->addMinutes(fake()->numberBetween(2, 30));
                }

                $call_logs[] = [
                    'user_id' => 2,
                    'customer_id' => $customer->id,
                    'started_at' => $started_at,
                    'ended_at' => $ended_at,
                    'result' => $result,
                    'notes' => $this->generateNotes($result, $customer->company_name),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // バッチ挿入
        CallLog::insert($call_logs);

        $this->command->info('架電記録のダミーデータを' . count($call_logs) . '件作成しました。');
    }

    private function generateNotes(string $result, string $company_name): string
    {
        return match ($result) {
            '通話成功' => fake()->randomElement([
                "{$company_name}の担当者と良好な会話ができた。サービスに興味を示している。",
                "担当者は親切で、詳細な説明を聞いてくれた。次回アポ取りを検討。",
                "現在のシステムに課題を感じており、解決策を探している。",
                "予算感も含めて前向きに検討したいとのこと。提案書送付予定。"
            ]),
            '受けブロ' => fake()->randomElement([
                "電話に出てもらえなかった。時間を変えて再コール予定。",
                "担当者が不在。会議中とのことで後日再連絡。",
                "忙しいとのことで話を聞いてもらえなかった。",
                "営業電話お断りと言われ通話終了。"
            ]),
            '会話のみ' => fake()->randomElement([
                "丁寧に対応してもらったが、現在は検討していないとのこと。",
                "サービス概要は理解してもらえたが、タイミングが合わない。",
                "今は必要ないが、将来的には検討したいとのこと。",
                "現在利用中のサービスで満足しているため、切り替え予定なし。"
            ]),
            '見込みあり' => fake()->randomElement([
                "サービスに強い関心を示してくれた。詳細資料を送付予定。",
                "現在のシステムに不満があり、乗り換えを検討中。来週打ち合わせ予定。",
                "予算も確保済みで、導入時期も具体的。非常に有望な案件。",
                "決裁者紹介の約束を取り付けた。来月商談予定。"
            ])
        };
    }
}