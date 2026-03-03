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

            width: 48mm;

            margin: 30px auto;

            padding: 4mm 2mm;

            font-family: "Courier New", "Lucida Console", monospace;

            font-size: 11px;

            line-height: 1.4;

            background: #fff;

            box-shadow: 0 2px 16px rgba(0,0,0,0.25);

            color: #000;

            -webkit-font-smoothing: none;

        }

        @media print {

            html { background: none; }

            body {

                width: 48mm;

                margin: 0;

                padding: 1mm;

                box-shadow: none;

                font-size: 11px;

                line-height: 1.3;

            }

        }

        .c  { text-align: center; }

        .b  { font-weight: bold; }

        .r  { text-align: right; }

        .div-dashed {

            border: none;

            border-top: 1px dashed #000;

            margin: 4px 0;

        }

        .div-double {

            border: none;

            border-top: 2px double #000;

            margin: 4px 0;

        }

        .div-solid {

            border: none;

            border-top: 1px solid #000;

            margin: 4px 0;

        }

        .brand {

            text-align: center;

            font-weight: bold;

            font-size: 16px;

            letter-spacing: 3px;

            text-transform: uppercase;

            padding: 2px 0 0;

        }

        .tagline {

            text-align: center;

            font-size: 9px;

            letter-spacing: 1px;

            margin-bottom: 2px;

        }

        .meta {

            text-align: center;

            font-size: 10px;

            line-height: 1.5;

        }

        .meta span {

            display: block;

        }

        .customer {

            font-size: 10px;

            padding: 2px 0;

        }

        table {

            width: 100%;

            font-size: 11px;

            border-collapse: collapse;

        }

        table td {

            padding: 1px 0;

            vertical-align: top;

        }

        .col-name { width: 54%; text-align: left; }

        .col-qty  { width: 14%; text-align: center; }

        .col-amt  { width: 32%; text-align: right; }

        .summary td {

            font-size: 11px;

            padding: 1px 0;

        }

        .summary .label { text-align: left; }

        .summary .value { text-align: right; }

        .grand-total {

            text-align: center;

            font-weight: bold;

            font-size: 15px;

            padding: 3px 0;

            letter-spacing: 1px;

        }

        .footer {

            text-align: center;

            font-size: 9px;

            padding: 4px 0 2px;

            line-height: 1.5;

        }

        @media print {

            .no-print { display: none !important; }

        }

        .no-print {

            text-align: center;

            margin-top: 8px;

        }

        .no-print button {

            font-family: "Courier New", monospace;

            font-size: 12px;

            padding: 4px 16px;

            cursor: pointer;

            border: 1px solid #000;

            background: #fff;

        }
</style>
</head>
<body onload="window.print()">
 
    <div class="brand">AnisaHub</div>
<div class="tagline">*** YOUR TRUSTED STORE ***</div>
<hr class="div-solid">
<div class="meta">
<span>Receipt #{{ $sale->id }}</span>
<span>{{ $sale->created_at?->format('d/m/Y h:i A') }}</span>
<span>Served by: {{ $sale->cashier?->name ?? 'N/A' }}</span>
</div>
 
    @if($sale->customer)
<hr class="div-dashed">
<div class="customer">

            Customer: {{ $sale->customer->name }}

            @if($sale->customer->phone)
<br>Tel: {{ $sale->customer->phone }}

            @endif
</div>

    @endif
 
    <hr class="div-double">
<table>
<thead>
<tr style="font-size:10px; font-weight:bold;">
<td class="col-name">ITEM</td>
<td class="col-qty">QTY</td>
<td class="col-amt">AMOUNT</td>
</tr>
</thead>
<tbody>
<tr><td colspan="3"><hr class="div-dashed" style="margin:1px 0;"></td></tr>

            @foreach($sale->items as $item)
<tr>
<td class="col-name">{{ Str::limit($item->product?->name ?? 'Item', 18) }}</td>
<td class="col-qty">{{ $item->quantity }}</td>
<td class="col-amt">{{ number_format($item->line_total, 2) }}</td>
</tr>

            @endforeach
</tbody>
</table>
 
    <hr class="div-double">
<table class="summary">
<tr>
<td class="label">Subtotal</td>
<td class="value">{{ number_format($sale->subtotal, 2) }}</td>
</tr>

        @if($sale->discount_amount > 0)
<tr>
<td class="label">Discount</td>
<td class="value">-{{ number_format($sale->discount_amount, 2) }}</td>
</tr>

        @endif

        @if($sale->tax_amount > 0)
<tr>
<td class="label">Tax</td>
<td class="value">{{ number_format($sale->tax_amount, 2) }}</td>
</tr>

        @endif
</table>
<hr class="div-dashed">
 
    <div class="grand-total">KSh {{ number_format($sale->total_amount, 2) }}</div>
<hr class="div-dashed">
 
    <table class="summary">

        @if($sale->cash_paid > 0)
<tr>
<td class="label">Cash</td>
<td class="value">{{ number_format($sale->cash_paid, 2) }}</td>
</tr>

        @endif

        @if($sale->mpesa_paid > 0)
<tr>
<td class="label">M-Pesa</td>
<td class="value">{{ number_format($sale->mpesa_paid, 2) }}</td>
</tr>

        @endif

        @if($sale->card_paid > 0)
<tr>
<td class="label">Card</td>
<td class="value">{{ number_format($sale->card_paid, 2) }}</td>
</tr>

        @endif

        @if($sale->change_amount > 0)
<tr class="b">
<td class="label">Change</td>
<td class="value">{{ number_format($sale->change_amount, 2) }}</td>
</tr>

        @endif
</table>
 
    <hr class="div-solid">
<div class="footer">

        Thank you for shopping with us!<br>

         

        *** COME AGAIN ***
</div>
 
    
 
</body>
</html>
 