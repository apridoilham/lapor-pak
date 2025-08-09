<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;
    protected $fillable = ['rw_id', 'number'];

    // Tambahkan relasi ini
    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }
}