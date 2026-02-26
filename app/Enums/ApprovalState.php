<?php

namespace App\Enums;

enum ApprovalState: string
{
    case Approved = 'approved';
    case ChangesRequested = 'changes_requested';
}
