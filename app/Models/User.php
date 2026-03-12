<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, staff, external
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Boot function untuk mengatur default role saat registrasi
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            // Jika saat registrasi role tidak diisi, otomatis jadi 'external'
            $user->role = $user->role ?? 'external';
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Menentukan akses ke Filament Panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Mendapatkan role user saat ini
        $role = $this->role;

        // Jika Anda hanya punya satu panel ('admin'), 
        // semua role (admin, staff, external) harus diizinkan masuk
        // agar mereka bisa melihat halaman "Verify Email" jika belum verifikasi.
        if ($panel->getId() === 'admin') {
            return in_array($role, ['admin', 'staff', 'external']);
        }

        // Jika kedepannya Anda buat panel khusus 'client'
        if ($panel->getId() === 'client') {
            return $role === 'external';
        }

        return false;
    }

    /**
     * Helper Methods untuk pengecekan role di kodingan lain
     */
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isStaff(): bool { return $this->role === 'staff'; }
    public function isExternal(): bool { return $this->role === 'external'; }
}