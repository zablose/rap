<?php

namespace Tests\Feature;

use Tests\TestCase;

class RapTest extends TestCase
{
    /** @test */
    public function attach_and_detach_role_to_the_user(): void
    {
        $user = $this->createUser();

        $role = $this->createRole('admin', 'Admin');

        $this->assertFalse($user->rap()->is([$role->name]));

        $user->rap()->attachRole($role);

        $this->assertTrue($user->fresh()->rap()->is([$role->name]));

        $user->rap()->detachRole($role);

        $this->assertFalse($user->fresh()->rap()->is([$role->name]));
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
}
