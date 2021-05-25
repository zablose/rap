<?php

declare(strict_types=1);

namespace Zablose\Rap\Exceptions;

class PermissionDeniedException extends AccessDeniedException
{
    public function __construct(string $permission)
    {
        parent::__construct(sprintf("You don't have a required ['%s'] permission.", $permission));
    }
}
