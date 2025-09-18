<!-- resources/views/manager/components/sidebar.blade.php -->
@php
$currentRoute = Route::currentRouteName();
@endphp

<aside class="w-64 bg-indigo-700 text-white min-h-screen flex flex-col">
    <div class="px-6 py-8">
        <h1 class="text-2xl font-bold mb-8">POS Manager</h1>
        <nav class="space-y-2">
            <a href="{{ route('manager.dashboard') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'manager.dashboard' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Dashboard
            </a>
            <a href="{{ route('manager.products') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'manager.products' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Products
            </a>
            <a href="{{ route('manager.inventory') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'manager.inventory' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Inventory
            </a>
            <a href="{{ route('manager.supplier') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'manager.supplier' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Supplier
            </a>
            <a href="{{ route('manager.discounts') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'manager.discounts' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Discounts
            </a>
            <a href="{{ route('manager.reports') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                    {{ $currentRoute == 'manager.reports' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Reports
            </a>
        </nav>
    </div>
</aside>