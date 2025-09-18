@extends('manager.layouts.app')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')

@section('content')
<!-- Low Stock Products -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Low Stock Products</h2>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-600" id="lowStockTable">
            <thead class="text-sm text-green-800 uppercase bg-green-100">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="stockModal" class="fixed inset-0 flex justify-center items-center bg-black/50 backdrop-blur-sm hidden z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative animate-fadeIn">
        <h3 id="stockModalTitle" class="text-xl font-semibold text-gray-800 mb-4"></h3>
        <button id="btnCloseStockModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form id="stockForm" class="space-y-4">
            <input type="hidden" id="productId">
            <input type="hidden" id="actionType">

            <div id="unitCostWrapper">
                <label class="block text-sm font-medium text-gray-700">Unit Cost (for Add Stock)</label>
                <input type="number" step="0.01" id="unitCost"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-12 px-3">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="quantity" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-12 px-3">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Reason</label>
                <input type="text" id="reason" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-12 px-3">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 px-3"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Reference</label>
                <input type="text" id="reference" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-12 px-3">
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" id="btnCancelStock" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- Stock Movements -->
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Stock Movements</h2>

    <div class="mb-4 flex items-center space-x-2">
        <input type="number" id="movementProductId" placeholder="Product ID (leave empty for all)" class="block w-72 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-10 px-3">
        <button id="btnLoadMovements" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md">Load Movements</button>
    </div>
    <!-- <div class="mb-4 flex items-center space-x-2">
        <input type="number" id="movementProductId"
            placeholder="Product ID (optional)"
            class="block w-48 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 h-10 px-3">
        <button id="btnLoadMovements"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md">Load</button>
    </div> -->

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-600" id="movementTable">
            <thead class="text-sm text-green-800 uppercase bg-green-100">
                <tr>
                    <th class="px-4 py-3">Time</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Qty</th>
                    <th class="px-4 py-3">Reason</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">No data</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/manager/inventory.js'])
@endsection