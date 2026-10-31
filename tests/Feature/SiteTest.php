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
        $this->get('/')->assertOk()->assertSee('Inloggen');
        $this->get('/districten')->assertOk()->assertSee('District Klaas');
        $this->get('/wie-is-wie')->assertOk()->assertSee('Gouwvoorzitter');
        $this->get('/activiteiten')->assertOk()->assertSee('Activiteiten');
        $this->get('/login')->assertOk();
    }

    public function test_districten_lists_every_group_with_its_website(): void
    {
        $response = $this->get('/districten')->assertOk();

        foreach (config('gouw.districts') as $district) {
            $response->assertSee('District '.$district['name']);

            foreach ($district['groups'] as [$name, , , $website]) {
                $response->assertSee($name);

                if ($website) {
                    $response->assertSee('href="'.$website.'"', false);
                }
            }
        }
    }

    public function test_user_can_log_in_and_is_welcomed(): void
    {
        $user = User::factory()->create(['name' => 'Robin']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->get('/')->assertSeeText('Welkom Robin!');
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
