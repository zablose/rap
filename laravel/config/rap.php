<?php

return [

    'models' => [
        'user' => \App\Models\User::class,
        'role' => Zablose\Rap\Models\Role::class,
        'permission' => Zablose\Rap\Models\Permission::class,
    ],

    'tables' => [
        'roles' => 'rap_roles',
        'permissions' => 'rap_permissions',
        'role_user' => 'rap_role_user',
        'permission_user' => 'rap_permission_user',
        'permission_role' => 'rap_permission_role',
    ],

];
