<?php declare(strict_types=1);

namespace Tests\Feature\Models;

use Tests\TestCase;

class PermissionTest extends TestCase
{
    /** @test */
    public function get_users_by_permission(): void
    {
        $sergejs = $this->createUser();
        $jane    = $this->createUser();

        $permission = $this->createPermission('update', 'Update');

        $sergejs->rap()->attachPermission($permission);
        $jane->rap()->attachPermission($permission);

        $users = $permission->users()->get();

        $this->assertTrue($users->count() === 2);
        $this->assertTrue($users->contains('name', $sergejs->name));
        $this->assertTrue($users->contains('name', $jane->name));
    }

    /** @test */
    public function get_roles_by_permission(): void
    {
        $role_user  = $this->createRole('user', 'User');
        $role_admin = $this->createRole('admin', 'Admin');

        $permission = $this->createPermission('delete', 'Delete');

        $role_user->attachPermission($permission);
        $role_admin->attachPermission($permission);

        $roles = $permission->roles()->get();

        $this->assertTrue($roles->count() === 2);
        $this->assertTrue($roles->contains('name', $role_user->name));
        $this->assertTrue($roles->contains('name', $role_admin->name));
    }
}
