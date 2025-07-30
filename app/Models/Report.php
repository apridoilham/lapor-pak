<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'code',
        'resident_id',
        'report_category_id',
        'title',
        'description',
        'image',
        'latitude',
        'longitude',
        'address',
    ];

    // Menghubungkan dengan model Resident dan ReportCategory
    public function resident()
    {
        // satu laporan dimiliki oleh satu resident
        return $this->belongsTo(Resident::class);
    }

    public function reportCategory()
    {
        // satu laporan memiliki satu kategori laporan
        return $this->belongsTo(ReportCategory::class);
    }

    public function statuses()
    {
        // satu laporan memiliki banyak status laporan
        return $this->hasMany(ReportStatus::class);
    }
}
