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
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function getCensoredNameAttribute(): string
    {
        $name = $this->attributes['name'];

        if (mb_strlen($name) <= 3) {
            return mb_substr($name, 0, 1) . '***';
        }

        return mb_substr($name, 0, 3) . '***';
    }

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