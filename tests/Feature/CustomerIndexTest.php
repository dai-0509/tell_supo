<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_顧客一覧画面を表示できる(): void
    {
        $customers = Customer::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertViewIs('pages.customers.index')
            ->assertSee($customers[0]->company_name)
            ->assertSee($customers[1]->company_name)
            ->assertSee($customers[2]->company_name);
    }

    public function test_他のユーザーの顧客は表示されない(): void
    {
        $otherUser = User::factory()->create();
        $myCustomer = Customer::factory()->create(['user_id' => $this->user->id]);
        $otherCustomer = Customer::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee($myCustomer->company_name)
            ->assertDontSee($otherCustomer->company_name);
    }

    public function test_顧客が0件の場合に空状態を表示する(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('登録中の顧客データがありません')
            ->assertSee('新規登録');
    }

    public function test_ページネーションが正常に動作する(): void
    {
        // 25件作成（20件を超える）
        Customer::factory(25)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        // 20件まで表示される
        $response->assertStatus(200)
            ->assertViewHas('customers', function ($customers) {
                return $customers->count() === 20;
            });

        // 2ページ目にアクセス
        $page2Response = $this->actingAs($this->user)
            ->get(route('customers.index', ['page' => 2]));

        $page2Response->assertStatus(200)
            ->assertViewHas('customers', function ($customers) {
                return $customers->count() === 5; // 残り5件
            });
    }

    public function test_空状態時に新規登録ボタンが表示される(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('登録中の顧客データがありません')
            ->assertSee('新規登録') // 空状態内のボタン
            ->assertSee('+ 新規登録') // ナビゲーション内のボタン
            ->assertSee(route('customers.create'));
    }

    public function test_ナビゲーションに新規登録ボタンが常に表示される(): void
    {
        Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertDontSee('登録中の顧客データがありません')
            ->assertSee('+ 新規登録'); // ナビゲーションに表示される
    }

    public function test_顧客の基本情報が一覧に表示される(): void
    {
        $customer = Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'テスト株式会社',
            'contact_name' => '田中太郎',
            'email' => 'test@example.com',
            'phone' => '09012345678',
            'temperature_rating' => 'A',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('テスト株式会社')
            ->assertSee('田中太郎')
            ->assertSee('test@example.com')
            ->assertSee('09012345678')
            ->assertSee('A')
            ->assertSee('active');
    }

    public function test_操作ボタンが表示される(): void
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('詳細')
            ->assertSee('編集')
            ->assertSee('削除')
            ->assertSee(route('customers.show', $customer))
            ->assertSee(route('customers.edit', $customer));
    }

    public function test_温度感のバッジが正しく表示される(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'temperature_rating' => 'A',
        ]);

        Customer::factory()->create([
            'user_id' => $this->user->id,
            'temperature_rating' => 'B',
        ]);

        Customer::factory()->create([
            'user_id' => $this->user->id,
            'temperature_rating' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('bg-red-100') // A評価
            ->assertSee('bg-orange-100'); // B評価
    }

    public function test_最新の顧客から順番に表示される(): void
    {
        $oldCustomer = Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '古い会社',
            'created_at' => now()->subDays(2),
        ]);

        $newCustomer = Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '新しい会社',
            'created_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $content = $response->getContent();
        $newPos = strpos($content, '新しい会社');
        $oldPos = strpos($content, '古い会社');

        // 新しい会社が先に表示される
        $this->assertTrue($newPos < $oldPos);
    }

    public function test_未認証ユーザーはリダイレクトされる(): void
    {
        $response = $this->get(route('customers.index'));

        $response->assertRedirect('/login');
    }

    public function test_成功メッセージが表示される(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['success' => '顧客を登録しました'])
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('顧客を登録しました');
    }
}
