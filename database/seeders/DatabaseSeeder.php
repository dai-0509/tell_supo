<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // user_id = 2のユーザーを作成
        User::factory()->create([
            'name' => '営業太郎',
            'email' => 'sales@example.com',
        ]);

        $this->call([
            CustomerSeeder::class,
            CallLogSeeder::class,
        ]);
    }
}
