<?php

namespace App\Enums;

enum ReportStatusEnum: string
{
    case DELIVERED = 'delivered';
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DELIVERED => 'Terkirim',
            self::IN_PROCESS => 'Diproses',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::DELIVERED => 'primary',
            self::IN_PROCESS => 'warning',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
        };
    }
}