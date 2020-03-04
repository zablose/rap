<?php

namespace Zablose\Rap\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PermissionContract
{
    public function users(): BelongsToMany;

    public function roles(): BelongsToMany;
}
