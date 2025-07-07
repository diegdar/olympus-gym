<?php
declare(strict_types=1);

namespace App\Enums;

enum OperationHours: int
{
    case START_HOUR = 7;
    case END_HOUR = 21;
}