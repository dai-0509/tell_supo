<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserKpiTarget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * KPI目標の基本機能テスト
 */
class KpiTargetBasicTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * KPI管理画面にアクセスできるか（認証必須）
     */
    public function test_kpi_index_requires_authentication(): void
    {
        $response = $this->get('/kpi-targets');
        $response->assertRedirect('/login');
    }

    /**
     * 認証済みユーザーはKPI管理画面にアクセスできる
     */
    public function test_authenticated_user_can_access_kpi_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/kpi-targets');

        $response->assertStatus(200);
        $response->assertViewIs('kpi-targets.index');
        $response->assertViewHas(['activeTarget', 'recentTargets']);
    }

    /**
     * KPI目標作成画面にアクセスできる
     */
    public function test_can_access_kpi_create_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/kpi-targets/create');

        $response->assertStatus(200);
        $response->assertViewIs('kpi-targets.create');
    }

    /**
     * 有効なデータでKPI目標を作成できる
     */
    public function test_can_create_kpi_target_with_valid_data(): void
    {
        $data = [
            'monday_call_target' => 50,
            'tuesday_call_target' => 50,
            'wednesday_call_target' => 50,
            'thursday_call_target' => 50,
            'friday_call_target' => 50,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 250,
            'monthly_call_target' => 1000,
            'monthly_appointment_target' => 25,
            'target_success_rate' => 60.0,
            'target_appointment_rate' => 25.0,
            'setting_method' => 'manual',
            'effective_from' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->post('/kpi-targets', $data);

        // デバッグ情報
        if ($response->getStatusCode() !== 302) {
            dump('Status Code: ' . $response->getStatusCode());
            dump('Response Content: ' . $response->getContent());
        }

        $response->assertRedirect('/kpi-targets');
        $response->assertSessionHas('success');

        // データベースに保存されているか確認
        $this->assertDatabaseHas('user_kpi_targets', [
            'user_id' => $this->user->id,
            'monday_call_target' => 50,
            'weekly_call_target' => 250,
            'monthly_call_target' => 1000,
            'is_active' => true,
        ]);
    }

    /**
     * バリデーションエラーのテスト
     */
    public function test_validation_errors_on_invalid_data(): void
    {
        $data = [
            'monday_call_target' => 0,
            'tuesday_call_target' => 0,
            'wednesday_call_target' => 0,
            'thursday_call_target' => 0,
            'friday_call_target' => 0,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 10,
            'monthly_call_target' => 50,
            'monthly_appointment_target' => -1, // 最小値エラー
            'setting_method' => 'manual',
        ];

        $response = $this->actingAs($this->user)
            ->post('/kpi-targets', $data);

        $response->assertStatus(302); // リダイレクト
        $response->assertSessionHasErrors([
            'monday_call_target',
            'monthly_appointment_target'
        ]);
    }

    /**
     * カスタムバリデーション（整合性）エラーのテスト
     */
    public function test_consistency_validation_errors(): void
    {
        $data = [
            'monday_call_target' => 40,
            'tuesday_call_target' => 40,
            'wednesday_call_target' => 40,
            'thursday_call_target' => 40,
            'friday_call_target' => 40,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 250, // 整合性エラー（曜日合計200 ≠ 250）
            'monthly_call_target' => 700, // 整合性エラー（250 * 4 = 1000 未満）
            'monthly_appointment_target' => 25,
            'setting_method' => 'manual',
            'effective_from' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->post('/kpi-targets', $data);

        $response->assertStatus(302); // リダイレクト
        $response->assertSessionHasErrors([
            'weekly_call_target',
            'monthly_call_target'
        ]);
    }

    /**
     * モデルの整合性チェック機能のテスト
     */
    public function test_model_consistency_check(): void
    {
        // 整合性のある目標
        $consistentTarget = UserKpiTarget::make([
            'monday_call_target' => 50,
            'tuesday_call_target' => 50,
            'wednesday_call_target' => 50,
            'thursday_call_target' => 50,
            'friday_call_target' => 50,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 250, // 合計250
            'monthly_call_target' => 1000, // 250 * 4
        ]);
        $this->assertTrue($consistentTarget->isConsistent());

        // 整合性のない目標
        $inconsistentTarget = UserKpiTarget::make([
            'monday_call_target' => 40,
            'tuesday_call_target' => 40,
            'wednesday_call_target' => 40,
            'thursday_call_target' => 40,
            'friday_call_target' => 40,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 250, // 合計200 ≠ 250
            'monthly_call_target' => 1000,
        ]);
        $this->assertFalse($inconsistentTarget->isConsistent());
    }

    /**
     * 既存目標があると無効化される
     */
    public function test_existing_target_is_deactivated_when_creating_new(): void
    {
        // 既存の目標を作成
        $existingTarget = UserKpiTarget::create([
            'user_id' => $this->user->id,
            'monday_call_target' => 30,
            'tuesday_call_target' => 30,
            'wednesday_call_target' => 30,
            'thursday_call_target' => 30,
            'friday_call_target' => 30,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 150,
            'monthly_call_target' => 600,
            'monthly_appointment_target' => 15,
            'setting_method' => 'manual',
            'effective_from' => now()->toDateString(),
            'is_active' => true,
        ]);

        // 新しい目標を作成
        $newData = [
            'monday_call_target' => 50,
            'tuesday_call_target' => 50,
            'wednesday_call_target' => 50,
            'thursday_call_target' => 50,
            'friday_call_target' => 50,
            'saturday_call_target' => 0,
            'sunday_call_target' => 0,
            'weekly_call_target' => 250,
            'monthly_call_target' => 1000,
            'monthly_appointment_target' => 25,
            'setting_method' => 'manual',
            'effective_from' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->post('/kpi-targets', $newData);

        $response->assertRedirect('/kpi-targets');

        // 既存目標が無効化されているか確認
        $existingTarget->refresh();
        $this->assertFalse($existingTarget->is_active);

        // 新しい目標がアクティブか確認
        $this->assertDatabaseHas('user_kpi_targets', [
            'user_id' => $this->user->id,
            'monday_call_target' => 50,
            'is_active' => true,
        ]);
    }
}
