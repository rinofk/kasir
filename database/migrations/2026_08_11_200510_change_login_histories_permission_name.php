<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete old permission if exists
        $oldPermission = Permission::where('name', 'view login histories')->first();
        if ($oldPermission) {
            $oldPermission->delete();
        }

        // Create new permission using the Indonesian menu name
        $newPermission = Permission::firstOrCreate(['name' => 'riwayat login']);

        // Assign to admin role if exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($newPermission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete new permission
        $newPermission = Permission::where('name', 'riwayat login')->first();
        if ($newPermission) {
            $newPermission->delete();
        }

        // Restore old permission
        $oldPermission = Permission::firstOrCreate(['name' => 'view login histories']);
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($oldPermission);
        }
    }
};
