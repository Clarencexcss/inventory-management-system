<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Analytics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            color: #8B0000;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #8B0000;
            color: white;
        }
        .text-right {
            text-align: right;
        }
        .profit-positive {
            color: #28a745;
            font-weight: bold;
        }
        .profit-negative {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>ButcherPro - Sales Analytics Report</h1>
    <p style="text-align: center; margin-bottom: 20px;">Generated on: {{ date('F d, Y') }}</p>

    <h2>Yearly Summary (2020-2024)</h2>
    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th class="text-right">Total Sales</th>
                <th class="text-right">Total Expenses</th>
                <th class="text-right">Net Profit</th>
                <th class="text-right">Margin %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($yearlySummary as $year)
                @php
                    $margin = $year->total_sales > 0 ? ($year->net_profit / $year->total_sales) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ $year->year }}</strong></td>
                    <td class="text-right">₱{{ number_format($year->total_sales, 2) }}</td>
                    <td class="text-right">₱{{ number_format($year->total_expenses, 2) }}</td>
                    <td class="text-right {{ $year->net_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        ₱{{ number_format($year->net_profit, 2) }}
                    </td>
                    <td class="text-right">{{ number_format($margin, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Top-Selling Products</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Category</th>
                <th class="text-right">Qty Sold</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->category_name }}</td>
                    <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                    <td class="text-right">₱{{ number_format($product->total_revenue, 2) }}</td>
                    <td class="text-right {{ $product->total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        ₱{{ number_format($product->total_profit, 2) }}
                    </td>
                    <td class="text-right">{{ number_format($product->profit_margin, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>ButcherPro Management System - Sales Analytics Report</p>
        <p>This report is confidential and intended for internal use only</p>
    </div>
</body>
</html>
