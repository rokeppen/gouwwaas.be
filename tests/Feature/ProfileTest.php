<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_see_the_profile(): void
    {
        $this->get('/profiel')->assertRedirect('/login');
    }

    public function test_profile_shows_name_and_email(): void
    {
        $user = User::factory()->create(['name' => 'Robin', 'email' => 'robin@example.com']);

        $this->actingAs($user)->get('/profiel')
            ->assertOk()
            ->assertSee('Robin')
            ->assertSee('robin@example.com')
            ->assertDontSee('Rol');
    }

    public function test_password_can_be_changed_with_the_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/profiel/wachtwoord', [
            'current_password' => 'password',
            'password' => 'nieuw-wachtwoord',
            'password_confirmation' => 'nieuw-wachtwoord',
        ])->assertRedirect('/profiel')->assertSessionHas('status');

        $this->assertTrue(Hash::check('nieuw-wachtwoord', $user->fresh()->password));
    }

    public function test_password_change_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profiel')->put('/profiel/wachtwoord', [
            'current_password' => 'fout',
            'password' => 'nieuw-wachtwoord',
            'password_confirmation' => 'nieuw-wachtwoord',
        ])->assertRedirect('/profiel')->assertSessionHasErrorsIn('password', 'current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_password_must_be_confirmed_and_long_enough(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/profiel/wachtwoord', [
            'current_password' => 'password',
            'password' => 'kort',
            'password_confirmation' => 'anders',
        ])->assertSessionHasErrorsIn('password', 'password');
    }

    public function test_forgot_password_sends_a_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/wachtwoord-vergeten', ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, fn ($n) => ! $n->newAccount);
    }

    public function test_forgot_password_does_not_reveal_unknown_addresses(): void
    {
        Notification::fake();

        $this->post('/wachtwoord-vergeten', ['email' => 'onbekend@example.com'])
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->get("/wachtwoord-herstellen/{$token}?email={$user->email}")->assertOk();

        $this->post('/wachtwoord-herstellen', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'nieuw-wachtwoord',
            'password_confirmation' => 'nieuw-wachtwoord',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('nieuw-wachtwoord', $user->fresh()->password));
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/wachtwoord-herstellen', [
            'token' => 'ongeldig',
            'email' => $user->email,
            'password' => 'nieuw-wachtwoord',
            'password_confirmation' => 'nieuw-wachtwoord',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
