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
            ->assertSee('顧客がまだ登録されていません')
            ->assertSee('顧客を作成');
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
            ->assertSee('顧客がまだ登録されていません')
            ->assertSee('顧客を作成');
    }

    public function test_ナビゲーションに新規登録ボタンが常に表示される(): void
    {
        Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertDontSee('顧客がまだ登録されていません')
            ->assertSee('顧客を作成');
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
            'status' => '取引中',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('テスト株式会社')
            ->assertSee('田中太郎')
            ->assertSee('test@example.com')
            ->assertSee('09012345678')
            ->assertSee('A')
            ->assertSee('取引中');
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
            'temperature_rating' => 'C',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index'));

        $response->assertStatus(200)
            ->assertSee('A') // A温度
            ->assertSee('B') // B温度
            ->assertSee('C'); // C温度
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

    // 検索・フィルタ・ソート機能のテスト

    public function test_会社名で検索ができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'テスト株式会社',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'サンプル商事',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['search' => 'テスト']));

        $response->assertStatus(200)
            ->assertSee('テスト株式会社')
            ->assertDontSee('サンプル商事');
    }

    public function test_担当者名で検索ができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'contact_name' => '田中太郎',
            'company_name' => '会社A',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'contact_name' => '佐藤花子',
            'company_name' => '会社B',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['search' => '田中']));

        $response->assertStatus(200)
            ->assertSee('会社A')
            ->assertDontSee('会社B');
    }

    public function test_複数ステータスでフィルタができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '受けブロ顧客',
            'status' => '受けブロ',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '会話のみ顧客',
            'status' => '会話のみ',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '見込みあり顧客',
            'status' => '見込みあり',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['statuses' => ['受けブロ', '会話のみ']]));

        $response->assertStatus(200)
            ->assertSee('受けブロ顧客')
            ->assertSee('会話のみ顧客')
            ->assertDontSee('見込みあり顧客');
    }

    public function test_複数温度感でフィルタができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'A温度顧客',
            'temperature_rating' => 'A',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'B温度顧客',
            'temperature_rating' => 'B',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'C温度顧客',
            'temperature_rating' => 'C',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['temperatures' => ['A', 'B']]));

        $response->assertStatus(200)
            ->assertSee('A温度顧客')
            ->assertSee('B温度顧客')
            ->assertDontSee('C温度顧客');
    }

    public function test_複数業界でフィルタができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'IT企業',
            'industry' => 'IT',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '製造企業',
            'industry' => '製造業',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '小売企業',
            'industry' => '小売業',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['industries' => ['IT', '製造業']]));

        $response->assertStatus(200)
            ->assertSee('IT企業')
            ->assertSee('製造企業')
            ->assertDontSee('小売企業');
    }

    public function test_複数エリアでフィルタができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '関東企業',
            'area' => '関東',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '関西企業',
            'area' => '関西',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '中部企業',
            'area' => '中部',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['areas' => ['関東', '関西']]));

        $response->assertStatus(200)
            ->assertSee('関東企業')
            ->assertSee('関西企業')
            ->assertDontSee('中部企業');
    }

    public function test_複数フィルタが同時に適用される(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '対象企業1',
            'status' => '受けブロ',
            'temperature_rating' => 'A',
            'industry' => 'IT',
            'area' => '関東',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '対象企業2',
            'status' => '会話のみ',
            'temperature_rating' => 'B',
            'industry' => '製造業',
            'area' => '関西',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '除外企業',
            'status' => '見込みあり',
            'temperature_rating' => 'C',
            'industry' => '小売業',
            'area' => '中部',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', [
                'statuses' => ['受けブロ', '会話のみ'],
                'temperatures' => ['A', 'B'],
                'industries' => ['IT', '製造業'],
                'areas' => ['関東', '関西'],
            ]));

        $response->assertStatus(200)
            ->assertSee('対象企業1')
            ->assertSee('対象企業2')
            ->assertDontSee('除外企業');
    }

    public function test_検索とフィルタとソートが同時に適用される(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'あああテスト会社',
            'status' => '受けブロ',
            'created_at' => now()->subDays(2),
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'いいいテスト会社',
            'status' => '受けブロ',
            'created_at' => now()->subDays(1),
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '別テスト会社',
            'status' => '会話のみ',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', [
                'search' => 'テスト',
                'statuses' => ['受けブロ'],
                'sort' => 'created_at',
                'direction' => 'asc',
            ]));

        $content = $response->getContent();
        $pos1 = strpos($content, 'あああテスト会社');
        $pos2 = strpos($content, 'いいいテスト会社');
        $pos3 = strpos($content, '別テスト会社');

        $response->assertStatus(200);
        $this->assertTrue($pos1 < $pos2); // ソート順確認
        $this->assertFalse($pos3); // フィルタで除外確認
    }

    public function test_検索結果0件時に適切なメッセージが表示される(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => 'テスト会社',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['search' => '存在しない会社']));

        $response->assertStatus(200)
            ->assertSee('条件に一致する顧客が見つかりません')
            ->assertSee('フィルタをリセット');
    }

    public function test_無効なフィルタ値はバリデーションエラーになる(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['statuses' => ['無効なステータス']]));

        $response->assertSessionHasErrors('statuses.0');
    }

    public function test_架電禁止ステータスでフィルタができる(): void
    {
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '架電禁止企業',
            'status' => '架電禁止',
        ]);
        Customer::factory()->create([
            'user_id' => $this->user->id,
            'company_name' => '通常企業',
            'status' => '会話のみ',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['statuses' => ['架電禁止']]));

        $response->assertStatus(200)
            ->assertSee('架電禁止企業')
            ->assertDontSee('通常企業');
    }

    public function test_ページネーションが検索条件を維持する(): void
    {
        // 25件作成（20件を超えてページネーション発生）
        Customer::factory(25)->create([
            'user_id' => $this->user->id,
            'status' => '受けブロ',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('customers.index', ['statuses' => ['受けブロ'], 'page' => 2]));

        $response->assertStatus(200);
        // ページネーションリンクに検索条件が含まれることを確認
        $this->assertStringContainsString('statuses%5B0%5D=', $response->getContent());
    }
}
