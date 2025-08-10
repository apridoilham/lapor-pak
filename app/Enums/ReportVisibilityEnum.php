<?php

namespace App\Enums;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

enum ReportVisibilityEnum: string
{
    case PUBLIC = 'public';
    case RW = 'rw';
    case RT = 'rt';
    case PRIVATE = 'private';

    public function label(): string
    {
        $user = Auth::user();
        $rwNumber = $user?->resident?->rw?->number;
        $rtNumber = $user?->resident?->rt?->number;

        return match ($this) {
            self::PUBLIC => 'Publik (Semua Orang)',
            self::RW => $rwNumber ? "Hanya sesama Warga RW {$rwNumber}" : 'Hanya sesama Warga RW',
            self::RT => $rtNumber ? "Hanya sesama Warga RT {$rtNumber}" : 'Hanya sesama Warga RT',
            self::PRIVATE => 'Hanya Saya (Private)',
        };
    }
}