<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentRegisterController extends Controller
{
    public function show()
    {
        return view('auth.student-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lrn' => 'required|unique:students,lrn|digits:12',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'student',
            'password' => Hash::make($request->password),
        ]);

        Student::create([
            'user_id' => $user->id,
            'lrn' => $request->lrn,
        ]);

        auth()->login($user);

        return redirect('/student/dashboard');
    }
}
