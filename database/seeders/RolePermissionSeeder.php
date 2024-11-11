<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'admin',
            'email' => 'admin@case.com',
            'password' => bcrypt('Test1234!'),
        ]);

        $support = User::query()->create([
            'name' => 'support',
            'email' => 'support@case.com',
            'password' => bcrypt('Test1234!'),
        ]);

        $user = User::query()->create([
            'name' => 'user',
            'email' => 'user@case.com',
            'password' => bcrypt('Test1234!'),
        ]);

        Role::query()->insert([
            ['name' => 'admin', 'guard_name' => 'api'],
            ['name' => 'support', 'guard_name' => 'api'],
            ['name' => 'user', 'guard_name' => 'api'],
        ]);

        Permission::query()->insert([
            ['name' => 'payment-providers.index', 'guard_name' => 'api'],
            ['name' => 'payment-providers.show', 'guard_name' => 'api'],
            ['name' => 'payment-providers.change-pos-status', 'guard_name' => 'api'],
        ]);

        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo([
            'payment-providers.index',
            'payment-providers.show',
            'payment-providers.change-pos-status',
        ]);

        $supportRole = Role::findByName('support');
        $supportRole->givePermissionTo([
            'payment-providers.index',
            'payment-providers.show',
        ]);

        $admin->assignRole('admin');
        $support->assignRole('support');
        $user->assignRole('user');
    }
}
