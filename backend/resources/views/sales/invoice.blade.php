<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        /* Thermal Printer Optimization */
        @page {
            size: 80mm 200mm;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 5mm;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px dashed #000;
            margin-top: 5px;
            padding-top: 5px;
        }

        .border-bottom {
            border-bottom: 1px dashed #000;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            border-bottom: 1px solid #000;
        }

        .text-right {
            text-align: right;
        }

        .logo {
            width: 50px;
            height: auto;
            filter: grayscale(100%);
            margin-bottom: 5px;
        }

        .total-section {
            font-size: 14px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
        }

        /* Queue Number Styling */
        .queue-box {
            padding: 10px;
            margin: 10px 0;
            text-align: center;
        }

        .queue-number {
            font-size: 40px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="center">
        <img src="https://i.pinimg.com/736x/fd/9c/fc/fd9cfc4eb0b0498a45b73343f1aa04ed.jpg" class="logo"><br>
        <span class="bold" style="font-size: 16px;">MY POS STORE</span><br>
        <span>Street 123, Phnom Penh</span><br>
        <span>Tel: 012 345 678</span>
    </div>

    <div class="border-top" style="margin-top: 10px;">
        <div style="display: flex; justify-content: space-between;">
            <span>INV:{{ $sale->invoice_no }}</span>
            <span>{{ is_string($sale->created_at) ? date('d-M-Y H:i', strtotime($sale->created_at)) : $sale->created_at->format('d-M-Y H:i') }}</span>
        </div>
        <div>Cashier: {{ auth()->user()->name ?? 'System' }}</div>
    </div>

    <table style="margin-top: 10px;">
        <thead>
            <tr>
                <th width="40%">Item</th>
                <th width="10%" class="text-right">Qty</th>
                <th width="25%" class="text-right">Price</th>
                <th width="25%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->sellable->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right bold">${{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section border-top">
        <div style="display: flex; justify-content: space-between;">
            <span>Sub-Total:</span>
            <span>${{ number_format($total, 2) }}</span>
        </div>
        @php
            $exchangeRate = 4100; // You should ideally fetch this from your settings table
            $totalRiel = ceil(($total * $exchangeRate) / 100) * 100; // Round to nearest 100
            // Check if this is a coffee shop order or has a queue number
            $showQueue = isset($sale->queue_number);
        @endphp
        <div class="border-top" style="margin-top: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 2px 0;">
                <span class="bold" style="font-size: 14px;">TOTAL (KHR):</span>
                <span class="bold" style="font-size: 18px;">៛{{ number_format($totalRiel, 0, '.', ',') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 2px 0;">
                <span class="bold" style="font-size: 14px;">TOTAL (USD):</span>
                <span class="bold" style="font-size: 20px;">${{ number_format($total, 2) }}</span>
            </div>
            <div class="center" style="font-size: 10px; margin-top: 5px; font-style: italic;">
                Rate: $1 = {{ number_format($exchangeRate) }} KHR
            </div>
        </div>
    </div>

    @if ($showQueue)
        <div class="queue-box">
            <div style="font-size: 12px;">WAITING NUMBER</div>
            <div class="queue-number">#{{ str_pad($sale->queue_number, 2, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size: 10px;">Please wait for your number to be called</div>
        </div>
    @endif

    <div class="border-top center footer">
        <p>Follow us on Telegram: @my_mart</p>
        <p class="bold">*** THANK YOU ***</p>
        <p>{{ date('Y-m-d H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            // Optional: window.close(); // Closes tab after print dialog
        }
    </script>
</body>

</html>
