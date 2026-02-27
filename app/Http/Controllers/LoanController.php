<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LoanController extends Controller
{
    public function index(): View
    {
        $loans = Loan::with(['sale', 'creator'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_loans' => Loan::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'total_outstanding' => Loan::where('status', 'active')->sum('balance'),
            'overdue_loans' => Loan::overdue()->count(),
        ];

        return view('loans.index', compact('loans', 'stats'));
    }

    public function create(): View
    {
        $sales = Sale::whereDoesntHave('loan')
            ->latest()
            ->take(50)
            ->get();

        return view('loans.create', compact('sales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id|unique:loans,sale_id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_id_number' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0|max:9999999999.99',
            'paid_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'monthly_payment' => 'nullable|numeric|min:0|max:9999999999.99',
            'duration_months' => 'nullable|integer|min:1|max:120',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $totalAmount = $request->total_amount;
        $paidAmount = $request->paid_amount ?? 0;
        $balance = $totalAmount - $paidAmount;

        $loanData = $request->only([
            'sale_id', 'customer_name', 'customer_phone', 'customer_id_number',
            'total_amount', 'monthly_payment', 'duration_months', 'start_date', 'notes'
        ]);

        $loanData['paid_amount'] = $paidAmount;
        $loanData['balance'] = $balance;
        $loanData['created_by'] = auth()->id();

        if ($request->duration_months && $request->start_date) {
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $loanData['expected_end_date'] = $startDate->addMonths((int)$request->duration_months);
        }

        $loan = Loan::create($loanData);

        // If there's an initial payment, record it
        if ($paidAmount > 0) {
            LoanPayment::create([
                'loan_id' => $loan->id,
                'amount' => $paidAmount,
                'payment_date' => now(),
                'payment_method' => 'cash',
                'notes' => 'Initial down payment',
                'received_by' => auth()->id(),
            ]);
        }

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan recorded successfully');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['sale.items.product', 'payments.receiver', 'creator']);
        return view('loans.show', compact('loan'));
    }

    public function addPayment(Request $request, Loan $loan): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:9999999999.99',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        LoanPayment::create([
            'loan_id' => $loan->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'received_by' => auth()->id(),
        ]);

        // Update loan balance
        $loan->paid_amount += $request->amount;
        $loan->balance -= $request->amount;

        if ($loan->balance <= 0) {
            $loan->status = 'completed';
        }

        $loan->save();

        return back()->with('success', 'Payment recorded successfully');
    }

    public function markDefaulted(Loan $loan): RedirectResponse
    {
        $loan->update(['status' => 'defaulted']);
        return back()->with('success', 'Loan marked as defaulted');
    }
}
