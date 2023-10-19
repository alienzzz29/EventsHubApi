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
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('Admin'),
        ])->assignRole($admin);// role
    }
}
