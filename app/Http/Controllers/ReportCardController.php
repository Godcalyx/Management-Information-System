<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCardRequest;
use App\Models\FormRequest;

class ReportCardController extends Controller
{
    public function request(Request $request)
{
    $request->validate([
        'form_type' => 'required|string', // validate the field
    ]);

    FormRequest::create([
        'user_id' => auth()->id(),
        'status' => 'pending',
        'form_type' => $request->form_type, // ✅ include this
    ]);

    return redirect()->back()->with('success', 'Request submitted successfully.');
}

    public function index()
{
    // Show only pending requests in main page
    $requests = ReportCardRequest::where('status', 'pending')->latest()->get();

    return view('admin.reportcard.index', compact('requests'));
}

public function archive()
{
    $approved = ReportCardRequest::where('status', 'approved')->latest()->paginate(10);
    $rejected = ReportCardRequest::where('status', 'declined')->latest()->paginate(10);

    return view('admin.reportcard.archive', compact('approved', 'rejected'));
}

public function approve($id)
{
    $request = ReportCardRequest::findOrFail($id);
    $request->update(['status' => 'approved']);

    return redirect()->route('admin.reportcard.index')->with('success', 'Request approved successfully!');
}

public function decline($id)
{
    $request = ReportCardRequest::findOrFail($id);
    $request->update(['status' => 'declined']);

    return redirect()->route('admin.reportcard.index')->with('success', 'Request declined successfully!');
}

    
}
