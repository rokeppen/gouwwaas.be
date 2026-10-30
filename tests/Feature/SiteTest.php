<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_reachable(): void
    {
        $this->get('/')->assertOk()->assertSee('Log in');
        $this->get('/who-is-who')->assertOk()->assertSee('Who is who');
        $this->get('/activities')->assertOk()->assertSee('Activities');
        $this->get('/login')->assertOk();
    }

    public function test_user_can_log_in_and_is_welcomed(): void
    {
        $user = User::factory()->create(['name' => 'Robin']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->get('/')->assertSeeText('Welcome Robin!');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }
}
