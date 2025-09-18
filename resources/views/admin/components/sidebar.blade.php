<!-- resources/views/admin/components/sidebar.blade.php -->
@php
$currentRoute = Route::currentRouteName();
@endphp

<aside class="w-64 bg-indigo-700 text-white min-h-screen flex flex-col">
    <div class="px-6 py-8">
        <h1 class="text-2xl font-bold mb-8">POS Admin</h1>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'admin.dashboard' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.users') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'admin.users' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Users
            </a>
            <a href="{{ route('admin.system-config') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'admin.system-config' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                System Configuration
            </a>
            <a href="{{ route('admin.reports-analytics') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'admin.reports-analytics' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Report
            </a>
            <a href="{{ route('admin.audit') }}"
                class="block py-2 px-4 rounded-lg transition-colors
                      {{ $currentRoute == 'admin.audit' ? 'bg-indigo-500 shadow-lg' : 'hover:bg-indigo-600' }}">
                Audit / Activity Logs
            </a>
        </nav>
    </div>
</aside>