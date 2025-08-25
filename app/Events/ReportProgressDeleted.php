<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportProgressDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Report $report;
    public ?int $actorId;
    public string $deletedStatusLabel;

    public function __construct(Report $report, ?int $actorId, string $deletedStatusLabel)
    {
        $this->report = $report;
        $this->actorId = $actorId;
        $this->deletedStatusLabel = $deletedStatusLabel;
    }
}