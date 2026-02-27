<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CartItem;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SalesController extends Controller
{
    public function __construct(private SalesService $salesService)
    {}

    public function index(): View
    {
        $query = Sale::with(['cashier', 'customer']);
        $user = auth()->user();

        // Only restrict if the user is a cashier AND NOT a manager or super admin
        if ($user->isCashier() && !$user->isManager() && !$user->isSuperAdmin()) {
            $query->where('cashier_id', $user->id)
                  ->whereDate('created_at', today());
        }

        $sales = $query->latest()
            ->paginate(20);

        return view('sales.index', ['sales' => $sales]);
    }

    public function create(): View
    {
        $activeShift = Shift::where('status', 'open')
            ->where('cashier_id', auth()->id())
            ->first();

        $products = Product::where('is_active', true)
            ->with('category')
            ->get();

        $customers = Customer::all();

        return view('sales.pos', [
            'products' => $products,
            'customers' => $customers,
            'shift' => $activeShift,
            'hasActiveShift' => (bool)$activeShift
        ]);
    }

    // NEW: Get all products for POS
    public function getProducts(): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'barcode', 'imei', 'selling_price as price', 'quantity_in_stock as stock')
            ->orderBy('name')
            ->get();
        
        return response()->json($products);
    }

    // UPDATED: Search products for POS
    public function searchProduct(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            // Return all products if no query
            return $this->getProducts();
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('imei', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'sku as code', 'barcode', 'imei', 'selling_price as price', 'quantity_in_stock as stock')
            ->orderBy('name')
            ->take(20)
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'cart_data' => 'required|string',
            'payment_method' => 'required|in:cash,mpesa,card,credit',
            'mpesa_phone' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_tendered' => 'nullable|numeric|min:0',
        ]);

        try {
            // Get active shift
            $shift = Shift::where('status', 'open')
                ->where('cashier_id', auth()->id())
                ->firstOrFail();

            // Parse items from JSON
            $items = json_decode($validated['cart_data'], true);
            if (empty($items)) {
                return back()->with('error', 'No items in sale');
            }

            // Convert items to expected format for SalesService
            $formattedItems = [];
            $subtotal = 0;
            foreach ($items as $item) {
                $lineTotal = $item['quantity'] * $item['price'];
                $subtotal += $lineTotal;
                $formattedItems[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'line_total' => $lineTotal,
                    'discount_per_item' => 0
                ];
            }

            $saleData = [
                'cashier_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_amount' => (float)$validated['tax_amount'],
                'discount_amount' => (float)($validated['discount'] ?? 0),
                'total_amount' => (float)$validated['total_amount'],
                'primary_payment_method' => $validated['payment_method'],
                'cash_paid' => $validated['payment_method'] === 'cash' ? (float)$validated['total_amount'] : 0,
                'mpesa_paid' => $validated['payment_method'] === 'mpesa' ? (float)$validated['total_amount'] : 0,
                'card_paid' => $validated['payment_method'] === 'card' ? (float)$validated['total_amount'] : 0,
                'change_amount' => $validated['payment_method'] === 'cash' ? 
                    max(0, (float)($validated['amount_tendered'] ?? 0) - (float)$validated['total_amount']) : 0,
                'notes' => $validated['mpesa_phone'] ?? null,
                'shift_id' => $shift->id,
                'items' => $formattedItems
            ];

            $sale = $this->salesService->createSale($saleData);

            // Clear the database cart for this user
            CartItem::where('user_id', auth()->id())->delete();

            return redirect()->route('sales.receipt', $sale)
                ->with('success', 'Sale completed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating sale: ' . $e->getMessage());
        }
    }

    public function show(Sale $sale): View
    {
        return view('sales.show', [
            'sale' => $sale->load(['items.product', 'cashier', 'customer']),
        ]);
    }

    public function receipt(Sale $sale)
    {
        return view('sales.receipt', [
            'sale' => $sale->load(['items.product', 'cashier', 'customer']),
        ]);
    }
}