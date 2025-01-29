<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function showAccounts(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->with('role')
            ->paginate(10);

        // Append search query to pagination links
        if ($search) {
            $users->appends(['search' => $search]);
        }

        return view('accounts', compact('users'));
    }

    public function showRegister()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required|exists:roles,id'
            ]);

            $user = DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id' => $request->role_id,
                    'is_first_login' => true
                ]);

                return $user;
            });

            // Redirect with success message if transaction is successful
            return redirect()->route('view.accounts')->with([
                'success' => 'User has been added successfully!',
                'name' => $user->name,
            ]);
        } catch (\Exception $e) {
            // If there is any error, roll back the transaction and return an error message
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $user = User::findOrFail($id);

            // Update only the name
            $user->update([
                'name' => $request->name,
            ]);

            return back()->with('success', 'User updated successfully')->with('name', $user->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $request->user()->createToken('auth-token')->plainTextToken;
            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'isFirstLogin' => $user->is_first_login ?? true
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true
        ]);
    }

    public function getCurrentUser(Request $request)
    {
        $user = $request->user()->load('role');
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function apiChangePassword(Request $request)
    {
        try {
            $request->validate([
                'currentPassword' => 'required',
                'newPassword' => 'required|min:8',
                'newPasswordConfirmation' => 'required|same:newPassword',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated'
                ], 401);
            }

            if (!Hash::check($request->currentPassword, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->newPassword)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateFcmToken(Request $request)
    {
        try {
            $request->validate([
                'fcmToken' => 'required|string'
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated'
                ], 401);
            }

            $user->fcm_token = $request->fcmToken;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if (
                auth()->id() === $user->id ||
                $user->id === 1 ||
                $user->role->slug === 'client'
            ) {
                return back()->with('error', 'Cannot delete this user.');
            }

            $user->delete();

            return back()->with('success', 'User deleted successfully')->with('name', $user->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    public function changeFirstTimePassword(Request $request)
    {
        try {
            $request->validate([
                'newPassword' => 'required|min:8',
                'newPasswordConfirmation' => 'required|same:newPassword',
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($request->newPassword),
                'is_first_login' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
