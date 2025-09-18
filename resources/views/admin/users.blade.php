@extends('admin.layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">All Users</h2>
    <button onclick="openModal('userModal', false)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow transition">Add User</button>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="w-full text-sm text-left text-gray-600">
        <thead class="text-sm text-blue-800 uppercase bg-blue-100">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Last Active</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody id="usersTable" class="bg-white divide-y divide-gray-200">
            <tr>
                <td colspan="7" class="text-center py-6 text-gray-400">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Delete Modal -->
<div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Confirm Delete</h2>
        <p class="mb-6 text-gray-600" id="delete-message"></p>
        <div class="flex justify-end space-x-4">
            <button id="cancel-delete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden justify-center items-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 relative animate-fadeIn">
        <h3 id="modalTitle" class="text-xl font-semibold text-gray-800 mb-4">Add User</h3>
        <button onclick="closeModal('userModal')" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form id="userForm" class="space-y-4">
            <input type="hidden" id="userId">

            <div>
                <label for="userName" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="userName" placeholder="Full Name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label for="userEmail" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="userEmail" placeholder="example@email.com"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div>
                <label for="userPassword" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="userPassword" placeholder="••••••••"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="userRole" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="userRole"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                        <option value="manager">Manager</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>
                <div id="statusContainer">
                    <label for="userStatus" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="userStatus"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 h-12 px-3">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeModal('userModal')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/admin/users.js'])
@endsection