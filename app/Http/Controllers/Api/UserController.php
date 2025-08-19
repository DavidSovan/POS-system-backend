<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of users (Admin only).
     */
    public function index(Request $request): JsonResponse
    {
        if (!Gate::allows('viewAny', User::class)) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view users',
            ], 403);
        }

        $query = User::with('role');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ],
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_USER_NOT_FOUND',
                'message' => 'User not found',
            ], 404);
        }

        if (!Gate::allows('view', $user)) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to view this user',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'description' => $user->role->description,
                    ],
                    'status' => $user->status,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
            ],
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_USER_NOT_FOUND',
                'message' => 'User not found',
            ], 404);
        }

        if (!Gate::allows('update', $user)) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to update this user',
            ], 403);
        }

        $currentUser = auth()->user();
        $updateData = [];

        // Basic fields that users can update for themselves
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Role and status can only be changed by admins and not on themselves
        if ($request->has('role_id') || $request->has('status')) {
            if (!Gate::allows('manageRoleAndStatus', $user)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR_UNAUTHORIZED',
                    'message' => 'You are not authorized to change role or status',
                ], 403);
            }

            if ($request->has('role_id')) {
                $updateData['role_id'] = $request->role_id;
            }

            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }
        }

        try {
            $user->update($updateData);
            $user->load('role'); // Reload relationship

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => [
                            'id' => $user->role->id,
                            'name' => $user->role->name,
                        ],
                        'status' => $user->status,
                        'updated_at' => $user->updated_at,
                    ],
                    'message' => 'User updated successfully',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_USER_UPDATE_FAILED',
                'message' => 'Failed to update user',
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_USER_NOT_FOUND',
                'message' => 'User not found',
            ], 404);
        }

        if (!Gate::allows('delete', $user)) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_UNAUTHORIZED',
                'message' => 'You are not authorized to delete this user',
            ], 403);
        }

        try {
            $user->delete();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'User deleted successfully',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_USER_DELETE_FAILED',
                'message' => 'Failed to delete user',
            ], 500);
        }
    }
}