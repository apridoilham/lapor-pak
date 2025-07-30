<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStatus extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'image',
        'status', // 'delivered', 'in_process', 'completed', 'rejected'
        'description',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
