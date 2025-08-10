<?php

namespace App\Enums;

enum ReportVisibilityEnum: string
{
    case PUBLIC = 'public';
    case RW = 'rw';
    case RT = 'rt';
    case PRIVATE = 'private';

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => 'Semua Orang (Publik)',
            self::RW => 'Hanya sesama Warga RW',
            self::RT => 'Hanya sesama Warga RT',
            self::PRIVATE => 'Hanya Saya (Private)',
        };
    }
}