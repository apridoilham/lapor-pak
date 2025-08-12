<?php

namespace App\Enums;

use Illuminate\Support\Facades\Auth;

enum ReportStatusEnum: string
{
    case DELIVERED = 'delivered';
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        $user = Auth::user();
        $rwNumber = $user?->resident?->rw?->number;
        $rtNumber = $user?->resident?->rt?->number;

        return match ($this) {
            self::DELIVERED => 'Terkirim',
            self::IN_PROCESS => 'Diproses',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }
}