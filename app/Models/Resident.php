<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'avatar',
    ];

    // Mehubungkan dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        // Satu resident memiliki banyak laporan
        return $this->hasMany(Report::class);
    }
}
