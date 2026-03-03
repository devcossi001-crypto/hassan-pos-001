<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        /* ── Reset everything ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 80mm auto;
            margin: 0mm;
        }

        html {
            background: #d0d0d0;
        }

        body {
            width: 72mm;
            max-width: 72mm;
            margin: 20px auto;
            padding: 3mm;
            font-family: "Courier New", "Lucida Console", monospace;
            font-size: 12px;
            line-height: 1.5;
            background: #fff;
            color: #000;
            box-shadow: 0 2px 16px rgba(0,0,0,0.25);
            -webkit-font-smoothing: none;
            -moz-osx-font-smoothing: unset;
        }

        @media print {
            html, body {
                background: none !important;
                box-shadow: none !important;
            }
            body {
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 1mm;
                font-size: 12px;
                line-height: 1.4;
            }
            .no-print {
                display: none !important;
            }
        }

        /* ── Dividers ── */
        .line-dash {
            border: none;
            overflow: hidden;
            font-size: 12px;
            line-height: 1;
            letter-spacing: 2px;
            text-align: center;
            margin: 3px 0;
        }
        .line-dash::after {
            content: "- - - - - - - - - - - - - - - - - - - - - - - - - - - -";
        }
        .line-equal {
            border: none;
            overflow: hidden;
            font-size: 12px;
            line-height: 1;
            text-align: center;
            margin: 3px 0;
        }
        .line-equal::after {
            content: "================================================";
        }
        .line-star {
            border: none;
            overflow: hidden;
            font-size: 12px;
            line-height: 1;
            text-align: center;
            margin: 3px 0;
        }
        .line-star::after {
            content: "****************";
        }

        /* ── Header ── */
        .brand {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            letter-spacing: 4px;
            text-transform: uppercase;
            padding: 4px 0 0;
        }
        .tagline {
            text-align: center;
            font-size: 11px;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .meta {
            text-align: center;
            font-size: 11px;
            line-height: 1.6;
        }

        /* ── Customer ── */
        .customer-info {
            font-size: 11px;
            padding: 2px 0;
            line-height: 1.5;
        }

        /* ── Tables ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        td {
            padding: 1px 0;
            vertical-align: top;
        }

        /* Item columns */
        .item-name { text-align: left; }
        .item-qty  { text-align: center; width: 36px; }
        .item-amt  { text-align: right; }

        /* Summary columns */
        .sum-label { text-align: left; }
        .sum-value { text-align: right; }

        /* ── Column headers ── */
        .col-head td {
            font-weight: bold;
            font-size: 11px;
            padding-bottom: 2px;
        }

        /* ── Grand total ── */
        .grand-total {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            padding: 4px 0;
            letter-spacing: 1px;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 10px;
            padding: 4px 0 2px;
            line-height: 1.6;
        }

        /* ── Print button ── */
        .no-print {
            text-align: center;
            margin: 12px 0 4px;
        }
        .no-print button {
            font-family: "Courier New", monospace;
            font-size: 13px;
            padding: 6px 20px;
            cursor: pointer;
            border: 1px solid #000;
            background: #fff;
        }
        .no-print button:hover {
            background: #000;
            color: #fff;
        }
    </style>
</head>
<body>

    {{-- ════════ HEADER ════════ --}}
    <div class="brand">AnisaHub</div>
    <div class="tagline">* Your Trusted Store *</div>
    <div class="line-equal"></div>
    <div class="meta">
        Receipt: #{{ $sale->id }}<br>
        Date: {{ $sale->created_at?->format('d/m/Y h:i A') }}<br>
        Cashier: {{ $sale->cashier?->name ?? 'N/A' }}
    </div>

    {{-- ════════ CUSTOMER ════════ --}}
    @if($sale->customer)
        <div class="line-dash"></div>
        <div class="customer-info">
            Customer: {{ $sale->customer->name }}
            @if($sale->customer->phone)
                <br>Tel: {{ $sale->customer->phone }}
            @endif
        </div>
    @endif

    {{-- ════════ ITEMS ════════ --}}
    <div class="line-equal"></div>
    <table>
        <tr class="col-head">
            <td class="item-name">ITEM</td>
            <td class="item-qty">QTY</td>
            <td class="item-amt">AMOUNT</td>
        </tr>
    </table>
    <div class="line-dash"></div>
    <table>
        @foreach($sale->items as $item)
            <tr>
                <td class="item-name">
                    {{ Str::limit($item->product?->name ?? 'Item', 20) }}
                    @php $imei = $item->imei ?? $item->product?->imei ?? null; @endphp
                    @if($imei)
                        <div style="font-size:10px; margin-top:2px;">IMEI: {{ $imei }}</div>
                    @endif
                </td>
                <td class="item-qty">x{{ $item->quantity }}</td>
                <td class="item-amt">{{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </table>

    {{-- ════════ SUBTOTALS ════════ --}}
    <div class="line-equal"></div>
    <table>
        <tr>
            <td class="sum-label">Subtotal</td>
            <td class="sum-value">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        @if($sale->discount_amount > 0)
            <tr>
                <td class="sum-label">Discount</td>
                <td class="sum-value">-{{ number_format($sale->discount_amount, 2) }}</td>
            </tr>
        @endif
        @if($sale->tax_amount > 0)
            <tr>
                <td class="sum-label">Tax</td>
                <td class="sum-value">{{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
        @endif
    </table>

    {{-- ════════ GRAND TOTAL ════════ --}}
    <div class="line-star"></div>
    <div class="grand-total">KSh {{ number_format($sale->total_amount, 2) }}</div>
    <div class="line-star"></div>

    {{-- ════════ PAYMENTS ════════ --}}
    <table>
        @if($sale->cash_paid > 0)
            <tr>
                <td class="sum-label">Cash Paid</td>
                <td class="sum-value">{{ number_format($sale->cash_paid, 2) }}</td>
            </tr>
        @endif
        @if($sale->mpesa_paid > 0)
            <tr>
                <td class="sum-label">M-Pesa</td>
                <td class="sum-value">{{ number_format($sale->mpesa_paid, 2) }}</td>
            </tr>
        @endif
        @if($sale->card_paid > 0)
            <tr>
                <td class="sum-label">Card</td>
                <td class="sum-value">{{ number_format($sale->card_paid, 2) }}</td>
            </tr>
        @endif
        @if($sale->change_amount > 0)
            <tr style="font-weight:bold;">
                <td class="sum-label">Change</td>
                <td class="sum-value">{{ number_format($sale->change_amount, 2) }}</td>
            </tr>
        @endif
    </table>
 
    <div class="line-dash"></div>

    {{-- ════════ PRINT BUTTON ════════ --}}
    <div class="no-print">
        <button onclick="printReceipt()">&#128424; Print Receipt</button>
    </div>

    <script>
        function printReceipt() {
            window.print();
        }

        // Auto-print when opened
        window.onload = function() {
            // Small delay to ensure styles are fully loaded
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>

</body>
</html>