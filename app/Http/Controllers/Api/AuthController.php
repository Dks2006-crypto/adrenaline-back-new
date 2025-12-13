<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'name' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        $clientRole = Role::firstOrCreate(['name' => 'client']);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'phone' => $request->phone,
            'role_id' => $clientRole->id,
            'confirmed_at' => now(),
        ]);

        $token = auth('jwt')->login($user);

        return $this->respondWithToken($token, $user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = auth('jwt')->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Неверные данные'], 401);
        }

        $user = auth('jwt')->user();

        return $this->respondWithToken($token, $user);
    }

    public function me()
    {
        return response()->json(auth('jwt')->user());
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $user = auth('jwt')->user();

        // Удаляем старый аватар, если был
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Аватар обновлён',
            'avatar_url' => asset('storage/' . $path),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth('jwt')->user();

        $data = $request->validate([
            'bio' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $user->update($data);

        return response()->json(['success' => true]);
    }

    // ← ВСПОМОГАТЕЛЬНЫЙ МЕТОД — пиши один раз и используй везде
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'user' => $user,           // ← ВСЁ! Laravel сам сериализует с avatar_url
            'token' => $token,
        ]);
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Вышли']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);
            return response()->json(['token' => $newToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Токен недействителен'], 401);
        }
    }
}
