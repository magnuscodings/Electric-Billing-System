<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::find(Auth::id());

        if ($user) {
            $user->name = $request->name;
            $user->save();
        }

        return redirect()->route('view.profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::find(Auth::id());

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('view.profile')->with('success', 'Password updated successfully!');
    }
}
