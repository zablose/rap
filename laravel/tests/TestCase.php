<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Zablose\Rap\Models\Permission;
use Zablose\Rap\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createRole(string $name, string $description): Role
    {
        return Role::create(compact('name', 'description'));
    }

    protected function createPermission(string $name, string $description): Permission
    {
        return Permission::create(compact('name', 'description'));
    }
}
