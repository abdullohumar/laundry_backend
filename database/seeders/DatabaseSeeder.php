<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
        Permission::firstOrCreate(['name' => 'manage_users']);
        Permission::firstOrCreate(['name' => 'manage_products']);
        Permission::firstOrCreate(['name' => 'manage_promos']);
        Permission::firstOrCreate(['name' => 'view_all_transactions']);
    
        // Buat role
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'is_system' => true,
        ]);

        $karyawanRole = Role::firstOrCreate([
            'name' => 'karyawan',
            'is_system' => false
        ]);

        // Assign permission to super_admin
        $superAdminRole->givePermissionTo(Permission::all());        
    
        // Akun super admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@laundry.com'],
            [
                'name' => 'Laundry Owner',
                'password' => Hash::make('laundry_ganteng')
            ]
        );

        $superAdmin->assignRole($superAdminRole);
    }
}
