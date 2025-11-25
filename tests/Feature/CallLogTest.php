<?php

namespace Tests\Feature;

use App\Models\CallLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 架電記録機能のFeatureテスト
 *
 * 架電記録のCRUD操作、権限制御、バリデーションをテストするFeatureテストクラス
 * データベースリフレッシュ、ユーザー認証、ポリシー制御を含む
 */
class CallLogTest extends TestCase
{
    /**
     * RefreshDatabaseトレイト
     *
     * 各テスト前にデータベースをクリーンアップしファクトリを使用したテストデータ作成を可能にする
     */
    use RefreshDatabase;

    protected User $user;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_can_view_call_logs_index(): void
    {
        $callLog = CallLog::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('call-logs.index'));

        $response->assertOk();
        $response->assertSee($this->customer->company_name);
        $response->assertSee($callLog->result_label);
    }

    public function test_can_view_call_log_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('call-logs.create'));

        $response->assertOk();
        $response->assertSee('架電記録登録');
        $response->assertSee($this->customer->company_name);
    }

    public function test_can_create_call_log(): void
    {
        $callLogData = [
            'customer_id' => $this->customer->id,
            'started_at' => now()->subHour()->format('Y-m-d\TH:i'),
            'ended_at' => now()->format('Y-m-d\TH:i'),
            'result' => 'connected',
            'notes' => 'テスト通話記録',
        ];

        $response = $this->actingAs($this->user)->post(route('call-logs.store'), $callLogData);

        $response->assertStatus(302);

        $this->assertDatabaseHas('call_logs', [
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'result' => 'connected',
            'notes' => 'テスト通話記録',
        ]);

        $callLog = CallLog::where('user_id', $this->user->id)->first();
        $response->assertRedirect(route('call-logs.show', $callLog));
        $response->assertSessionHas('success');
    }

    public function test_can_view_call_log_details(): void
    {
        $callLog = CallLog::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'notes' => 'テスト通話詳細',
        ]);

        $response = $this->actingAs($this->user)->get(route('call-logs.show', $callLog));

        $response->assertOk();
        $response->assertSee($this->customer->company_name);
        $response->assertSee('テスト通話詳細');
        $response->assertSee($callLog->result_label);
    }

    public function test_can_view_call_log_edit_form(): void
    {
        $callLog = CallLog::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('call-logs.edit', $callLog));

        $response->assertOk();
        $response->assertSee('架電記録編集');
        $response->assertSee($this->customer->company_name);
    }

    public function test_can_update_call_log(): void
    {
        $callLog = CallLog::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'result' => 'no_answer',
            'notes' => '元のメモ',
        ]);

        $updateData = [
            'customer_id' => $this->customer->id,
            'started_at' => $callLog->started_at->format('Y-m-d\TH:i'),
            'ended_at' => now()->format('Y-m-d\TH:i'),
            'result' => 'connected',
            'notes' => '更新されたメモ',
        ];

        $response = $this->actingAs($this->user)->put(route('call-logs.update', $callLog), $updateData);

        $this->assertDatabaseHas('call_logs', [
            'id' => $callLog->id,
            'result' => 'connected',
            'notes' => '更新されたメモ',
        ]);

        $response->assertRedirect(route('call-logs.show', $callLog));
        $response->assertSessionHas('success');
    }

    public function test_can_delete_call_log(): void
    {
        $callLog = CallLog::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('call-logs.destroy', $callLog));

        $this->assertDatabaseMissing('call_logs', ['id' => $callLog->id]);
        $response->assertRedirect(route('call-logs.index'));
        $response->assertSessionHas('success');
    }

    public function test_user_cannot_access_other_users_call_logs(): void
    {
        $otherUser = User::factory()->create();
        $otherCustomer = Customer::factory()->create(['user_id' => $otherUser->id]);
        $otherCallLog = CallLog::factory()->create([
            'user_id' => $otherUser->id,
            'customer_id' => $otherCustomer->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('call-logs.show', $otherCallLog))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->get(route('call-logs.edit', $otherCallLog))
            ->assertForbidden();

        $updateData = [
            'customer_id' => $this->customer->id,
            'started_at' => now()->subHour()->format('Y-m-d\TH:i'),
            'result' => 'connected',
        ];

        $this->actingAs($this->user)
            ->put(route('call-logs.update', $otherCallLog), $updateData)
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('call-logs.destroy', $otherCallLog))
            ->assertForbidden();
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('call-logs.store'), []);

        $response->assertSessionHasErrors(['customer_id', 'started_at', 'result']);
    }

    public function test_validates_customer_belongs_to_user(): void
    {
        $otherUser = User::factory()->create();
        $otherCustomer = Customer::factory()->create(['user_id' => $otherUser->id]);

        $callLogData = [
            'customer_id' => $otherCustomer->id,
            'started_at' => now()->format('Y-m-d\TH:i'),
            'result' => 'connected',
        ];

        $response = $this->actingAs($this->user)->post(route('call-logs.store'), $callLogData);

        $response->assertSessionHasErrors(['customer_id']);
    }

    public function test_validates_end_time_after_start_time(): void
    {
        $startTime = now();
        $endTime = $startTime->copy()->subHour();

        $callLogData = [
            'customer_id' => $this->customer->id,
            'started_at' => $startTime->format('Y-m-d\TH:i'),
            'ended_at' => $endTime->format('Y-m-d\TH:i'),
            'result' => 'connected',
        ];

        $response = $this->actingAs($this->user)->post(route('call-logs.store'), $callLogData);

        $response->assertSessionHasErrors(['ended_at']);
    }

    public function test_validates_future_dates_not_allowed(): void
    {
        $futureTime = now()->addDay();

        $callLogData = [
            'customer_id' => $this->customer->id,
            'started_at' => $futureTime->format('Y-m-d\TH:i'),
            'result' => 'connected',
        ];

        $response = $this->actingAs($this->user)->post(route('call-logs.store'), $callLogData);

        $response->assertSessionHasErrors(['started_at']);
    }

    public function test_index_shows_pagination(): void
    {
        CallLog::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('call-logs.index'));

        $response->assertOk();
        $response->assertSee('pagination');
    }

    public function test_index_shows_empty_state_when_no_call_logs(): void
    {
        $response = $this->actingAs($this->user)->get(route('call-logs.index'));

        $response->assertOk();
        $response->assertSee('架電記録がまだ登録されていません');
        $response->assertSee('架電記録を作成');
    }

    public function test_guest_cannot_access_call_logs(): void
    {
        $this->get(route('call-logs.index'))->assertRedirect(route('login'));
        $this->get(route('call-logs.create'))->assertRedirect(route('login'));
        $this->post(route('call-logs.store'), [])->assertRedirect(route('login'));
    }
}
