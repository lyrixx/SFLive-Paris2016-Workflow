<?php

namespace App\Entity\Model;

enum TaskStep: string
{
    case New = 'new';
    case Backlogged = 'backlogged';
    case Processing = 'processing';
    case Failed = 'failed';
    case Completed = 'completed';
}
