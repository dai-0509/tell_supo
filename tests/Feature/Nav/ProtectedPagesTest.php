<?php

namespace Tests\Feature\Nav;

use App\Models\User;
use Tests\TestCase;

class ProtectedPagesTest extends TestCase
{
    /** @dataProvider pagesProvider */
    public function test_guest_is_redirected_to_login(string $uri): void
    {
        $this->get($uri)->assertRedirect(route('login'));
    }

    /** @dataProvider pagesProvider */
    public function test_authenticated_user_can_access(string $uri): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get($uri)->assertOk();
    }

    public static function pagesProvider(): array
    {
        return [
            ['/customers'],
            ['/call-logs'],
            ['/dashboard'],
        ];
    }
}
