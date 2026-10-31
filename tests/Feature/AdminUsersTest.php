<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admins_cannot_manage_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->get('/gebruikers')->assertForbidden();
        $this->actingAs($user)->post('/gebruikers', ['name' => 'X', 'email' => 'x@example.com'])->assertForbidden();
        $this->actingAs($user)->delete("/gebruikers/{$other->id}")->assertForbidden();
        $this->actingAs($user)->post("/gebruikers/{$other->id}/wachtwoord-reset")->assertForbidden();

        $this->assertModelExists($other);
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/gebruikers')->assertRedirect('/login');
    }

    public function test_only_admins_see_the_users_link(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertDontSee('Gebruikers');
        $this->actingAs(User::factory()->admin()->create())->get('/')->assertSee('Gebruikers');
    }

    public function test_admin_can_create_a_user_who_gets_an_invitation(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/gebruikers', [
            'name' => 'Nieuwe Leiding',
            'email' => 'nieuw@example.com',
            'is_admin' => '1',
        ])->assertRedirect('/gebruikers');

        $user = User::where('email', 'nieuw@example.com')->firstOrFail();
        $this->assertFalse($user->is_admin, 'is_admin must not be settable through the form');

        Notification::assertSentTo($user, ResetPassword::class, fn ($n) => $n->newAccount);
    }

    public function test_email_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->create();

        $this->actingAs($admin)->post('/gebruikers', ['name' => 'Dubbel', 'email' => $existing->email])
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_update_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->put("/gebruikers/{$user->id}", [
            'name' => 'Andere Naam',
            'email' => $user->email,
        ])->assertRedirect('/gebruikers');

        $this->assertSame('Andere Naam', $user->fresh()->name);
    }

    public function test_admin_can_delete_a_user_but_not_themselves(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->delete("/gebruikers/{$user->id}")->assertRedirect('/gebruikers');
        $this->assertModelMissing($user);

        $this->actingAs($admin)->delete("/gebruikers/{$admin->id}")->assertSessionHasErrors('user');
        $this->assertModelExists($admin);
    }

    public function test_admin_can_force_a_password_reset(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->post("/gebruikers/{$user->id}/wachtwoord-reset")->assertSessionHas('status');

        $this->assertFalse(Hash::check('password', $user->fresh()->password), 'old password must stop working');
        Notification::assertSentTo($user, ResetPassword::class, fn ($n) => ! $n->newAccount);
    }
}
