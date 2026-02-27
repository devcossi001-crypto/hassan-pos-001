<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Format for query to include full last day
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $user = auth()->user();
        $isCashier = $user->isCashier();
        $isManager = $user->isManager();
        $isSuperAdmin = $user->isSuperAdmin();
        $shouldRestrict = $isCashier && !$isManager && !$isSuperAdmin;

        $salesQuery = Sale::whereBetween('created_at', [$start, $end]);
        if ($shouldRestrict) {
            $salesQuery->where('cashier_id', $user->id);
        }
        $sales = $salesQuery->with(['cashier', 'items.product'])->get();

        $summary = [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'cash_sales' => $sales->sum('cash_paid'),
            'mpesa_sales' => $sales->sum('mpesa_paid'),
            'card_sales' => $sales->sum('card_paid'),
            'average_transaction' => $sales->count() > 0 ? $sales->sum('total_amount') / $sales->count() : 0,
        ];

        $topProductsQuery = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$start, $end]);

        if ($shouldRestrict) {
            $topProductsQuery->where('sales.cashier_id', $user->id);
        }

        $topProducts = $topProductsQuery->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.line_total) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('reports.sales', compact('summary', 'topProducts', 'startDate', 'endDate'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $user = auth()->user();
        $isCashier = $user->isCashier();
        $isManager = $user->isManager();
        $isSuperAdmin = $user->isSuperAdmin();
        $shouldRestrict = $isCashier && !$isManager && !$isSuperAdmin;

        // Revenue from sales
        $revenueQuery = Sale::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed');
        if ($shouldRestrict) {
            $revenueQuery->where('cashier_id', $user->id);
        }
        $revenue = $revenueQuery->sum('total_amount');

        // Cost of goods sold
        $cogsQuery = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->where('sales.status', 'completed');
        if ($shouldRestrict) {
            $cogsQuery->where('sales.cashier_id', $user->id);
        }
        $cogs = $cogsQuery->sum(DB::raw('sale_items.quantity * products.cost_price'));

        // Expenses
        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved');
        if ($shouldRestrict) {
            $expensesQuery->where('recorded_by', $user->id);
        }
        $expenses = $expensesQuery->sum('amount');

        // Expense breakdown
        $expenseBreakdownQuery = DB::table('expenses')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved');
        if ($shouldRestrict) {
            $expenseBreakdownQuery->where('recorded_by', $user->id);
        }
        $expenseBreakdown = $expenseBreakdownQuery->select('category_name', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category_name')
            ->orderByDesc('total_amount')
            ->get();

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;

        return view('reports.pnl', compact('revenue', 'cogs', 'grossProfit', 'expenses', 'netProfit', 'expenseBreakdown', 'startDate', 'endDate'));
    }
}
