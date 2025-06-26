<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    #[Test]
    public function user_with_role_can_access_route(): void
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

    #[Test]
    public function user_with_permission_can_access_route(): void
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
}
