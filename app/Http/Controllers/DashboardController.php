<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\Shift;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isCashier = $user->isCashier();
        $isManager = $user->isManager();
        $isSuperAdmin = $user->isSuperAdmin();
        
        // Final flag: Restrict if cashier AND NOT manager/admin
        $shouldRestrict = $isCashier && !$isManager && !$isSuperAdmin;

        // Get today's sales
        $todaySalesQuery = Sale::whereDate('created_at', today());
        if ($shouldRestrict) {
            $todaySalesQuery->where('cashier_id', $user->id);
        }
        $todaySales = $todaySalesQuery->sum('total_amount');

        // Get total products (visible to all)
        $totalProducts = Product::where('is_active', true)->count();

        // Get low stock products (visible to all)
        $lowStockProducts = Product::whereRaw('quantity_in_stock <= reorder_level')
            ->where('is_active', true)
            ->count();

        // Get active shift
        $activeShift = Shift::where('status', 'open')
            ->where('cashier_id', $user->id)
            ->first();

        // Get recent sales
        $recentSalesQuery = Sale::latest();
        if ($shouldRestrict) {
            $recentSalesQuery->where('cashier_id', $user->id)
                             ->whereDate('created_at', today());
        }
        $recentSales = $recentSalesQuery->take(10)
            ->with(['cashier', 'customer'])
            ->get();

        // Month-to-date Profit & Loss details
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $mtdRevenueQuery = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed');
        if ($shouldRestrict) {
            $mtdRevenueQuery->where('cashier_id', $user->id);
        }
        $mtdRevenue = $mtdRevenueQuery->sum('total_amount');

        $mtdCogsQuery = \DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
            ->where('sales.status', 'completed');
        if ($shouldRestrict) {
            $mtdCogsQuery->where('sales.cashier_id', $user->id);
        }
        $mtdCogs = $mtdCogsQuery->sum(\DB::raw('sale_items.quantity * products.cost_price'));

        $mtdExpensesQuery = \App\Models\Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'approved');
        if ($shouldRestrict) {
            $mtdExpensesQuery->where('recorded_by', $user->id);
        }
        $mtdExpenses = $mtdExpensesQuery->sum('amount');

        $mtdProfit = ($mtdRevenue - $mtdCogs) - $mtdExpenses;

        return view('dashboard.index', [
            'todaySales' => $todaySales,
            'totalProducts' => $totalProducts,
            'lowStockProducts' => $lowStockProducts,
            'activeShift' => $activeShift,
            'recentSales' => $recentSales,
            'mtdProfit' => $mtdProfit,
            'mtdRevenue' => $mtdRevenue,
        ]);
    }
}
