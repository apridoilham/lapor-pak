<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rt extends Model
{
    use HasFactory;

    protected $fillable = [
        'rw_id',
        'number',
    ];

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }
}