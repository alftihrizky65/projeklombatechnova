<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'xp', 'level', 'streak', 'avatar_8bit'])]
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
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isContentManager(): bool
    {
        return $this->role === 'content_manager';
    }

    public function hasDashboardAccess(): bool
    {
        return in_array($this->role, ['admin', 'content_manager', 'user']);
    }

    public function learningProgress()
    {
        return $this->hasMany(\Illuminate\Support\Facades\DB::class);
    }

    public function aiLogs()
    {
        return $this->hasMany(AiLog::class);
    }
}
