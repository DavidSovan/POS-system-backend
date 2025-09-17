@extends('admin.layouts.app')

@section('title', 'Audit & Activity Log')
@section('page-title', 'Audit & Activity Log')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- User Sessions -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold mb-4 text-indigo-600">User Sessions</h3>
        <ul id="userSessions" class="space-y-3">
            <li class="text-gray-400 italic">Loading sessions...</li>
        </ul>
    </div>

    <!-- System Changes -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-xl font-semibold mb-4 text-indigo-600">System Changes</h3>
        <ul id="systemChanges" class="space-y-3">
            <li class="text-gray-400 italic">Loading changes...</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/admin/audit.js'])
@endpush