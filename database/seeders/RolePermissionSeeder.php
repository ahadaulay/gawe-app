<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;



class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions = [
            'manage categories',
            'manage tools',
            'manage projects',
            'manage project tools',
            'manage wallets',
            'manage applicants',

            //regular action
            'apply job',
            'topup wallet',
            'withdraw wallet',
        ];


        foreach($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        $clientRole = Role::firstOrCreate([
            'name' => 'project_client'
        ]);
        $clientPermission = [
            'manage projects',
            'manage project tools',
            'manage applicants',
            'topup wallet',
            'withdraw wallet',
        ];
        $clientRole->syncPermissions($clientPermission);

        $freelanceRole = Role::firstOrCreate([
            'name' => 'project_freelance'
        ]);
        $freelanPermission = [
            'apply job',
            'withdraw wallet',
        ];
        $freelanceRole->syncPermissions($freelanPermission);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin'
        ]);
        
        $user = User::create([
            'name' => 'super admin',
            'email' => 'admin@gmail.com',
            'occupation' => 'owner',
            'connect' => 999999,
            'avatar' => 'default.png',
            'password' => bcrypt('admin123'),
        ]);
        $user->assignRole('super_admin');

        $wallet = new Wallet([
            'balance' => 0,
        ]);

        $user->wallet()->save($wallet);
    }
}
