<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Metrics
        $todaySales = Transaction::whereDate('created_at', $today)->sum('total_price');
        $todayTransactionsCount = Transaction::whereDate('created_at', $today)->count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        // Low Stock Products (stock < 10)
        $lowStockProducts = Product::where('stock', '<', 10)->with('category')->get();
        
        // Recent Transactions
        $recentTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Chart data for current month (sales per day)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $salesData = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total')
            )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
            
        // Format chart labels and datasets
        $chartLabels = [];
        $chartValues = [];
        
        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth) && $currentDate->lte(Carbon::today())) {
            $formattedDate = $currentDate->format('Y-m-d');
            $chartLabels[] = $currentDate->format('d M');
            
            $daySale = $salesData->firstWhere('date', $formattedDate);
            $chartValues[] = $daySale ? (float) $daySale->total : 0;
            
            $currentDate->addDay();
        }

        return view('dashboard.index', compact(
            'todaySales',
            'todayTransactionsCount',
            'totalProducts',
            'totalCategories',
            'lowStockProducts',
            'recentTransactions',
            'chartLabels',
            'chartValues'
        ));
    }
}
