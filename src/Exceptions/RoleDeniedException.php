<?php

namespace Zablose\Rap\Exceptions;

class RoleDeniedException extends AccessDeniedException
{
    public function __construct(string $role)
    {
        parent::__construct(sprintf("You don't have a required ['%s'] role.", $role));
    }
}
