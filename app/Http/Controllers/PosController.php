<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with('category')->orderBy('name')->get();

        return view('pos.index', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $cart = $request->cart;
                $paymentAmount = $request->payment_amount;
                $totalPrice = 0;
                $detailsData = [];

                // 1. Process items and validate stock
                foreach ($cart as $item) {
                    $product = Product::lockForUpdate()->find($item['id']);

                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stok produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->stock}");
                    }

                    $subtotal = $product->selling_price * $item['qty'];
                    $totalPrice += $subtotal;

                    // Decrement stock
                    $product->decrement('stock', $item['qty']);

                    $detailsData[] = [
                        'product_id' => $product->id,
                        'price' => $product->selling_price,
                        'quantity' => $item['qty'],
                        'subtotal' => $subtotal,
                    ];
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
