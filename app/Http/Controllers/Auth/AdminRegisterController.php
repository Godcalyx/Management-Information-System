<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminRegisterController extends Controller
{
    public function show()
    {
        return view('auth.register-admin');
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Create new admin user
    \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Hash::make($request->password),
        'role' => 'admin',
    ]);

    // Redirect to login with success message
    return redirect()->route('login.admin')->with('success', 'Registration successful! You may now log in.');
}
}
