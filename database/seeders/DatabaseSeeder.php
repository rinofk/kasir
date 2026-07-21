<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage categories',
            'manage products',
            'manage users',
            'manage transactions',
            'view reports',
            'pos checkout',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $kasirRole = Role::create(['name' => 'kasir']);
        $kasirRole->givePermissionTo([
            'manage transactions',
            'pos checkout',
        ]);

        // Create Default Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'lonkwandi@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);

        // Create Default Cashier User
        $kasir = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@nining.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $kasir->assignRole($kasirRole);

        // Create Categories and Products
        $categoriesData = [
            [
                'name' => 'Sembako',
                'description' => 'Kebutuhan pokok sehari-hari',
                'products' => [
                    ['code' => 'BRS5K', 'name' => 'Beras 5kg', 'purchase_price' => 60000, 'selling_price' => 68000, 'stock' => 50],
                    ['code' => 'MYK2L', 'name' => 'Minyak Goreng 2L', 'purchase_price' => 28000, 'selling_price' => 33000, 'stock' => 40],
                    ['code' => 'GLA1K', 'name' => 'Gula Pasir 1kg', 'purchase_price' => 13500, 'selling_price' => 15500, 'stock' => 60],
                ]
            ],
            [
                'name' => 'Minuman',
                'description' => 'Minuman kemasan, air mineral, teh, dan kopi',
                'products' => [
                    ['code' => 'TBSOS', 'name' => 'Teh Botol Sosro', 'purchase_price' => 3000, 'selling_price' => 4500, 'stock' => 100],
                    ['code' => 'AQ600', 'name' => 'Aqua Botol 600ml', 'purchase_price' => 2000, 'selling_price' => 3500, 'stock' => 150],
                ]
            ],
            [
                'name' => 'Makanan Ringan',
                'description' => 'Snack, mi instan, dan biskuit',
                'products' => [
                    ['code' => 'INDGR', 'name' => 'Indomie Goreng', 'purchase_price' => 2600, 'selling_price' => 3500, 'stock' => 200],
                    ['code' => 'CHTTB', 'name' => 'Chitato Barbeque', 'purchase_price' => 8000, 'selling_price' => 10500, 'stock' => 80],
                ]
            ],
            [
                'name' => 'Kebutuhan Rumah Tangga',
                'description' => 'Sabun, deterjen, shampoo, dan alat mandi',
                'products' => [
                    ['code' => 'RNSCR', 'name' => 'Rinso Cair 800ml', 'purchase_price' => 17500, 'selling_price' => 21000, 'stock' => 30],
                    ['code' => 'LFB75', 'name' => 'Sabun Lifebuoy', 'purchase_price' => 3200, 'selling_price' => 4500, 'stock' => 90],
                ]
            ]
        ];

        foreach ($categoriesData as $cData) {
            $category = Category::create([
                'name' => $cData['name'],
                'slug' => Str::slug($cData['name']),
                'description' => $cData['description'],
            ]);

            foreach ($cData['products'] as $pData) {
                Product::create([
                    'category_id' => $category->id,
                    'code' => $pData['code'],
                    'name' => $pData['name'],
                    'purchase_price' => $pData['purchase_price'],
                    'selling_price' => $pData['selling_price'],
                    'stock' => $pData['stock'],
                ]);
            }
        }
    }
}
