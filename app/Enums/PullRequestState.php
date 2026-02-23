<?php

namespace App\Enums;

enum PullRequestState: string
{
    case Open = 'OPEN';
    case Merged = 'MERGED';
    case Declined = 'DECLINED';
    case Superseded = 'SUPERSEDED';
}
