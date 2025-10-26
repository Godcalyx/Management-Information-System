<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormRequestStatusMail;

class FormRequestController extends Controller
{
    public function index()
    {
        $requests = FormRequest::with('user')->latest()->get();
        return view('admin.form_requests', compact('requests'));
    }

    public function update(Request $request, $id)
    {
        $formRequest = FormRequest::findOrFail($id);
        $formRequest->update(['status' => $request->status]);

        try {
            // Send email immediately
            Mail::to($formRequest->user->email)->send(new FormRequestStatusMail($formRequest));
        } catch (\Exception $e) {
            // Log error and show feedback
            \Log::error('Failed to send email: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Request updated, but failed to send email.');
        }

        return redirect()->back()->with('success', 'Request status updated and student notified!');
    }
}
