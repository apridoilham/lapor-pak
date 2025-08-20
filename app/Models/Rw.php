<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rw extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
    ];

    public function rts(): HasMany
    {
        return $this->hasMany(Rt::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(User::class)->role('admin');
    }
}