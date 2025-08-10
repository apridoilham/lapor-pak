<?php

namespace App\Policies;

use App\Enums\ReportVisibilityEnum;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, Report $report): bool
    {
        if ($report->visibility === ReportVisibilityEnum::PUBLIC) {
            return true;
        }

        if (!$user->resident) {
            return false;
        }

        if ($report->visibility === ReportVisibilityEnum::RW) {
            return $user->resident->rw_id === $report->resident->rw_id;
        }

        if ($report->visibility === ReportVisibilityEnum::RT) {
            return $user->resident->rt_id === $report->resident->rt_id;
        }

        return false;
    }

    public function create(User $user, Report $report): bool
    {
        return $this->viewAny($user, $report);
    }
}