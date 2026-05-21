<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    // Show list of admins
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        return view('superadmin.admins.index', compact('admins'));
    }

    // Activate admin
    public function activate(User $admin)
    {
        // Deactivate any other active admin
        User::where('role', 'admin')->where('id', '!=', $admin->id)
            ->update(['status' => 'inactive']);

        // Activate this admin
        $admin->status = 'active';
        $admin->save();

        return redirect()->route('admins.index')
                         ->with('success', 'Admin activated successfully.');
    }

    // Deactivate admin
    public function deactivate(User $admin)
    {
        $admin->status = 'inactive';
        $admin->save();

        return redirect()->route('admins.index')
                         ->with('success', 'Admin deactivated successfully.');
    }
}
