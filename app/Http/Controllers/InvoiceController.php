<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with(['customer', 'payments'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_invoices' => Invoice::count(),
            'pending' => Invoice::whereIn('status', ['pending', 'sent', 'overdue'])->count(),
            'partially_paid' => Invoice::where('status', 'partially_paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'total_outstanding' => Invoice::whereIn('status', ['pending', 'sent', 'overdue', 'partially_paid'])
                ->sum('amount_due'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create(): View
    {
        $customers = Customer::where('can_buy_on_credit', true)->orderBy('name')->get();
        return view('invoices.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $invoiceData = $request->validated();
        $invoiceData['created_by'] = auth()->id();
        $invoiceData['status'] = 'draft';
        $invoiceData['amount_paid'] = 0;
        $invoiceData['amount_due'] = $invoiceData['total_amount'];
        $invoiceData['invoice_number'] = $this->generateInvoiceNumber();

        $invoice = Invoice::create($invoiceData);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'payments.receivedBy', 'createdBy']);
        
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        if ($invoice->status !== 'draft') {
            abort(403, 'Can only edit draft invoices');
        }

        $customers = Customer::where('can_buy_on_credit', true)->orderBy('name')->get();
        
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Can only edit draft invoices');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        $invoiceData = $request->validated();
        $invoiceData['amount_due'] = $invoiceData['total_amount'] - $invoice->amount_paid;

        $invoice->update($invoiceData);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully');
    }

    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'cancelled' || $invoice->status === 'paid') {
            return back()->with('error', 'Cannot add payments to this invoice');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->amount_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,mpesa,card,bank_transfer,cheque',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
            'received_by' => auth()->id(),
        ]);

        // Update invoice status
        $invoice->updateStatus();

        return back()->with('success', 'Payment recorded successfully');
    }

    public function send(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status === 'draft') {
            $invoice->markAsSent();
            return back()->with('success', 'Invoice marked as sent');
        }

        return back()->with('error', 'Only draft invoices can be sent');
    }

    public function cancel(Invoice $invoice): RedirectResponse
    {
        if ($invoice->amount_paid > 0) {
            return back()->with('error', 'Cannot cancel invoices with payments');
        }

        $invoice->markAsCancelled();
        return back()->with('success', 'Invoice cancelled successfully');
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Y');
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->latest()
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}

