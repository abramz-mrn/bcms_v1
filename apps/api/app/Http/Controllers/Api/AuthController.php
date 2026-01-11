<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash:: check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->locked !== 'active') {
            return $this->errorResponse('Your account is locked or inactive.', 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        AuditLog::log('login', 'User', $user->id, null, null, "User {$user->name} logged in");

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'group' => $user->userGroup->name,
                'permissions' => $user->userGroup->permissions,
            ],
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        AuditLog::log('logout', 'User', $user->id, null, null, "User {$user->name} logged out");
        
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['userGroup', 'company']);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'nik' => $user->nik,
            'photo' => $user->photo,
            'group' => [
                'id' => $user->userGroup->id,
                'name' => $user->userGroup->name,
            ],
            'permissions' => $user->userGroup->permissions,
            'company' => [
                'id' => $user->company->id,
                'name' => $user->company->name,
                'alias' => $user->company->alias,
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'photo' => 'sometimes|image|max:2048',
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'sometimes|string|min:8|confirmed',
        ]);

        $oldData = $user->only(['name', 'phone', 'email']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users/photos', 'public');
            $user->photo = $path;
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect.', 422);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        AuditLog::log(
            'update',
            'User',
            $user->id,
            $oldData,
            $user->only(['name', 'phone', 'email']),
            "User {$user->name} updated their profile"
        );

        return $this->successResponse($user, 'Profile updated successfully');
    }
}