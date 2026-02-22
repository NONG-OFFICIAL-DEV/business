<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 80mm 200mm;
            margin: 0;
        }

        body {
            font-family: 'Noto Sans Khmer', sans-serif;
            width: 70mm;
            /* Reduced width to prevent right-edge clipping */
            margin: 0 auto;
            padding: 5mm;
            font-size: 12px;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        /* Dashed Line Helper */
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
            width: 100%;
        }

        /* Use Tables for EVERYTHING horizontal */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-table th {
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .logo {
            width: 50px;
            margin-bottom: 5px;
        }

        /* Large Totals */
        .total-khr {
            font-size: 15px;
        }

        .total-usd {
            font-size: 15px;
        }

        .footer {
            font-size: 10px;
            margin-top: 15px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="center">
        <img src="https://i.pinimg.com/736x/fd/9c/fc/fd9cfc4eb0b0498a45b73343f1aa04ed.jpg" class="logo"><br>
        <span class="bold" style="font-size: 16px;">MY POS STORE</span><br>
        <span>Street 123, Phnom Penh</span><br>
        <span>Tel: 012 345 678</span>
    </div>

    <div class="divider" style="margin-top:10px"></div>

    <table>
        <tr>
            <td>INV:{{ $sale->invoice_no }}</td>
        </tr>
        <tr>
            <td>
                Date:{{ is_string($sale->created_at) ? date('d-M-y H:i', strtotime($sale->created_at)) : $sale->created_at->format('d-M-y H:i') }}
            </td>
        </tr>
        <tr>
            <td colspan="2">Cashier: {{ auth()->user()->name ?? 'System' }}</td>
        </tr>
    </table>

    <table class="item-table" style="margin-top: 10px;">
        <thead>
            <tr>
                <th align="left">Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td style="padding-top: 3px;">{{ $item->sellable->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right bold">${{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider" style="margin-top:10px"></div>

    <table>
        <tr>
            <td>Sub-Total:</td>
            <td class="text-right">${{ number_format($total, 2) }}</td>
        </tr>
    </table>

    @php
        $exchangeRate = 4100;
        $totalRiel = ceil(($total * $exchangeRate) / 100) * 100;
        $showQueue = isset($sale->queue_number) && $sale->queue_number > 0;
    @endphp

    <div class="divider"></div>

    <table>
        <tr>
            <td class="bold">TOTAL (KHR):</td>
            <td class="text-right bold total-khr">{{ number_format($totalRiel, 0, '.', ',') }}៛</td>
        </tr>
        <tr>
            <td class="bold">TOTAL (USD):</td>
            <td class="text-right bold total-usd">${{ number_format($total, 2) }}</td>
        </tr>
    </table>

    <div class="center" style="font-size: 10px; font-style: italic;">
        Rate: $1 = {{ number_format($exchangeRate) }} KHR
    </div>

    @if ($showQueue)
        <div class="divider"></div>
        <div class="center">
            <div style="font-size: 12px;">WAITING NUMBER</div>
            <div style="font-size: 44px; font-weight: bold;">#{{ str_pad($sale->queue_number, 2, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    @endif

    <div class="divider"></div>

    <div class="center footer">
        <p>Follow us on Telegram: @my_mart</p>
        <p class="bold">*** THANK YOU ***</p>
        <p>{{ date('Y-m-d H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
