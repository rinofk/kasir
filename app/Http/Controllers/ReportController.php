<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $cashierId = $request->input('user_id');

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->when($cashierId, function ($query, $cashierId) {
                return $query->where('user_id', $cashierId);
            })
            ->with(['user', 'details.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalSales = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->when($cashierId, function ($query, $cashierId) {
                return $query->where('user_id', $cashierId);
            })
            ->sum('total_price');

        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->when($cashierId, function ($query, $cashierId) {
                return $query->where('user_id', $cashierId);
            })
            ->count();

        // Calculate gross profit
        $grossProfit = DB::table('transactions')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->when($cashierId, function ($query, $cashierId) {
                return $query->where('transactions.user_id', $cashierId);
            })
            ->sum(DB::raw('(transaction_details.price - products.purchase_price) * transaction_details.quantity'));

        $cashiers = User::orderBy('name')->get();

        return view('reports.index', compact(
            'transactions', 
            'totalSales', 
            'totalTransactions', 
            'grossProfit', 
            'cashiers',
            'startDate', 
            'endDate', 
            'cashierId'
        ));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product.category']);
        return view('reports.show', compact('transaction'));
    }
}
