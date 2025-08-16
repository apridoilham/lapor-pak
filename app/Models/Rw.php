<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    use HasFactory;
    protected $fillable = ['number'];

    public function rts()
    {
        return $this->hasMany(Rt::class)->orderBy('number');
    }

    // TAMBAHKAN METHOD INI
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    // TAMBAHKAN METHOD INI
    // Relasi ke User (Admin) yang terikat pada RW ini
    public function admins()
    {
        return $this->hasMany(User::class)->role('admin');
    }
}