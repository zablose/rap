<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Tests\TestCase;

class RoleTest extends TestCase
{
    /** @test */
    public function get_users_by_role(): void
    {
        $sergejs = $this->createUser();
        $jane = $this->createUser();

        $role = $this->createRole('user', 'User');

        $sergejs->rap()->attachRole($role);
        $jane->rap()->attachRole($role);

        $users = $role->users()->get();

        $this->assertTrue($users->count() === 2);
        $this->assertTrue($users->contains('name', $sergejs->name));
        $this->assertTrue($users->contains('name', $jane->name));
    }

    /** @test */
    public function get_permissions_by_role(): void
    {
        $role = $this->createRole('user', 'User');

        $create = $this->createPermission('create', 'Create');
        $update = $this->createPermission('update', 'Update');

        $role->attachPermission($create)->attachPermission($update);

        $permissions = $role->permissions()->get();

        $this->assertTrue($permissions->count() === 2);
        $this->assertTrue($permissions->contains('name', $create->name));
        $this->assertTrue($permissions->contains('name', $update->name));
    }

    /** @test */
    public function detach_permission_from_a_role(): void
    {
        $role = $this->createRole('user', 'User');

        $this->assertTrue($role->permissions()->get()->count() === 0);

        $create = $this->createPermission('create', 'Create');

        $role->attachPermission($create);

        $this->assertTrue($role->permissions()->get()->count() === 1);

        $role->detachPermission($create);

        $this->assertTrue($role->permissions()->get()->count() === 0);
    }

    /** @test */
    public function detach_all_permissions_from_a_role(): void
    {
        $role = $this->createRole('user', 'User');

        $this->assertTrue($role->permissions()->get()->count() === 0);

        $create = $this->createPermission('create', 'Create');
        $update = $this->createPermission('update', 'Update');

        $role->attachPermission($create)->attachPermission($update);

        $this->assertTrue($role->permissions()->get()->count() === 2);

        $role->detachAllPermissions();

        $this->assertTrue($role->permissions()->get()->count() === 0);
    }
}
