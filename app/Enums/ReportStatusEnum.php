<?php

namespace App\Enums;

enum ReportStatusEnum: string
{
    case DELIVERED = 'delivered';
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
}