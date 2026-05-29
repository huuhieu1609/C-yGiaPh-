<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'description' => 'Administrator',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $customerRoleId = DB::table('roles')->insertGetId([
            'name' => 'customer',
            'description' => 'Customer',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('users')->insertOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        DB::table('users')->insertOrInsert(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('123456'),
                'role_id' => $customerRoleId,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
