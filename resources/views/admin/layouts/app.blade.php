<!-- resources/views/admin/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js',
    "resources/js/admin/dashboard.js",
    "resources/js/admin/users.js",
    "resources/js/admin/system-config.js",
    "resources/js/admin/reports-analytics.js",
    "resources/js/admin/audit.js",
    ])
</head>


<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('admin.components.sidebar')

        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            @include('admin.components.topbar')

            <!-- Main Content -->
            <main class="p-6 flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>

</html>