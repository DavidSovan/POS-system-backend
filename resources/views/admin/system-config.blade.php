@extends('admin.layouts.app')

@section('title', 'System Configuration')
@section('page-title', 'System Configuration')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Store Information -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold text-indigo-600 mb-4">Store Information</h3>
        <form id="storeForm" class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700">Store Name</label>
                <input type="text" id="storeName" placeholder="Your store name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Contact Number</label>
                <input type="text" id="storeContact" placeholder="+1 234 567 890"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Address</label>
                <textarea id="storeAddress" rows="3" placeholder="Store address"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3"></textarea>
            </div>

            <button type="button" id="saveStoreInfo"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow mt-2">Save Store Info</button>
        </form>

        <!-- Live Preview -->
        <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
            <h4 class="font-semibold text-indigo-700">Preview:</h4>
            <p><strong>Name:</strong> <span id="previewStoreName">—</span></p>
            <p><strong>Contact:</strong> <span id="previewStoreContact">—</span></p>
            <p><strong>Address:</strong> <span id="previewStoreAddress">—</span></p>
        </div>
    </div>

    <!-- Tax & Currency Settings -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold text-indigo-600 mb-4">Tax & Currency</h3>
        <form id="taxCurrencyForm" class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700">Tax Rate (%)</label>
                <input type="number" id="taxRate" placeholder="0.0" step="0.01" min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Currency</label>
                <select id="currency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                    <option value="USD">USD - $</option>
                    <option value="KHR">KHR - ៛</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Payment Methods</label>
                <div class="space-y-2 mt-1">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-indigo-600" value="Cash" checked>
                        <span class="ml-2 text-gray-700">Cash</span>
                    </label>

                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-indigo-600" value="CreditCard">
                        <span class="ml-2 text-gray-700">Credit/Debit Card (Visa/MasterCard)</span>
                    </label>

                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-indigo-600" value="ABAPay">
                        <span class="ml-2 text-gray-700">ABA Pay</span>
                    </label>

                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-indigo-600" value="Wing">
                        <span class="ml-2 text-gray-700">Wing Mobile Wallet</span>
                    </label>
                </div>
            </div>

            <button type="button" id="saveTaxCurrency"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow mt-2">Save Settings</button>
        </form>

        <!-- Live Preview -->
        <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
            <h4 class="font-semibold text-indigo-700">Preview:</h4>
            <p><strong>Tax Rate:</strong> <span id="previewTaxRate">—</span>%</p>
            <p><strong>Currency:</strong> <span id="previewCurrency">—</span></p>
            <p><strong>Payment Methods:</strong> <span id="previewPaymentMethods">—</span></p>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    @keyframes fadeInOut {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        10% {
            opacity: 1;
            transform: translateY(0);
        }

        90% {
            opacity: 1;
            transform: translateY(0);
        }

        100% {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    .animate-fadeInOut {
        animation: fadeInOut 2.5s ease forwards;
    }
</style>
@endpush

@push('scripts')
@vite(['resources/js/admin/system-config.js'])
@endpush