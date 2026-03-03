<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        html {
            background: #d0d0d0;
        }

        body {
            width: 58mm;
            margin: 20px auto;
            padding: 8px;
            font-family: "Courier New", monospace;
            font-size: 11px;
            line-height: 1.5;
            background: #fff;
            box-shadow: 0 2px 16px rgba(0,0,0,0.25);
        }

        @media print {
            html { background: none; }
            body {
                margin: 0;
                padding: 2px;
                box-shadow: none;
                font-size: 10px;
                line-height: 1.2;
            }
        }

        .c  { text-align: center; }
        .b  { font-weight: bold; }
        .r  { text-align: right; }

        hr {
            border: none;
            border-top: 1px dashed black;
            margin: 2px 0;
        }

        table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }

        td { padding: 1px 0; }

        .total {
            font-weight: bold;
            font-size: 12px;
        }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="c b">AnisaHub</div>
    <div class="c">#{{ $sale->id }} &bull; {{ $sale->created_at?->format('d/m/y h:iA') }}</div>
    <div class="c">{{ $sale->cashier?->name ?? 'N/A' }}</div>

    @if($sale->customer)
        <hr>
        <div>{{ $sale->customer->name }}@if($sale->customer->phone) &bull; {{ $sale->customer->phone }}@endif</div>
    @endif

    <hr>

    <table>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ Str::limit($item->product?->name ?? 'Item', 18) }}</td>
                    <td class="r">x{{ $item->quantity }}</td>
                    <td class="r">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <table>
        <tr><td>Subtotal</td><td class="r">{{ number_format($sale->subtotal, 2) }}</td></tr>
        @if($sale->discount_amount > 0)
            <tr><td>Discount</td><td class="r">-{{ number_format($sale->discount_amount, 2) }}</td></tr>
        @endif
        @if($sale->tax_amount > 0)
            <tr><td>Tax</td><td class="r">{{ number_format($sale->tax_amount, 2) }}</td></tr>
        @endif
        @if($sale->cash_paid > 0)
            <tr><td>Cash</td><td class="r">{{ number_format($sale->cash_paid, 2) }}</td></tr>
        @endif
        @if($sale->mpesa_paid > 0)
            <tr><td>M-Pesa</td><td class="r">{{ number_format($sale->mpesa_paid, 2) }}</td></tr>
        @endif
        @if($sale->card_paid > 0)
            <tr><td>Card</td><td class="r">{{ number_format($sale->card_paid, 2) }}</td></tr>
        @endif
        @if($sale->change_amount > 0)
            <tr class="b"><td>Change</td><td class="r">{{ number_format($sale->change_amount, 2) }}</td></tr>
        @endif
    </table>

    <hr>

    <div class="total c">KSh {{ number_format($sale->total_amount, 2) }}</div>

    <hr>

    <div class="c">Thank you! Come again.</div>

    <div class="no-print c" style="margin-top:6px">
        <button onclick="window.print()">Print</button>
    </div>

</body>
</html>