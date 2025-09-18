@extends('manager.layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Current Inventory -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Current Inventory</h3>
            <p class="text-2xl font-bold text-indigo-600" id="inventoryCount">--</p>
            <p class="text-sm text-gray-500 mt-1">Items in stock</p>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Low Stock Items</h3>
            <p class="text-2xl font-bold text-red-600" id="lowStockCount">--</p>
            <p class="text-sm text-gray-500 mt-1">Items need restocking</p>
        </div>

        <!-- Pending Supplier Orders -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pending Supplier Orders</h3>
            <p class="text-2xl font-bold text-yellow-600" id="pendingSuppliersCount">--</p>
            <p class="text-sm text-gray-500 mt-1">Awaiting confirmation</p>
        </div>

        <!-- Pending Discount Approvals -->
        <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col justify-between">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pending Discounts</h3>
            <p class="text-2xl font-bold text-green-600" id="pendingDiscountsCount">--</p>
            <p class="text-sm text-gray-500 mt-1">Discount requests awaiting approval</p>
        </div>
    </div>

    <!-- Low Stock Table -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Low Stock Items</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-sm text-green-800 uppercase bg-green-100">
                    <tr>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Stock Left</th>
                        <th class="px-4 py-3">Reorder Level</th>
                    </tr>
                </thead>
                <tbody id="lowStockTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">Loading data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Latest Pending Suppliers -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Pending Supplier Approvals</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-sm text-yellow-800 uppercase bg-yellow-100">
                    <tr>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Phone</th>
                    </tr>
                </thead>
                <tbody id="pendingSuppliersTable" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">Loading pending suppliers...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Latest Pending Discounts -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Pending Discount Requests</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-sm text-green-800 uppercase bg-green-100">
                    <tr>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">Requested By</th>
                        <th class="px-4 py-3">Discount (%)</th>
                        <th class="px-4 py-3">Reason</th>
                    </tr>
                </thead>
                <tbody id="pendingDiscountsTable" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">Loading pending discounts...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
@vite(['resources/js/manager/dashboard.js'])
@endsection