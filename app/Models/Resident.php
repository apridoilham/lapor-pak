<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Resident extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'avatar',
        'rt_id',    // <-- Diubah
        'rw_id',    // <-- Diubah
        'address',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function reports() { return $this->hasMany(Report::class); }

    // ▼▼▼ TAMBAHKAN RELASI BARU INI ▼▼▼
    public function rt() { return $this->belongsTo(Rt::class); }
    public function rw() { return $this->belongsTo(Rw::class); }
}