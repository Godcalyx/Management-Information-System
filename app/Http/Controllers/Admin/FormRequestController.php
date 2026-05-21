<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormRequest;
use App\Models\Announcement;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormRequestStatusMail;
use Illuminate\Support\Facades\Auth;

class FormRequestController extends Controller
{
    public function index()
    {
        $requests = FormRequest::with('user')->latest()->get();

        // Count unread announcements for admin (if applicable)
        $user = Auth::user();
        $unreadAnnouncementCount = Announcement::where(function($query) use ($user) {
                $query->whereJsonContains('target_grades', $user->grade_level ?? null)
                      ->orWhereNull('target_grades');
            })
            ->whereDoesntHave('users', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();

        return view('admin.form_requests', compact('requests', 'unreadAnnouncementCount'));
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
