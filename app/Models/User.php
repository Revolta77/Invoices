<?php

namespace App\Models;

use App\Mail\ResetPasswordMail;
use App\Mail\WelcomeMail;
use App\Support\Auth\EmailVerificationLinkBuilder;
use App\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'google_id',
    'avatar',
    'google_access_token',
    'google_refresh_token',
    'google_token_expires_at',
    'google_drive_root_folder_id',
    'google_backup_last_at',
    'google_backup_status',
    'google_backup_error',
])]
#[Hidden([
    'password',
    'remember_token',
    'google_access_token',
    'google_refresh_token',
])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use CanResetPassword, HasFactory, MustVerifyEmail, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'google_access_token' => 'encrypted',
            'google_refresh_token' => 'encrypted',
            'google_token_expires_at' => 'datetime',
            'google_backup_last_at' => 'datetime',
        ];
    }

    public function companyProfiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyProfile::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function canSyncToGoogleDrive(): bool
    {
        return filled($this->google_id) && filled($this->google_refresh_token);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::User;
    }

    public function sendEmailVerificationNotification(): void
    {
        Mail::to($this)->send(new WelcomeMail(
            user: $this,
            verificationUrl: EmailVerificationLinkBuilder::for($this),
        ));
    }

    public function sendWelcomeNotification(): void
    {
        Mail::to($this)->send(new WelcomeMail(
            user: $this,
            verificationUrl: null,
        ));
    }

    public function sendPasswordResetNotification($token): void
    {
        Mail::to($this)->send(new ResetPasswordMail(
            user: $this,
            token: $token,
        ));
    }
}
