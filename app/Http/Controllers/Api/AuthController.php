<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Authenticate user and return JWT token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            // Find user by email
            $user = User::with('role')->where('email', $credentials['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR_INVALID_CREDENTIALS',
                    'message' => 'Invalid credentials provided',
                ], 401);
            }

            // Check password
            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR_INVALID_CREDENTIALS',
                    'message' => 'Invalid credentials provided',
                ], 401);
            }

            // Check if user is active
            if (!$user->isActive()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR_ACCOUNT_INACTIVE',
                    'message' => 'Your account is not active. Please contact administrator.',
                ], 403);
            }

            // Generate token
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR_TOKEN_GENERATION_FAILED',
                    'message' => 'Could not create token',
                ], 500);
            }

            return $this->respondWithToken($token, $user);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_TOKEN_GENERATION_FAILED',
                'message' => 'Could not create token',
            ], 500);
        }
    }

    /**
     * Register new user (Admin only).
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Check if authenticated user is admin
        try {
            $user = User::create([
                'name'   => $request->name,
                'email'  => trim($request->email),
                'password' => Hash::make(trim($request->password)),
                'status'   => 'pending', // New users start as pending
            ]);

            // $user->load('role');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        // 'role'  => [
                        //     'id'   => $user->role->id,
                        //     'name' => $user->role->name,
                        // ],
                        'status' => $user->status,
                        'created_at' => $user->created_at,
                    ],
                    'message' => 'User created successfully',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_USER_CREATION_FAILED',
                'message' => 'Failed to create user',
            ], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'Successfully logged out'
                ],
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_LOGOUT_FAILED',
                'message' => 'Failed to logout, please try again',
            ], 500);
        }
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            $user = auth()->user();

            return $this->respondWithToken($newToken, $user);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR_TOKEN_REFRESH_FAILED',
                'message' => 'Could not refresh token',
            ], 401);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        $user->load('role');

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
                    'permissions' => $user->role->getPermissions(),
                    'created_at' => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token, User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => [
                        'id'   => $user->role->id,
                        'name' => $user->role->name,
                    ],
                    'status' => $user->status,
                    'permissions' => $user->role->getPermissions(),
                ],
            ],
        ]);
    }

    // public function success(mixed $data, string $message='', int $statusCode=200, string $status='', $option=[])
    // {
    //     $response = [
    //         'status' => $status,
    //         'data'   => $data ?? [],
    //         'message'=> $message
    //     ];
    //     if($option) $response['option'] = $option;
    //     return response()->json($response, $statusCode);
    // }

    // public function error(string $message='Something went wrong!', int $statusCode = 500, string $status='',  int | string $code='ERR_USER_CREATION_FAILED')
    // {
    //     $response = [
    //         'status' => $statusCode,
    //         'code'   => $code,
    //         'message'=> $message
    //     ];
    //     return response()->json($response, $status);
    // }
}