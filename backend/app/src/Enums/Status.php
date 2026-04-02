<?php

namespace App\Enums;

enum Status: string
{
    case Created = 'created';
    case InProgress = 'in progress';
    case Done = 'done';
    case Cancelled = 'cancelled';
}