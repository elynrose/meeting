<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'session_create',
            ],
            [
                'id'    => 18,
                'title' => 'session_edit',
            ],
            [
                'id'    => 19,
                'title' => 'session_show',
            ],
            [
                'id'    => 20,
                'title' => 'session_delete',
            ],
            [
                'id'    => 21,
                'title' => 'session_access',
            ],
            [
                'id'    => 22,
                'title' => 'todo_create',
            ],
            [
                'id'    => 23,
                'title' => 'todo_edit',
            ],
            [
                'id'    => 24,
                'title' => 'todo_show',
            ],
            [
                'id'    => 25,
                'title' => 'todo_delete',
            ],
            [
                'id'    => 26,
                'title' => 'todo_access',
            ],
            [
                'id'    => 27,
                'title' => 'payment_create',
            ],
            [
                'id'    => 28,
                'title' => 'payment_edit',
            ],
            [
                'id'    => 29,
                'title' => 'payment_show',
            ],
            [
                'id'    => 30,
                'title' => 'payment_delete',
            ],
            [
                'id'    => 31,
                'title' => 'payment_access',
            ],
            [
                'id'    => 32,
                'title' => 'credit_create',
            ],
            [
                'id'    => 33,
                'title' => 'credit_edit',
            ],
            [
                'id'    => 34,
                'title' => 'credit_show',
            ],
            [
                'id'    => 35,
                'title' => 'credit_delete',
            ],
            [
                'id'    => 36,
                'title' => 'credit_access',
            ],
            [
                'id'    => 37,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 38,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 39,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 40,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 41,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
