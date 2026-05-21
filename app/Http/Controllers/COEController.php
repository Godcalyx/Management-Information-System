<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCardRequest; // <-- correct model!
use Auth;

class COEController extends Controller
{
    public function request(Request $request)
    {
        ReportCardRequest::create([
            'user_id' => Auth::id(),
            'form_type' => 'coe',   // important!
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'COE Request Submitted!');
    }
}
