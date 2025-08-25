<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Report $report;
    public ?int $actorId;
    public array $changes;

    public function __construct(Report $report, ?int $actorId, array $changes = [])
    {
        $this->report = $report;
        $this->actorId = $actorId;
        $this->changes = $changes;
    }
}