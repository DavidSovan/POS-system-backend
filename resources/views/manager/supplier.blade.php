@extends('manager.layouts.app')

@section('title', 'Supplier Management')
@section('page-title', 'Supplier Management')

@section('content')
<!-- Supplier Header -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Supplier List</h2>
        <button onclick="openSupplierModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition">+ Add Supplier</button>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-sm text-green-800 uppercase bg-green-100">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Contact Person</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Address</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="suppliersTable" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add / Edit Supplier Modal -->
<div id="supplierModal" class="fixed inset-0 flex bg-black/50 backdrop-blur-sm hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 relative animate-fadeIn">
        <h3 class="text-xl font-semibold text-gray-800 mb-4" id="supplierModalTitle">Add / Edit Supplier</h3>
        <button onclick="closeSupplierModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form id="supplierForm" class="space-y-4">
            <input type="hidden" id="supplierId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplierName" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                    <input type="text" id="supplierName" placeholder="Supplier Name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>

                <div>
                    <label for="contactPerson" class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <input type="text" id="contactPerson" placeholder="Contact Person"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>

                <div>
                    <label for="supplierEmail" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="supplierEmail" placeholder="email@example.com"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>

                <div>
                    <label for="supplierPhone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" id="supplierPhone" placeholder="+123 456 7890"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>

                <div class="md:col-span-2">
                    <label for="supplierAddress" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" id="supplierAddress" placeholder="Address"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                </div>
                <div>
                    <label for="supplierStatus" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="supplierStatus"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeSupplierModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Supplier Deliveries / Purchases Modal -->
<div id="supplierTransactionModal" class="fixed inset-0 flex bg-black/50 backdrop-blur-sm hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative animate-fadeIn">
        <h3 class="text-xl font-semibold text-gray-800 mb-4" id="transactionModalTitle">Record Delivery / Purchase</h3>
        <button onclick="closeSupplierTransactionModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form id="supplierTransactionForm" class="space-y-4">
            <input type="hidden" id="transactionId">
            <input type="hidden" id="supplierTransactionId">

            <div>
                <label for="transactionType" class="block text-sm font-medium text-gray-700">Transaction Type</label>
                <select id="transactionType" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                    <option value="delivery">Delivery</option>
                    <option value="purchase">Purchase</option>
                </select>
            </div>

            <div>
                <label for="transactionQuantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="transactionQuantity" placeholder="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label for="transactionReason" class="block text-sm font-medium text-gray-700">Reason / Note</label>
                <input type="text" id="transactionReason" placeholder="Optional"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeSupplierTransactionModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
@vite(['resources/js/manager/supplier.js'])
@endsection