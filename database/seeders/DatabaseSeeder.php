<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Product;
use App\Models\Setting;
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

        // 1. Buat Data Settings untuk Promo
        Setting::firstOrCreate(['key' => 'promo_coin_target'], ['value' => '10']);
        Setting::firstOrCreate(['key' => 'promo_reward_product_id'], ['value' => '2']); // ID 2 nanti adalah detergen

        // 2. Buat Dummy Produk
        Product::firstOrCreate(['id' => 1], [
            'name' => 'Koin Laundry',
            'type' => 'coin',
            'price' => 10000,
            'stock' => 9999,
        ]);

        Product::firstOrCreate(['id' => 2], [
            'name' => 'Detergen Cair Saset',
            'type' => 'addon',
            'price' => 2000,
            'stock' => 100,
        ]);

        // 3. Buat Dummy Mesin
        Machine::firstOrCreate(['id' => 1], [
            'code' => 'W1', // Washer 1
            'type' => 'washer',
            'status' => 'idle',
        ]);

        Machine::firstOrCreate(['id' => 2], [
            'code' => 'D1', // Dryer 1
            'type' => 'dryer',
            'status' => 'idle',
        ]);
    }
}
