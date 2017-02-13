<?php

return [

    'models' => [
        'role'       => Zablose\Rap\Models\Role::class,
        'permission' => Zablose\Rap\Models\Permission::class,
    ],

    'tables' => [
        'role_user' => 'rap_role_user',
        'permission_user' => 'rap_permission_user',
        'permission_role' => 'rap_permission_role',
    ],

];
