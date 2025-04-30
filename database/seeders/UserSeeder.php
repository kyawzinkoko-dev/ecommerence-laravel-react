<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\VendorStatusEnum;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com'
        ])->assignRole(RoleEnum::User->value);
        User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor@gmail.com'
        ])->assignRole(RoleEnum::Vendor->value);
        Vendor::factory()->create([
            'user_id' => 1,
            'status' => VendorStatusEnum::APPROVED->value,
            'store_name' => 'Vendor Store',
            'store_address' => fake()->address,
        ]);
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com'
        ])->assignRole(RoleEnum::Admin->value);
    }
}
