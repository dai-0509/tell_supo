<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Customer;
use App\Models\CallLog;
use Illuminate\Console\Command;

class CreateUserTwoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-user-two-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create 25+ customers and call logs for user ID 2 (pagination testing)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ユーザーID 2を確認または作成
        $user = User::find(2);
        
        if (!$user) {
            $user = User::factory()->create([
                'id' => 2,
                'name' => 'Test User 2',
                'email' => 'user2@example.com',
            ]);
            $this->info('✅ ユーザーID 2を作成しました');
        }

        $this->info('ユーザーID 2用のデータを作成中...');

        // 25件の顧客を作成（ページネーション表示用）
        $customers = Customer::factory(25)->create([
            'user_id' => $user->id,
        ]);

        // 各顧客に対して架電記録を作成
        foreach ($customers as $customer) {
            CallLog::factory()->create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
            ]);
        }

        $totalCustomers = Customer::where('user_id', $user->id)->count();
        $totalCallLogs = CallLog::where('user_id', $user->id)->count();

        $this->info("✅ ユーザーID 2用に25件の顧客と25件の架電記録を作成しました");
        $this->info("📊 合計: 顧客{$totalCustomers}件、架電記録{$totalCallLogs}件");
        $this->info("🔄 ページネーションが表示されます（20件/ページ）");
    }
}
