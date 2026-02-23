<?php

namespace App\Enums;

enum ApprovalState: string
{
    case Approved = 'approved';
    case Unapproved = 'unapproved';
    case RequestChanges = 'request-changes';
}
