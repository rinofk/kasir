<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display store settings form.
     */
    public function index()
    {
        $storeName = Setting::get('store_name', 'Toko Nining');
        $storeAddress = Setting::get('store_address', 'Mentibar, Kecamatan Paloh, Kabupaten Sambas');
        $storePhone = Setting::get('store_phone', '0812-3456-7890');

        return view('settings.index', compact('storeName', 'storeAddress', 'storePhone'));
    }

    /**
     * Update store settings in database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string|max:500',
            'store_phone' => 'required|string|max:50',
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'store_address.required' => 'Alamat toko wajib diisi.',
            'store_phone.required' => 'Nomor HP/Telepon toko wajib diisi.',
        ]);

        Setting::set('store_name', trim($request->store_name));
        Setting::set('store_address', trim($request->store_address));
        Setting::set('store_phone', trim($request->store_phone));

        return redirect()->route('settings.index')->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
