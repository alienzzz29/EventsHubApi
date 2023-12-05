<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        $admin = Role::create(['guard_name' => 'api','name' => 'admin']);
        $merchant = Role::create(['guard_name' => 'api','name' => 'merchant']);
        $attendee = Role::create(['guard_name' => 'api','name' => 'attendee']);
        
        User::create([
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'contact_no' => '00000000000',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('Admin'),
        ])->assignRole($admin);// role

        User::create([
            'first_name' => 'Merchant',
            'last_name' => 'Merchant',
            'contact_no' => '00000000000',
            'email' => 'merchant@gmail.com',
            'password' => bcrypt('Merchant'),
    ])->assignRole($merchant);// role
    }
}
