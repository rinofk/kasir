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
        $stockValidation = Setting::get('stock_validation', '1');

        $bankBriNumber = Setting::get('bank_bri_number', '1234-01-000123-53-0');
        $bankBriHolder = Setting::get('bank_bri_holder', 'Nining');
        
        $bankBniNumber = Setting::get('bank_bni_number', '0987654321');
        $bankBniHolder = Setting::get('bank_bni_holder', 'Nining');
        
        $bankBcaNumber = Setting::get('bank_bca_number', '8880123456');
        $bankBcaHolder = Setting::get('bank_bca_holder', 'Nining');

        return view('settings.index', compact(
            'storeName', 'storeAddress', 'storePhone', 'stockValidation',
            'bankBriNumber', 'bankBriHolder',
            'bankBniNumber', 'bankBniHolder',
            'bankBcaNumber', 'bankBcaHolder'
        ));
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
            'stock_validation' => 'required|in:0,1',
            'bank_bri_number' => 'nullable|string|max:50',
            'bank_bri_holder' => 'nullable|string|max:100',
            'bank_bni_number' => 'nullable|string|max:50',
            'bank_bni_holder' => 'nullable|string|max:100',
            'bank_bca_number' => 'nullable|string|max:50',
            'bank_bca_holder' => 'nullable|string|max:100',
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'store_address.required' => 'Alamat toko wajib diisi.',
            'store_phone.required' => 'Nomor HP/Telepon toko wajib diisi.',
            'stock_validation.required' => 'Pilih opsi validasi stok.',
        ]);

        Setting::set('store_name', trim($request->store_name));
        Setting::set('store_address', trim($request->store_address));
        Setting::set('store_phone', trim($request->store_phone));
        Setting::set('stock_validation', $request->stock_validation);

        Setting::set('bank_bri_number', trim($request->bank_bri_number ?? ''));
        Setting::set('bank_bri_holder', trim($request->bank_bri_holder ?? ''));

        Setting::set('bank_bni_number', trim($request->bank_bni_number ?? ''));
        Setting::set('bank_bni_holder', trim($request->bank_bni_holder ?? ''));

        Setting::set('bank_bca_number', trim($request->bank_bca_number ?? ''));
        Setting::set('bank_bca_holder', trim($request->bank_bca_holder ?? ''));

        return redirect()->route('settings.index')->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
