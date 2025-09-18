<!-- @extends('admin.layouts.app')

@section('title', 'System Configuration')
@section('page-title', 'System Configuration')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white shadow rounded-lg p-6 relative">
        <div class="absolute top-4 right-4">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-4 text-indigo-700">POS Settings</h3>
        <form id="posSettingsForm" class="space-y-4">
            <div>
                <label for="taxRate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                <input type="number" id="taxRate" placeholder="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                <input type="text" id="currency" placeholder="USD" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="receiptFormat" class="block text-sm font-medium text-gray-700">Receipt Format</label>
                <select id="receiptFormat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="simple">Simple</option>
                    <option value="detailed">Detailed</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow">Save POS Settings</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6 relative">
        <div class="absolute top-4 right-4">
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405M19 13V9a7 7 0 00-14 0v4l-2 2h18l-2-2z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-4 text-yellow-700">Notification Settings</h3>
        <form id="notificationSettingsForm" class="space-y-4">
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="lowStockAlerts" class="rounded text-yellow-500">
                <label for="lowStockAlerts" class="text-gray-700">Low Stock Alerts</label>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="salesReports" class="rounded text-yellow-500">
                <label for="salesReports" class="text-gray-700">Daily Sales Reports</label>
            </div>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow">Save Notification Settings</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6 relative">
        <div class="absolute top-4 right-4">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 1.343 3 3m-3-3C10.343 8 9 9.343 9 11m3-3v6m-6 4h12"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-4 text-green-700">Payment Methods Setup</h3>
        <form id="paymentMethodsForm" class="space-y-4">
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="cashPayment" class="rounded text-green-500" checked>
                <label for="cashPayment" class="text-gray-700">Cash</label>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="cardPayment" class="rounded text-green-500">
                <label for="cardPayment" class="text-gray-700">Credit / Debit Card</label>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="mobilePayment" class="rounded text-green-500">
                <label for="mobilePayment" class="text-gray-700">Mobile Payment (Apple/Google Pay)</label>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">Save Payment Methods</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6 relative">
        <div class="absolute top-4 right-4">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4h18V7M3 11v10h18V11H3z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-4 text-purple-700">Store / Branch Information</h3>
        <form id="storeInfoForm" class="space-y-4">
            <div>
                <label for="storeName" class="block text-sm font-medium text-gray-700">Store Name</label>
                <input type="text" id="storeName" placeholder="Main Branch" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label for="storeAddress" class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" id="storeAddress" placeholder="123 Main St, City" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label for="storePhone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" id="storePhone" placeholder="+1 555 1234" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow">Save Store Info</button>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const forms = ['posSettingsForm', 'notificationSettingsForm', 'paymentMethodsForm', 'storeInfoForm'];

        forms.forEach(id => {
            const form = document.getElementById(id);
            form.addEventListener('submit', e => {
                e.preventDefault();

                // Mock save behavior: show toast / alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded shadow-lg animate-fadeInOut';
                alertDiv.innerText = `${id.replace('Form', '').replace(/([A-Z])/g, ' $1')} saved successfully!`;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    alertDiv.remove();
                }, 2500);
            });
        });
    });
</script>

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
@endsection -->