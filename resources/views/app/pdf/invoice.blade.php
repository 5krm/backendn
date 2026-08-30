<html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>


    <style>
        @page {
            margin: 0
        }

        .ar-text {
            font-family: "tajawal", sans-serif;
        }

        html {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        body,
        html h1,
        h2,
        p {
            margin: 0;
            padding: 0;
        }

 

        .invoice {
            padding: 0 3rem;
            border-top: 8px solid #00cc99;
        }

        .invoice-header {
            padding-top: 2rem;
        }

        .invoice-header img {
            width: 220px;
        }

        .company-info {
            font-size: 0.9rem;
            margin-bottom: 2rem;
            font-style: italic;
        }


        .invoice-meta>div {
            padding: 0.5rem 0;
        }

        .invoice-meta>div {
            border-bottom: 1px solid #ccc;
        }

        .invoice-meta>div:last-child {
            border-bottom: none;
        }

        .invoice-meta .invoice-number {
            font-weight: 600;
        }

        .invoice-customer {
            margin-top: 1.5rem;
        }

        .invoice-customer .info {
            margin-top: 0.5rem;
        }

        .invoice-customer .info>p {
            margin: 0;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .invoice-items {
            margin-top: 2rem;
        }

        .invoice-items table {
            width: 100%;
        }

        .invoice-items td:first-child,
        .invoice-items th:first-child {
            text-align: left;
        }

        .invoice-items td,
        .invoice-items th {
            text-align: right;
            line-height: 1.25rem;
            font-size: 0.875rem;
            padding-top: 0.875rem;
            padding-bottom: 0.875rem;
            padding-right: 0.75rem;
        }

        .invoice-items th {
            font-weight: 600;
            border-bottom: 1px solid #ccc;
            color: rgb(17, 24, 39);
        }

        .invoice-items td {
            color: rgb(51, 55, 61);
        }

        .invoice-total {
            margin-top: 1rem;
        }

        .invoice-total .total-item {
            width: 100%;
            padding: 0.3rem 0;
        }

        .invoice-total .total-item {
            border-bottom: 1px solid #ccc;
        }

        .invoice-total .total-item:last-child {
            border-bottom: none;
        }

        .invoice-total .total-item.due {
            font-weight: 600;
        }
    </style>

</head>

<body>
    <div class="invoice">
        <div class="invoice-header">
            <h1 style="display: inline-block">Invoice</h1>
            <div style="float: right">
                <img src="{{ public_path('assets/images/English_Logo.png') }}" alt="Portal365" />
            </div>
        </div>
        <div class="company-info">
            <p>NGO Academy</p>
            <p>info@portal365.org</p>
            <p>+12 1234567890</p>
        </div>
        <div class="invoice-meta">
            <div class="invoice-number">
                <span>Invoice number</span>
                <span style="float: right">#{{ 'INV-' . $data->id . '-' . $user->id }}</span>
            </div>
            <div class="invoice-issue">
                <span>Date of issue</span>
                <span style="float: right">{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
            <div class="invoice-due">
                <span>Date of due</span>
                <span style="float: right">{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="invoice-customer">
            <h2>Bill to</h2>
            <div class="info">
                <p class="ar-text">{{ $user->name }}</p>
                <p>{{ $user->email }}</p>
                <p>{{ $user->phone }}</p>
            </div>
        </div>

        <div class="invoice-items">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="ar-text">
                                {{ $data->title }}
                            </div>
                        </td>
                        <td>1</td>
                        <td>{{ Number::currency($invoice->amount_subtotal) }}</td>
                        <td>{{ Number::currency($invoice->amount_subtotal) }}</td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="invoice-total">
            <div style="width: 30%; float: right">
                <div class="total-item">
                    <span>Subtotal</span>
                    <span style="float: right">{{ Number::currency($invoice->amount_subtotal) }}</span>
                </div>
                <div class="total-item">
                    <span>discount</span>
                    <span
                        style="float: right">{{ Number::currency($invoice->amount_subtotal - $invoice->amount_total) }}</span>
                </div>
                <div class="total-item">
                    <span>Total</span>
                    <span style="float: right">{{ Number::currency($invoice->amount_total) }}</span>
                </div>
                <div class="total-item due">
                    <span>Amount due</span>
                    <span style="float: right">{{ Number::currency($invoice->amount_total) }}</span>
                </div>
            </div>
        </div>
</body>

</html>
