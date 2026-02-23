<?php

namespace App\Enums;

enum Status: string
{
    case ToDo = 'To do';
    case InProgress = 'In progress';
    case Done = 'Done';
    case Cancelled = 'Cancelled';
}