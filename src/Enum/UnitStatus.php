<?php

namespace App\Enum;

enum UnitStatus: string
{
    case RUNNING = 'Running';
    case STOPPED = 'Stopped';
    case MAINTENANCE = 'Maintenance';
    case ERROR = 'Error';

}