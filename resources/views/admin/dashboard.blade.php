@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-indigo-50 text-indigo-800 shadow rounded-lg p-6 flex flex-col justify-between">
        <div class="flex items-center space-x-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 10h4l3 8 4-16 3 8h4"></path>
            </svg>
            <h3 class="text-lg font-bold">Total Sales</h3>
        </div>
        <p id="totalSales" class="text-3xl font-semibold mt-4">$0</p>
        <span class="text-sm text-gray-600 mt-1">This Month</span>
    </div>

    <div class="bg-green-50 text-green-800 shadow rounded-lg p-6 flex flex-col justify-between">
        <div class="flex items-center space-x-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5 13l4 4L19 7"></path>
            </svg>
            <h3 class="text-lg font-bold">Active Users</h3>
        </div>
        <p id="activeUsers" class="text-3xl font-semibold mt-4">0</p>
    </div>

    <div class="bg-yellow-50 text-yellow-800 shadow rounded-lg p-6 flex flex-col justify-between">
        <div class="flex items-center space-x-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 7h18M3 12h18M3 17h18"></path>
            </svg>
            <h3 class="text-lg font-bold">Low Stock Items</h3>
        </div>
        <p id="lowStock" class="text-3xl font-semibold mt-4">0</p>
    </div>

    <div class="bg-red-50 text-red-800 shadow rounded-lg p-6 flex flex-col justify-between">
        <div class="flex items-center space-x-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4l3 3"></path>
            </svg>
            <h3 class="text-lg font-bold">Pending Approvals</h3>
        </div>
        <p id="pendingApprovals" class="text-3xl font-semibold mt-4">0</p>
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-bold mb-4">Recent Activity</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700 font-medium">Time</th>
                    <th class="px-4 py-2 text-left text-gray-700 font-medium">User</th>
                    <th class="px-4 py-2 text-left text-gray-700 font-medium">Action</th>
                    <th class="px-4 py-2 text-left text-gray-700 font-medium">Details</th>
                </tr>
            </thead>
            <tbody id="recentActivity">
                <tr>
                    <td class="px-4 py-2 text-gray-500" colspan="4">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/admin/dashboard.js'])
@endsection