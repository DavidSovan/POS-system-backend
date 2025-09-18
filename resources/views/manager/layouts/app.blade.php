<!-- resources/views/manager/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manager Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js',
    "resources/js/manager/dashboard.js",
    "resources/js/manager/products.js",
    "resources/js/manager/inventory.js",
    "resources/js/manager/supplier.js",
    "resources/js/manager/discounts.js",
    "resources/js/manager/reports.js",
    ])
</head>


<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('manager.components.sidebar')

        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            @include('manager.components.topbar')

            <!-- Main Content -->
            <main class="p-6 flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>

</html>