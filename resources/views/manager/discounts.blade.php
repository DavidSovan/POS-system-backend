@extends('manager.layouts.app')

@section('title', 'Discounts Management')
@section('page-title', 'Discounts Management')

@section('content')
<!-- Discounts Header -->
<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Discount Requests</h2>
    <button onclick="openDiscountModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition">+ Add Discount</button>
</div>

<!-- Discount Requests Table -->
<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full text-sm text-left text-gray-600 divide-y divide-gray-200">
        <thead class="text-sm text-green-800 uppercase bg-green-100">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Requested By</th>
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Discount (%)</th>
                <th class="px-4 py-3">Reason</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody id="discountRequestsTable" class="bg-white divide-y divide-gray-200">
            <tr>
                <td colspan="7" class="text-center py-6 text-gray-400">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Add/Edit Discount Modal -->
<div id="discountModal" class="fixed inset-0 flex bg-black/50 backdrop-blur-sm hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 relative animate-fadeIn">
        <h3 class="text-xl font-semibold text-gray-800 mb-4" id="discountModalTitle">Add / Edit Discount</h3>
        <button onclick="closeDiscountModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form id="discountForm" class="space-y-4">
            <input type="hidden" id="discountId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="productName" class="block text-sm font-medium text-gray-700">Product</label>
                    <input type="text" id="productName" placeholder="Product Name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>

                <div>
                    <label for="discountPercentage" class="block text-sm font-medium text-gray-700">Discount (%)</label>
                    <input type="number" step="0.01" id="discountPercentage" placeholder="0.00"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>

                <div class="md:col-span-2">
                    <label for="discountReason" class="block text-sm font-medium text-gray-700">Reason / Note</label>
                    <textarea id="discountReason" placeholder="Reason for discount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeDiscountModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
@vite(['resources/js/manager/discounts.js'])
@endsection