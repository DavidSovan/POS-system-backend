<!-- resources/views/admin/components/topbar.blade.php -->
<header class="bg-white shadow p-4 flex justify-between items-center">
    <h2 class="text-xl font-semibold">@yield('page-title', 'Dashboard')</h2>
    <div class="flex items-center space-x-4">
        <span id="userName">Admin</span>
        <button id="logoutBtn" class="px-4 py-2 bg-red-500 text-white rounded">Logout</button>
    </div>
</header>