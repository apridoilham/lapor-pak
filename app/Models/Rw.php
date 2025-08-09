<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    use HasFactory;
    protected $fillable = ['number'];

    // Tambahkan relasi ini
    public function rts()
    {
        return $this->hasMany(Rt::class)->orderBy('number');
    }
}