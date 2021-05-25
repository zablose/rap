<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class BladeExtensionsTest extends TestCase
{
    /** @test */
    public function user_with_role_can_see_content(): void
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
    public function user_with_permission_can_see_content(): void
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
}
