<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TradeInController extends Controller
{
    public function index(): View
    {
        $tradeIns = Product::where('origin', 'Trade In')
            ->with('category')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_trade_ins' => Product::where('origin', 'Trade In')->count(),
            'total_value' => Product::where('origin', 'Trade In')->sum('total_cost'),
            'in_stock' => Product::where('origin', 'Trade In')->where('quantity_in_stock', '>', 0)->count(),
            'sold_out' => Product::where('origin', 'Trade In')->where('quantity_in_stock', 0)->count(),
        ];

        return view('trade-ins.index', compact('tradeIns', 'stats'));
    }

    public function create(): View
    {
        return view('trade-ins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'imei' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'category' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0|max:9999999999.99',
            'selling_price' => 'required|numeric|min:0|max:9999999999.99',
            'quantity_in_stock' => 'required|integer|min:1',
            'reorder_level' => 'nullable|integer|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $productData = $request->only([
            'name', 'description', 'sku', 'imei', 'barcode',
            'cost_price', 'selling_price', 'quantity_in_stock', 'reorder_level'
        ]);

        // Find or create category by name
        $category = Category::firstOrCreate(
            ['name' => $request->category],
            ['is_active' => true]
        );
        $productData['category_id'] = $category->id;
        
        // Set origin as Trade In
        $productData['origin'] = 'Trade In';
        $productData['is_active'] = true;

        // Calculate total cost
        $productData['total_cost'] = $productData['cost_price'] * $productData['quantity_in_stock'];

        // Add customer info to notes if provided
        if ($request->customer_name || $request->customer_phone) {
            $customerInfo = "Trade-In Customer: ";
            if ($request->customer_name) {
                $customerInfo .= $request->customer_name;
            }
            if ($request->customer_phone) {
                $customerInfo .= " (" . $request->customer_phone . ")";
            }
            
            $productData['description'] = trim(($productData['description'] ?? '') . "\n\n" . $customerInfo);
        }

        if ($request->notes) {
            $productData['description'] = trim(($productData['description'] ?? '') . "\n\nNotes: " . $request->notes);
        }

        $product = Product::create($productData);

        return redirect()->route('trade-ins.show', $product)
            ->with('success', 'Trade-in product recorded successfully');
    }

    public function show(Product $tradeIn): View
    {
        // Ensure this is actually a trade-in product
        if ($tradeIn->origin !== 'Trade In') {
            abort(404);
        }

        $tradeIn->load(['category', 'saleItems.sale']);
        
        return view('trade-ins.show', compact('tradeIn'));
    }

    public function edit(Product $tradeIn): View
    {
        // Ensure this is actually a trade-in product
        if ($tradeIn->origin !== 'Trade In') {
            abort(404);
        }

        $categories = Category::orderBy('name')->get();
        return view('trade-ins.edit', compact('tradeIn', 'categories'));
    }

    public function update(Request $request, Product $tradeIn): RedirectResponse
    {
        // Ensure this is actually a trade-in product
        if ($tradeIn->origin !== 'Trade In') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $tradeIn->id,
            'imei' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0|max:9999999999.99',
            'selling_price' => 'required|numeric|min:0|max:9999999999.99',
            'quantity_in_stock' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        $productData = $request->only([
            'name', 'description', 'sku', 'imei', 'barcode', 'category_id',
            'cost_price', 'selling_price', 'quantity_in_stock', 'reorder_level'
        ]);

        $tradeIn->update($productData);

        return redirect()->route('trade-ins.show', $tradeIn)
            ->with('success', 'Trade-in product updated successfully');
    }
}
