<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.audit-trails.index');
    }
}
