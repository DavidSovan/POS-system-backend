@extends('manager.layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<!-- Report Tabs -->
<div class="mb-6">
    <ul class="flex border-b border-gray-200 text-gray-600">
        <li class="mr-6">
            <button class="pb-2 border-b-2 border-transparent hover:border-indigo-600" onclick="showReportTab('sales')">Sales</button>
        </li>
        <li class="mr-6">
            <button class="pb-2 border-b-2 border-transparent hover:border-indigo-600" onclick="showReportTab('products')">Product Performance</button>
        </li>
        <li class="mr-6">
            <button class="pb-2 border-b-2 border-transparent hover:border-indigo-600" onclick="showReportTab('inventory')">Inventory</button>
        </li>
        <li class="mr-6">
            <button class="pb-2 border-b-2 border-transparent hover:border-indigo-600" onclick="showReportTab('discounts')">Discounts</button>
        </li>
    </ul>
</div>

<!-- Sales Reports -->
<div id="sales" class="report-tab flex flex-col w-full px-2 sm:px-4 md:px-6 py-4">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Sales Reports</h3>
    <div class="mb-4">
        <label for="salesPeriod" class="block text-sm font-medium text-gray-700">Select Period</label>
        <select id="salesPeriod" class="mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
        </select>
    </div>
    <div id="salesChart" class="bg-white rounded-lg shadow p-4 h-64 w-full">
        <!-- Replace with Chart.js or ApexCharts -->
        <canvas id="salesChartCanvas" class="w-full h-full"></canvas>
    </div>
</div>

<!-- Product Performance -->
<div id="products" class="report-tab hidden flex flex-col w-full px-2 sm:px-4 md:px-6 py-4">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Product Performance</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow p-4">
        <table class="min-w-full text-sm text-left text-gray-600 divide-y divide-gray-200">
            <thead class="text-sm text-green-800 uppercase bg-green-100">
                <tr>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Units Sold</th>
                    <th class="px-4 py-3">Revenue</th>
                    <th class="px-4 py-3">Stock</th>
                </tr>
            </thead>
            <tbody id="productPerformanceTable" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Inventory Reports -->
<div id="inventory" class="report-tab hidden flex flex-col w-full px-2 sm:px-4 md:px-6 py-4">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Inventory Reports</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow p-4">
        <table class="min-w-full text-sm text-left text-gray-600 divide-y divide-gray-200">
            <thead class="text-sm text-yellow-800 uppercase bg-yellow-100">
                <tr>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Stock Level</th>
                    <th class="px-4 py-3">Low Stock Threshold</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody id="inventoryReportTable" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Discounts Reports -->
<div id="discounts" class="report-tab hidden flex flex-col w-full px-2 sm:px-4 md:px-6 py-4">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Discounts & Sales Effect</h3>
    <div class="overflow-x-auto bg-white rounded-lg shadow p-4">
        <table class="min-w-full text-sm text-left text-gray-600 divide-y divide-gray-200">
            <thead class="text-sm text-purple-800 uppercase bg-purple-100">
                <tr>
                    <th class="px-4 py-3">Discount Name</th>
                    <th class="px-4 py-3">Start Date</th>
                    <th class="px-4 py-3">End Date</th>
                    <th class="px-4 py-3">Affected Products</th>
                    <th class="px-4 py-3">Revenue Impact</th>
                </tr>
            </thead>
            <tbody id="discountReportTable" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/manager/reports.js'])
@endsection