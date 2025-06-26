<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RapTest extends TestCase
{
    #[Test]
    public function attach_and_detach_role_with_permission_to_the_user(): void
    {
        $user = $this->createUser();

        $role = $this->createRole('admin', 'Admin');
        $permission = $this->createPermission('update', 'Update');

        $role->attachPermission($permission);

        $this->assertFalse($user->rap()->is([$role->name]));
        $this->assertFalse($user->rap()->can([$permission->name]));

        $user->rap()->attachRole($role);

        $this->assertTrue($user->fresh()->rap()->is([$role->name]));
        $this->assertTrue($user->fresh()->rap()->can([$permission->name]));

        $user->rap()->detachRole($role);

        $this->assertFalse($user->fresh()->rap()->is([$role->name]));
        $this->assertFalse($user->rap()->can([$permission->name]));
    }

    #[Test]
    public function attach_and_detach_permission_to_the_user(): void
    {
        $user = $this->createUser();

        $permission = $this->createPermission('password.update', 'Permission to update password.');

        $this->assertFalse($user->rap()->can([$permission->name]));

        $user->rap()->attachPermission($permission);

        $this->assertTrue($user->fresh()->rap()->can([$permission->name]));

        $user->rap()->detachPermission($permission);

        $this->assertFalse($user->fresh()->rap()->can([$permission->name]));
    }

    #[Test]
    public function detach_all_roles_from_a_user(): void
    {
        $user = $this->createUser();

        $this->assertTrue($user->rap()->roles()->get()->count() === 0);

        $user->rap()
            ->attachRole($this->createRole('user', 'User'))
            ->attachRole($this->createRole('admin', 'Admin'));

        $this->assertTrue($user->rap()->roles()->get()->count() === 2);

        $user->rap()->detachAllRoles();

        $this->assertTrue($user->rap()->roles()->get()->count() === 0);
    }

    #[Test]
    public function detach_all_permissions_from_a_user(): void
    {
        $user = $this->createUser();

        $this->assertTrue($user->rap()->permissions()->get()->count() === 0);

        $user->rap()
            ->attachPermission($this->createPermission('create', 'Create'))
            ->attachPermission($this->createPermission('update', 'Update'));

        $this->assertTrue($user->rap()->permissions()->get()->count() === 2);

        $user->rap()->detachAllPermissions();

        $this->assertTrue($user->rap()->permissions()->get()->count() === 0);
    }
}
