<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with('category')->orderBy('name')->get();
        $stockValidation = Setting::get('stock_validation', '1') === '1';

        return view('pos.index', compact('categories', 'products', 'stockValidation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|string',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.name' => 'nullable|string',
            'cart.*.price' => 'nullable|numeric|min:0',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $cart = $request->cart;
                $paymentAmount = $request->payment_amount;
                $totalPrice = 0;
                $detailsData = [];
                $stockValidationEnabled = Setting::get('stock_validation', '1') === '1';

                // 1. Process items and validate stock
                foreach ($cart as $item) {
                    $isManual = str_starts_with((string) $item['id'], 'manual_');

                    if ($isManual) {
                        $customName = $item['name'] ?? 'Barang Manual';
                        $customPrice = (float) ($item['price'] ?? 0);
                        $subtotal = $customPrice * $item['qty'];
                        $totalPrice += $subtotal;

                        $detailsData[] = [
                            'product_id' => null,
                            'custom_name' => $customName,
                            'price' => $customPrice,
                            'quantity' => $item['qty'],
                            'subtotal' => $subtotal,
                        ];
                    } else {
                        $product = Product::lockForUpdate()->find($item['id']);
                        if (!$product) {
                            throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                        }

                        if ($stockValidationEnabled && $product->stock < $item['qty']) {
                            throw new \Exception("Stok produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->stock}");
                        }

                        $subtotal = $product->selling_price * $item['qty'];
                        $totalPrice += $subtotal;

                        // Decrement stock
                        $product->decrement('stock', $item['qty']);

                        $detailsData[] = [
                            'product_id' => $product->id,
                            'custom_name' => null,
                            'price' => $product->selling_price,
                            'quantity' => $item['qty'],
                            'subtotal' => $subtotal,
                        ];
                    }
                }

                // 2. Validate payment amount
                if ($paymentAmount < $totalPrice) {
                    throw new \Exception("Uang pembayaran tidak cukup. Total belanja: Rp " . number_format($totalPrice, 0, ',', '.') . ", dibayar: Rp " . number_format($paymentAmount, 0, ',', '.'));
                }

                $changeAmount = $paymentAmount - $totalPrice;

                // 3. Generate invoice number
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                // 4. Create Transaction
                $transaction = Transaction::create([
                    'user_id' => Auth::id(),
                    'invoice_number' => $invoiceNumber,
                    'total_price' => $totalPrice,
                    'payment_amount' => $paymentAmount,
                    'change_amount' => $changeAmount,
                ]);

                // 5. Create Transaction Details
                foreach ($detailsData as $detail) {
                    $detail['transaction_id'] = $transaction->id;
                    TransactionDetail::create($detail);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil!',
                    'transaction_id' => $transaction->id,
                    'invoice_number' => $transaction->invoice_number,
                    'total_price' => $totalPrice,
                    'payment_amount' => $paymentAmount,
                    'change_amount' => $changeAmount,
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product']);
        return view('pos.receipt', compact('transaction'));
    }
}
