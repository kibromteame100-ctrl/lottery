<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'             => $request->name,
            'phone'            => $request->phone,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'preferred_locale' => $request->header('Accept-Language', 'en'),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('auth.registered_successfully'),
            'data'    => [
                'user'  => $this->userResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required_without:phone', 'nullable', 'email'],
            'phone'    => ['required_without:email', 'nullable', 'string'],
            'password' => ['required'],
        ]);

        $field = isset($credentials['phone']) ? 'phone' : 'email';
        $user  = User::where($field, $credentials[$field])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                $field => [__('auth.failed')],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => __('auth.account_suspended'),
            ], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('auth.login_successful'),
            'data'    => [
                'user'  => $this->userResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => __('auth.logged_out'),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->userResource($request->user()),
        ]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'preferred_locale' => ['nullable', 'in:en,am,ti'],
            'theme_preference' => ['nullable', 'in:light,dark,system'],
        ]);

        $request->user()->update(array_filter($data));

        return response()->json([
            'success' => true,
            'message' => __('auth.preferences_updated'),
            'data'    => $this->userResource($request->user()->fresh()),
        ]);
    }

    private function userResource(User $user): array
    {
        return [
            'id'               => $user->id,
            'name'             => $user->name,
            'phone'            => $user->phone,
            'email'            => $user->email,
            'preferred_locale' => $user->preferred_locale,
            'theme_preference' => $user->theme_preference,
            'status'           => $user->status,
        ];
    }
}
