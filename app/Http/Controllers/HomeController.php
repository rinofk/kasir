<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display public landing page.
     */
    public function index(Request $request)
    {
        // 1. Ensure Gas & LPG category and sample products exist in database
        $this->ensureGasProductsExist();

        // 2. Fetch Store Settings
        $storeName = Setting::get('store_name', 'Toko Nining');
        $storeAddress = Setting::get('store_address', 'Mentibar, Kecamatan Paloh, Kabupaten Sambas');
        $storePhone = Setting::get('store_phone', '0812-3456-7890');

        // 3. Fetch Bank Accounts
        $bankAccounts = [
            [
                'name' => 'BRI',
                'fullName' => 'Bank Rakyat Indonesia',
                'number' => Setting::get('bank_bri_number', '1234-01-000123-53-0'),
                'holder' => Setting::get('bank_bri_holder', 'Nining'),
                'color' => '#00529C',
                'bgColor' => '#ebf5ff',
                'icon' => 'fa-building-columns',
            ],
            [
                'name' => 'BNI',
                'fullName' => 'Bank Negara Indonesia',
                'number' => Setting::get('bank_bni_number', '0987654321'),
                'holder' => Setting::get('bank_bni_holder', 'Nining'),
                'color' => '#F15A24',
                'bgColor' => '#fff3ed',
                'icon' => 'fa-building-columns',
            ],
            [
                'name' => 'BCA',
                'fullName' => 'Bank Central Asia',
                'number' => Setting::get('bank_bca_number', '8880123456'),
                'holder' => Setting::get('bank_bca_holder', 'Nining'),
                'color' => '#0060AF',
                'bgColor' => '#eef7ff',
                'icon' => 'fa-building-columns',
            ],
        ];

        // 4. Fetch Gas Elpiji products
        $gasProducts = Product::where('name', 'LIKE', '%gas%')
            ->orWhere('name', 'LIKE', '%elpiji%')
            ->orWhere('name', 'LIKE', '%lpg%')
            ->orWhereHas('category', function($q) {
                $q->where('name', 'LIKE', '%gas%');
            })
            ->get();

        // 5. Fetch all products & categories for catalog search
        $categories = Category::withCount('products')->get();
        
        $query = Product::with('category');
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $catalogProducts = $query->orderBy('name')->take(24)->get();

        return view('home', compact(
            'storeName',
            'storeAddress',
            'storePhone',
            'bankAccounts',
            'gasProducts',
            'categories',
            'catalogProducts'
        ));
    }

    /**
     * Helper to auto-create Gas products if missing.
     */
    private function ensureGasProductsExist()
    {
        try {
            $gasCategory = Category::firstOrCreate(
                ['slug' => 'gas-elpiji'],
                ['name' => 'Gas & Elpiji', 'description' => 'Tabung gas elpiji 3kg, 5.5kg, dan 12kg']
            );

            Product::firstOrCreate(
                ['code' => 'GAS3K'],
                [
                    'category_id' => $gasCategory->id,
                    'name' => 'Gas Elpiji 3kg (Melon)',
                    'purchase_price' => 17000,
                    'selling_price' => 20000,
                    'stock' => 35
                ]
            );

            Product::firstOrCreate(
                ['code' => 'GAS5K'],
                [
                    'category_id' => $gasCategory->id,
                    'name' => 'Bright Gas 5.5kg (Isi)',
                    'purchase_price' => 95000,
                    'selling_price' => 110000,
                    'stock' => 12
                ]
            );

            Product::firstOrCreate(
                ['code' => 'GAS12'],
                [
                    'category_id' => $gasCategory->id,
                    'name' => 'Gas Elpiji 12kg (Isi)',
                    'purchase_price' => 195000,
                    'selling_price' => 215000,
                    'stock' => 8
                ]
            );
        } catch (\Exception $e) {
            // Silence if DB is not ready
        }
    }
}
