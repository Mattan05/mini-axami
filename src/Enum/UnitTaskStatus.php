<?php

namespace App\Enum;

enum UnitTaskStatus: string
{
    case NOT_STARTED = 'Not Started';
    case IN_PROGRESS = 'In Progress ';
    case COMPLETED = 'Completed';
}
