<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Customer;
use App\Models\CallLog;
use Illuminate\Console\Command;

class CreatePaginationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-pagination-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create additional data for pagination testing (25+ records)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        
        if (!$user) {
            $this->error('ユーザーが存在しません。まずユーザーを作成してください。');
            return;
        }

        $this->info('ページネーション用のデータを作成中...');

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

        $totalCustomers = Customer::where('user_id', $user->id)->count();
        $totalCallLogs = CallLog::where('user_id', $user->id)->count();

        $this->info("✅ 追加で20件の顧客と20件の架電記録を作成しました");
        $this->info("📊 合計: 顧客{$totalCustomers}件、架電記録{$totalCallLogs}件");
        $this->info("🔄 ページネーションが表示されるはずです（20件/ページ）");
    }
}