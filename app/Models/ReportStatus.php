<?php

namespace App\Models;

use App\Enums\ReportStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStatus extends Model
{
    protected $fillable = [
        'report_id',
        'image',
        'status',
        'description',
        'created_by_role',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatusEnum::class,
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}