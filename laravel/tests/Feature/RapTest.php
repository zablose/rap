<?php

namespace Tests\Feature;

use Tests\TestCase;

class RapTest extends TestCase
{
    /** @test */
    public function attach_and_detach_role_with_permission_to_the_user(): void
    {
        $user = $this->createUser();

        $role       = $this->createRole('admin', 'Admin');
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

    /** @test */
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

    /** @test */
    public function detach_all_roles_from_a_user()
    {
        $user = $this->createUser();

        $this->assertTrue($user->rap()->roles()->get()->count() === 0);

        $role_user  = $this->createRole('user', 'User');
        $role_admin = $this->createRole('admin', 'Admin');

        $user->rap()->attachRole($role_user);
        $user->rap()->attachRole($role_admin);

        $this->assertTrue($user->rap()->roles()->get()->count() === 2);

        $user->rap()->detachAllRoles();

        $this->assertTrue($user->rap()->roles()->get()->count() === 0);
    }

    /** @test */
    public function detach_all_permissions_from_a_user()
    {
        $user = $this->createUser();

        $this->assertTrue($user->rap()->permissions()->get()->count() === 0);

        $create = $this->createPermission('create', 'Create');
        $update = $this->createPermission('update', 'Update');

        $user->rap()->attachPermission($create);
        $user->rap()->attachPermission($update);

        $this->assertTrue($user->rap()->permissions()->get()->count() === 2);

        $user->rap()->detachAllPermissions();

        $this->assertTrue($user->rap()->permissions()->get()->count() === 0);
    }
}
