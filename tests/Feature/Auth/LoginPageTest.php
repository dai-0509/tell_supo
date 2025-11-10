<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_page_is_visible(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }
}
