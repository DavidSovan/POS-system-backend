@extends('manager.layouts.app')

@section('title', 'Product Management')
@section('page-title', 'Product Management')

@section('content')
<!-- Low Stock Alert -->
<div id="lowStockAlert" class="hidden mb-4 p-4 rounded text-white bg-red-500 font-medium">
    ⚠ Some products are low in stock!
</div>

<!-- Product Table -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Product List</h2>
        <button onclick="openProductModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition">+ Add Product</button>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-sm text-green-800 uppercase bg-green-100">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Unit Cost</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="productTable" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock Adjustment / Add Product Modal -->
<div id="productModal" class="fixed inset-0 flex bg-black/50 backdrop-blur-sm hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative animate-fadeIn">
        <h3 class="text-xl font-semibold text-gray-800 mb-4" id="productModalTitle" onclick="openProductModal()">Add / Edit Product</h3>
        <button onclick="closeProductModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form id="productForm" class="space-y-4">
            <input type="hidden" id="productId">

            <div>
                <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" id="productName" placeholder="Product Name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label for="productCategory" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="productCategory"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                    <!-- Options will be populated dynamically via JS -->
                </select>
            </div>


            <div>
                <label for="productSku" class="block text-sm font-medium text-gray-700">SKU</label>
                <input type="text" id="productSku" placeholder="Unique product code"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label for="productReorderLevel" class="block text-sm font-medium text-gray-700">Reorder Level</label>
                <input type="number" id="productReorderLevel" placeholder="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>


            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="productStock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" id="productStock" placeholder="0"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>
                <div>
                    <label for="productCost" class="block text-sm font-medium text-gray-700">Unit Cost</label>
                    <input type="number" step="0.01" id="productCost" placeholder="0.00"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeProductModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/manager/products.js'])
@endsection