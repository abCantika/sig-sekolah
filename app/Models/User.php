<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_DINAS = 'dinas';
    public const ROLE_SEKOLAH = 'sekolah';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sekolah_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->email) {
                $user->email = strtolower(trim($user->email));
            }
        });
    }
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDinas(): bool
    {
        return $this->role === self::ROLE_DINAS;
    }

    public function isSekolah(): bool
    {
        return $this->role === self::ROLE_SEKOLAH;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_DINAS => 'Dinas Pendidikan',
            default => 'Sekolah',
        };
    }
}