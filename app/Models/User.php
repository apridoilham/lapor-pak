<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'rw_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', // Tambahkan ini
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    // [PERBAIKAN] Tambahkan metode accessor di bawah ini
    /**
     * Accessor untuk mendapatkan nama yang disensor.
     *
     * @return string
     */
    public function getCensoredNameAttribute(): string
    {
        $name = $this->attributes['name'];

        // Menangani nama yang sangat pendek agar tidak error
        if (mb_strlen($name) <= 3) {
            return mb_substr($name, 0, 1) . '***';
        }

        // Ambil 3 huruf pertama, sisanya diganti dengan bintang
        return mb_substr($name, 0, 3) . '***';
    }
    // [AKHIR PERBAIKAN]

    public function resident()
    {
        return $this->hasOne(Resident::class);
    }

    public function loginActivities()
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}