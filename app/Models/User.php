<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPassword;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

// is_admin is deliberately not fillable: it can't be set through any form.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Send the (Dutch) password reset e-mail.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token, newAccount: false));
    }

    /**
     * Make the current password unusable, end all sessions and e-mail a reset link.
     *
     * @return string Password broker status (Password::RESET_LINK_SENT on success)
     */
    public function forcePasswordReset(bool $newAccount = false): string
    {
        $this->forceFill([
            'password' => Str::random(64),
            'remember_token' => Str::random(60),
        ])->save();

        $this->endSessions();

        $token = Password::broker()->createToken($this);
        $this->notify(new ResetPassword($token, $newAccount));

        return Password::RESET_LINK_SENT;
    }

    /**
     * Log the user out everywhere, optionally keeping one session.
     */
    public function endSessions(?string $exceptSessionId = null): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $this->getKey())
            ->when($exceptSessionId, fn ($query) => $query->where('id', '!=', $exceptSessionId))
            ->delete();
    }
}
