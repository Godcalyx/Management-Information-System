<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCardRequest;
use App\Models\Announcement;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormRequestStatusMail;
use Illuminate\Support\Facades\Auth;

class ReportCardController extends Controller
{
    public function request(Request $request)
    {
        $request->validate([
            'form_type' => 'required|string',
        ]);

        ReportCardRequest::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'form_type' => $request->form_type,
        ]);

        return redirect()->back()->with('success', 'Request submitted successfully.');
    }

   public function index(Request $request)
{
    $search = $request->search;

    $requests = ReportCardRequest::with('user')
        ->where('status', 'pending')
        ->when($search, function ($query) use ($search) {
            // Only apply search if input is not numeric
            if (!is_numeric($search)) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('form_type', 'LIKE', "%{$search}%");
            }
        })
        ->latest()
        ->get();

    $unreadAnnouncementCount = $this->getUnreadAnnouncementCount();

    return view('admin.reportcard.index', compact('requests', 'unreadAnnouncementCount'));
}



    public function archive()
    {
        $approved = ReportCardRequest::where('status', 'approved')->latest()->paginate(10);
        $rejected = ReportCardRequest::where('status', 'declined')->latest()->paginate(10);

        $unreadAnnouncementCount = $this->getUnreadAnnouncementCount();

        return view('admin.reportcard.archive', compact('approved', 'rejected', 'unreadAnnouncementCount'));
    }

    public function approve($id)
    {
        $request = ReportCardRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        Mail::to($request->user->email)->send(new FormRequestStatusMail($request));

        return redirect()->back()->with('success', 'Request approved and email sent.');
    }

    public function decline($id)
    {
        $request = ReportCardRequest::findOrFail($id);
        $request->status = 'declined';
        $request->save();

        Mail::to($request->user->email)->send(new FormRequestStatusMail($request));

        return redirect()->back()->with('success', 'Request declined and email sent.');
    }

    /**
     * Get unread announcement count for the authenticated user.
     */
    private function getUnreadAnnouncementCount()
    {
        $user = Auth::user();

        // Only apply if user has a grade_level (student)
        if (!$user || !isset($user->grade_level)) {
            return 0;
        }

        return Announcement::where(function ($query) use ($user) {
                $query->whereJsonContains('target_grades', $user->grade_level)
                      ->orWhereNull('target_grades'); // Null = All
            })
            ->whereDoesntHave('users', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();
    }
}
