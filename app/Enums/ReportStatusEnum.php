<?php

namespace App\Enums;

// Hapus 'use Illuminate\Support\Facades\Auth;' karena tidak lagi digunakan

enum ReportStatusEnum: string
{
    case DELIVERED = 'delivered';
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        // Logika yang bergantung pada user dihapus karena tidak digunakan
        return match ($this) {
            self::DELIVERED => 'Terkirim',
            self::IN_PROCESS => 'Diproses',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }
}