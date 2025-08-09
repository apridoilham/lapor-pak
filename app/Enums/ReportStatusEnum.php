<?php

namespace App\Enums;

enum ReportStatusEnum: string
{
    case DELIVERED = 'delivered';
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    /**
     * ▼▼▼ TAMBAHKAN METHOD BARU DI SINI ▼▼▼
     * Method ini akan mengembalikan label Bahasa Indonesia untuk setiap status.
     */
    public function label(): string
    {
        return match ($this) {
            self::DELIVERED => 'Terkirim',
            self::IN_PROCESS => 'Sedang Diproses',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }
}