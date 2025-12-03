<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
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

    public function test_顧客登録フォームを表示できる(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('customers.create'));

        $response->assertStatus(200)
            ->assertViewIs('pages.customers.create')
            ->assertSee('顧客登録');
    }

    public function test_新規顧客を登録できる(): void
    {
        $customerData = [
            'company_name' => 'テスト株式会社',
            'contact_name' => '田中太郎',
            'email' => 'test@example.com',
            'phone' => '090-1234-5678',
            'industry' => 'IT',
            'temperature_rating' => 'A',
            'area' => '東京',
            'status' => '見込みあり',
            'priority' => 3,
            'memo' => 'テストメモ',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('customers.store'), $customerData);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'company_name' => 'テスト株式会社',
            'contact_name' => '田中太郎',
            'email' => 'test@example.com',
            'phone' => '09012345678', // ハイフン除去確認
            'user_id' => $this->user->id,
        ]);
    }

    public function test_会社名が必須でバリデーションエラーになる(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('customers.store'), [
                'contact_name' => '田中太郎',
            ]);

        $response->assertSessionHasErrors('company_name');
        $this->assertDatabaseMissing('customers', [
            'contact_name' => '田中太郎',
        ]);
    }

    public function test_同一ユーザー内で会社名重複時にバリデーションエラーになる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '重複会社',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('customers.store'), [
                'company_name' => '重複会社',
            ]);

        $response->assertSessionHasErrors('company_name');
    }

    public function test_異なるユーザーなら同じ会社名で登録できる(): void
    {
        $otherUser = User::factory()->create();
        Customer::factory()->create([
            'user_id' => $otherUser->id,
            'company_name' => '同名会社',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('customers.store'), [
                'company_name' => '同名会社',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'user_id' => $this->user->id,
            'company_name' => '同名会社',
        ]);
    }

    public function test_電話番号の正規化が動作する(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('customers.store'), [
                'company_name' => 'テスト会社',
                'phone' => '090-1234-5678',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'company_name' => 'テスト会社',
            'phone' => '09012345678',
        ]);
    }

    public function test_顧客詳細画面を表示できる(): void
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.show', $customer));

        $response->assertStatus(200)
            ->assertViewIs('pages.customers.show')
            ->assertSee($customer->company_name);
    }

    public function test_他のユーザーの顧客詳細は閲覧できない(): void
    {
        $otherUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.show', $customer));

        $response->assertStatus(403);
    }

    public function test_顧客編集フォームを表示できる(): void
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.edit', $customer));

        $response->assertStatus(200)
            ->assertViewIs('pages.customers.edit')
            ->assertSee($customer->company_name);
    }

    public function test_顧客情報を更新できる(): void
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'company_name' => '更新後会社名',
            'contact_name' => '更新後担当者',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('customers.update', $customer), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'company_name' => '更新後会社名',
            'contact_name' => '更新後担当者',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_更新時は自分のレコードは重複チェックから除外される(): void
    {
        $customer = Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '既存会社名',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('customers.update', $customer), [
                'company_name' => '既存会社名', // 同じ名前のまま更新
                'contact_name' => '更新後担当者',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'company_name' => '既存会社名',
            'contact_name' => '更新後担当者',
        ]);
    }

    public function test_他のユーザーの顧客は更新できない(): void
    {
        $otherUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->put(route('customers.update', $customer), [
                'company_name' => '不正更新',
            ]);

        $response->assertStatus(403);
    }

    public function test_顧客を削除できる(): void
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('customers.destroy', $customer));

        $response->assertRedirect();
        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_他のユーザーの顧客は削除できない(): void
    {
        $otherUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('customers.destroy', $customer));

        $response->assertStatus(403);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_未認証ユーザーは顧客機能にアクセスできない(): void
    {
        $customer = Customer::factory()->create();

        $this->get(route('customers.index'))->assertRedirect('/login');
        $this->get(route('customers.create'))->assertRedirect('/login');
        $this->get(route('customers.show', $customer))->assertRedirect('/login');
        $this->get(route('customers.edit', $customer))->assertRedirect('/login');
        $this->post(route('customers.store'), [])->assertRedirect('/login');
        $this->put(route('customers.update', $customer), [])->assertRedirect('/login');
        $this->delete(route('customers.destroy', $customer))->assertRedirect('/login');
    }
}
