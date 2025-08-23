<?php

namespace App\Enums;

use App\Models\User;

enum ReportVisibilityEnum: string
{
    case PUBLIC = 'public';
    case RW = 'rw';
    case RT = 'rt';
    case PRIVATE = 'private';

    public function label(User $user = null): string
    {
        return match ($this) {
            self::PUBLIC => 'Publik (Semua Orang)',
            self::RW => ($user?->resident?->rw?->number) ? "Hanya sesama Warga RW {$user->resident->rw->number}" : 'Hanya sesama Warga RW',
            self::RT => ($user?->resident?->rt?->number) ? "Hanya sesama Warga RT {$user->resident->rt->number}" : 'Hanya sesama Warga RT',
            self::PRIVATE => 'Hanya Saya (Private)',
        };
    }
}