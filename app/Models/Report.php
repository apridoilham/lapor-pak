<?php

namespace App\Models;

use App\Enums\ReportVisibilityEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    use HasFactory;

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
        'visibility',
    ];

    protected $attributes = [
        'visibility' => 'public',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => ReportVisibilityEnum::class,
        ];
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function reportCategory(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class);
    }

    public function reportStatuses(): HasMany
    {
        return $this->hasMany(ReportStatus::class)->orderBy('created_at', 'desc');
    }

    public function latestStatus(): HasOne
    {
        return $this->hasOne(ReportStatus::class)->latestOfMany();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }
}