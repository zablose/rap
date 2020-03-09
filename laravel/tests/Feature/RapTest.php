<?php

namespace Tests\Feature;

use Tests\TestCase;
use Zablose\Rap\Exceptions\PermissionDeniedException;
use Zablose\Rap\Exceptions\RoleDeniedException;

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

    /** @test */
    public function user_with_role_can_see_content()
    {
        $user = $this->createUser();

        $role = $this->createRole('user', 'User');

        $user->rap()->attachRole($role);

        $this
            ->actingAs($user->fresh())
            ->get('/')
            ->assertSee('User')
            ->assertDontSee('Admin')
            ->assertDontSee('Password Update');
    }

    /** @test */
    public function user_with_permission_can_see_content()
    {
        $user = $this->createUser();

        $permission = $this->createPermission('password.update', 'Password Update');

        $user->rap()->attachPermission($permission);

        $this
            ->actingAs($user->fresh())
            ->get('/')
            ->assertSee('Password Update')
            ->assertDontSee('User')
            ->assertDontSee('Admin');
    }

    /** @test */
    public function throw_role_denied_exception()
    {
        $this->withoutExceptionHandling();

        $this->expectException(RoleDeniedException::class);

        $this->get('/user');
    }

    /** @test */
    public function user_with_role_can_access_route()
    {
        $user = $this->createUser();

        $role = $this->createRole('user', 'User');

        $user->rap()->attachRole($role);

        $this
            ->actingAs($user->fresh())
            ->get('/user')
            ->assertOk()
            ->assertSee('User');
    }

    /** @test */
    public function throw_permission_denied_exception()
    {
        $this->withoutExceptionHandling();

        $this->expectException(PermissionDeniedException::class);

        $this->post('/password/update');
    }

    /** @test */
    public function user_with_permission_can_access_route()
    {
        $user = $this->createUser();

        $permission = $this->createPermission('password.update', 'Password Update');

        $user->rap()->attachPermission($permission);

        $this
            ->actingAs($user->fresh())
            ->post('/password/update')
            ->assertOk()
            ->assertSee('Password Update');
    }

    /** @test */
    public function get_users_by_role()
    {
        $sergejs = $this->createUser();
        $jane    = $this->createUser();

        $role = $this->createRole('user', 'User');

        $sergejs->rap()->attachRole($role);
        $jane->rap()->attachRole($role);

        $users = $role->users()->get();

        $this->assertTrue($users->count() === 2);
        $this->assertTrue($users->contains('name', $sergejs->name));
        $this->assertTrue($users->contains('name', $jane->name));
    }

    /** @test */
    public function get_permissions_by_role()
    {
        $role = $this->createRole('user', 'User');

        $create = $this->createPermission('create', 'Create');
        $update = $this->createPermission('update', 'Update');

        $role->attachPermission($create);
        $role->attachPermission($update);

        $permissions = $role->permissions()->get();

        $this->assertTrue($permissions->count() === 2);
        $this->assertTrue($permissions->contains('name', $create->name));
        $this->assertTrue($permissions->contains('name', $update->name));
    }

    /** @test */
    public function detach_permission_from_a_role()
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
    public function detach_all_permissions_from_a_role()
    {
        $role = $this->createRole('user', 'User');

        $this->assertTrue($role->permissions()->get()->count() === 0);

        $create = $this->createPermission('create', 'Create');
        $update = $this->createPermission('update', 'Update');

        $role->attachPermission($create);
        $role->attachPermission($update);

        $this->assertTrue($role->permissions()->get()->count() === 2);

        $role->detachAllPermissions();

        $this->assertTrue($role->permissions()->get()->count() === 0);
    }

    /** @test */
    public function get_users_by_permission()
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
    public function get_roles_by_permission()
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
