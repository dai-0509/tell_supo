<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テスト用ユーザーを作成（存在しない場合）
        $user = User::firstOrCreate([
            'email' => 'test@example.com'
        ], [
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);

        // サンプル顧客データ
        $customers = [
            [
                'company_name' => '株式会社テクノロジーソリューションズ',
                'contact_name' => '田中太郎',
                'email' => 'tanaka@techsol.co.jp',
                'phone' => '03-1234-5678',
                'industry' => 'IT',
                'temperature_rating' => 'A',
                'area' => '東京都',
                'status' => 'interested',
                'priority' => 'high',
                'memo' => 'AI導入に強い関心。来月商談予定。'
            ],
            [
                'company_name' => 'サンプル商事株式会社',
                'contact_name' => '佐藤花子',
                'email' => 'sato@sample-trading.co.jp', 
                'phone' => '06-2345-6789',
                'industry' => '商社',
                'temperature_rating' => 'B',
                'area' => '大阪府',
                'status' => 'contacted',
                'priority' => 'medium',
                'memo' => '既存システムの改善を検討中。'
            ],
            [
                'company_name' => 'デモ製造株式会社',
                'contact_name' => '鈴木一郎',
                'email' => 'suzuki@demo-mfg.co.jp',
                'phone' => '052-3456-7890',
                'industry' => '製造業',
                'temperature_rating' => 'C',
                'area' => '愛知県',
                'status' => 'new',
                'priority' => 'medium',
                'memo' => '工場の自動化に興味あり。'
            ],
            [
                'company_name' => 'ABC金融グループ',
                'contact_name' => '高橋美咲',
                'email' => 'takahashi@abc-finance.co.jp',
                'phone' => '03-4567-8901',
                'industry' => '金融',
                'temperature_rating' => 'A',
                'area' => '東京都',
                'status' => 'callback_scheduled',
                'priority' => 'high',
                'memo' => 'フィンテック分野での協業を検討。重要顧客。'
            ],
            [
                'company_name' => 'XYZ不動産株式会社',
                'contact_name' => '伊藤健二',
                'email' => 'ito@xyz-realestate.co.jp',
                'phone' => '045-5678-9012',
                'industry' => '不動産',
                'temperature_rating' => 'D',
                'area' => '神奈川県',
                'status' => 'contacted',
                'priority' => 'low',
                'memo' => 'プロップテック導入を検討中。'
            ],
            [
                'company_name' => 'グリーンエナジー株式会社',
                'contact_name' => '山田奈々子',
                'email' => 'yamada@green-energy.co.jp',
                'phone' => '092-6789-0123',
                'industry' => 'エネルギー',
                'temperature_rating' => 'B',
                'area' => '福岡県',
                'status' => 'interested',
                'priority' => 'high',
                'memo' => '再生可能エネルギーの管理システムに関心。'
            ],
            [
                'company_name' => 'フューチャーリテール',
                'contact_name' => '渡辺裕介',
                'email' => 'watanabe@future-retail.co.jp',
                'phone' => '011-7890-1234',
                'industry' => '小売業',
                'temperature_rating' => 'C',
                'area' => '北海道',
                'status' => 'new',
                'priority' => 'medium',
                'memo' => 'オムニチャネル戦略でのIT活用を模索。'
            ],
            [
                'company_name' => 'ヘルスケアイノベーション株式会社',
                'contact_name' => '中村真理',
                'email' => 'nakamura@healthcare-innovation.co.jp',
                'phone' => '075-8901-2345',
                'industry' => 'ヘルスケア',
                'temperature_rating' => 'A',
                'area' => '京都府',
                'status' => 'interested',
                'priority' => 'high',
                'memo' => '医療DXに積極的。すぐに提案資料が必要。'
            ],
            [
                'company_name' => 'スマートロジスティクス',
                'contact_name' => '木村大輔',
                'email' => 'kimura@smart-logistics.co.jp',
                'phone' => '022-9012-3456',
                'industry' => '物流',
                'temperature_rating' => 'E',
                'area' => '宮城県',
                'status' => 'not_interested',
                'priority' => 'low',
                'memo' => '現状は既存システムで満足している模様。'
            ],
            [
                'company_name' => 'エデュテック・ソリューションズ',
                'contact_name' => '林あゆみ',
                'email' => 'hayashi@edutech-solutions.co.jp',
                'phone' => '087-0123-4567',
                'industry' => '教育',
                'temperature_rating' => 'B',
                'area' => '香川県',
                'status' => 'contacted',
                'priority' => 'medium',
                'memo' => 'オンライン教育プラットフォームの構築を検討。'
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create(array_merge($customerData, ['user_id' => $user->id]));
        }
    }
}
