@extends('admin.layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <!-- Tabs -->
    <div class="flex border-b mb-6 gap-8">
        <button onclick="switchTab('sales')" id="tab-sales" class="tab-btn active">Sales</button>
        <button onclick="switchTab('users')" id="tab-users" class="tab-btn">User Activity</button>
        <button onclick="switchTab('inventory')" id="tab-inventory" class="tab-btn">Inventory</button>
        <button onclick="switchTab('finance')" id="tab-finance" class="tab-btn">Finance</button>
    </div>

    <!-- Tab Content -->
    <div id="tabContent-sales" class="tab-content">
        <h3 class="text-xl font-bold mb-4">Sales Performance</h3>
        <canvas id="salesChart" class="w-full h-64"></canvas>
        <div class="mt-4 flex space-x-3">
            <button class="export-btn">Export to PDF</button>
            <button class="export-btn">Export to Excel</button>
        </div>
    </div>

    <div id="tabContent-users" class="tab-content hidden">
        <h3 class="text-xl font-bold mb-4">User Activity</h3>
        <canvas id="usersChart" class="w-full h-64"></canvas>
        <div class="mt-4 flex space-x-3">
            <button class="export-btn">Export to PDF</button>
            <button class="export-btn">Export to Excel</button>
        </div>
    </div>

    <div id="tabContent-inventory" class="tab-content hidden">
        <h3 class="text-xl font-bold mb-4">Inventory Reports</h3>
        <canvas id="inventoryChart" class="w-full h-64"></canvas>
        <div class="mt-4 flex space-x-3">
            <button class="export-btn">Export to PDF</button>
            <button class="export-btn">Export to Excel</button>
        </div>
    </div>

    <div id="tabContent-finance" class="tab-content hidden">
        <h3 class="text-xl font-bold mb-4">Financial Summaries</h3>
        <canvas id="financeChart" class="w-full h-64"></canvas>
        <div class="mt-4 flex space-x-3">
            <button class="export-btn">Export to PDF</button>
            <button class="export-btn">Export to Excel</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/admin/reports-analytics.js'])
@endsection

@push('styles')
<style>
    .tab-btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        background-color: #f3f4f6;
    }

    .tab-btn.active {
        color: #4f46e5;
        border-color: #4f46e5;
    }

    .export-btn {
        background-color: #4f46e5;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background 0.2s;
    }

    .export-btn:hover {
        background-color: #4338ca;
    }
</style>
@endpush