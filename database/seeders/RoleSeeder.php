<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => RoleEnum::Admin->value]);
        $vendorRole = Role::create(['name' => RoleEnum::Vendor->value]);
        $userRole = Role::create(['name' => RoleEnum::User->value]);

        $approveVendors = Permission::create(['name' => PermissionEnum::ApprovedVendors->value]);
        $buyProducts = Permission::create(['name' => PermissionEnum::BuyProducts->value]);
        $sellProducts = Permission::create(['name' => PermissionEnum::SellProducts->value]);
        $userRole->syncPermissions([$buyProducts]);
        $vendorRole->syncPermissions([$sellProducts,$buyProducts]);
        $adminRole->syncPermissions([$approveVendors,$buyProducts,$sellProducts]);
    }
}
