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
            ->paginate(25);

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

        $sortBy = $request->input('sort_by', 'quantity');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSortBy = ['quantity', 'profit'];
        $allowedSortOrder = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'quantity';
        }
        if (!in_array($sortOrder, $allowedSortOrder)) {
            $sortOrder = 'desc';
        }

        $orderByColumn = $sortBy === 'profit' ? 'total_profit' : 'total_quantity';

        $productStats = DB::table('transaction_details')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.code',
                'products.unit',
                DB::raw('SUM(transaction_details.quantity) as total_quantity'),
                DB::raw('SUM((transaction_details.price - products.purchase_price) * transaction_details.quantity) as total_profit')
            )
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->when($cashierId, function ($query, $cashierId) {
                return $query->where('transactions.user_id', $cashierId);
            })
            ->groupBy('products.id', 'products.name', 'products.code', 'products.unit')
            ->orderBy($orderByColumn, $sortOrder)
            ->get();

        $cashiers = User::orderBy('name')->get();

        return view('reports.index', compact(
            'transactions', 
            'totalSales', 
            'totalTransactions', 
            'grossProfit', 
            'cashiers',
            'startDate', 
            'endDate', 
            'cashierId',
            'productStats',
            'sortBy',
            'sortOrder'
        ));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product.category']);
        return view('reports.show', compact('transaction'));
    }
}
