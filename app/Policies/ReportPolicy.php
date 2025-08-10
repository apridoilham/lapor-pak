<?php

namespace App\Policies;

use App\Enums\ReportStatusEnum;
use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    private function isOwner(User $user, Report $report): bool
    {
        return $user->id === $report->resident->user_id;
    }

    public function update(User $user, Report $report): bool
    {
        return $this->isOwner($user, $report) &&
               ($report->latestStatus?->status === ReportStatusEnum::DELIVERED);
    }

    public function delete(User $user, Report $report): bool
    {
        return $this->isOwner($user, $report) &&
               ($report->latestStatus?->status === ReportStatusEnum::DELIVERED);
    }

    public function complete(User $user, Report $report): bool
    {
        if (!$this->isOwner($user, $report)) {
            return false;
        }

        $latestStatus = $report->latestStatus?->status;

        return in_array($latestStatus, [
            ReportStatusEnum::DELIVERED,
            ReportStatusEnum::IN_PROCESS,
        ]);
    }
}