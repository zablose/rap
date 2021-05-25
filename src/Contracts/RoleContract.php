<?php

declare(strict_types=1);

namespace Zablose\Rap\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleContract
{
    public function users(): BelongsToMany;

    public function permissions(): BelongsToMany;

    public function attachPermission(PermissionContract $permission): self;

    public function detachPermission(PermissionContract $permission): self;

    public function detachAllPermissions(): self;
}
