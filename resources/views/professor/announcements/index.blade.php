@extends('layouts.faculty')

@section('title', 'Announcements')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Manage Announcements</h2>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Create New Announcement -->
    <div class="card shadow-sm rounded-3 mb-5">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="bi bi-megaphone-fill me-1"></i> Post a New Announcement
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.announcements.index') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input 
                        type="text" 
                        name="title" 
                        class="form-control shadow-sm" 
                        placeholder="Enter announcement title..." 
                        required 
                        maxlength="255"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Content</label>
                    <textarea 
                        name="content" 
                        class="form-control shadow-sm" 
                        rows="4" 
                        placeholder="Write your announcement details..." 
                        required
                    ></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Attach Image or Document (optional)</label>
                    <input 
                        type="file" 
                        name="attachment" 
                        class="form-control shadow-sm" 
                        accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                    >
                    <small class="text-muted">Allowed formats: JPG, PNG, PDF, DOCX</small>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-send-fill me-1"></i> Post Announcement
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Announcements -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">📄 All Announcements</h4>
    </div>

    @if($announcements->count())
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-striped align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Posted At</th>
                        <th>Attachment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $announcement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $announcement->title }}</td>
                            <td>{{ Str::limit($announcement->content, 70) }}</td>
                            <td>{{ $announcement->created_at->format('M d, Y • h:i A') }}</td>
                            <td>
                                @if($announcement->attachment)
                                    <a href="{{ $announcement->attachment_url }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                       <i class="bi bi-paperclip"></i> View / Download
                                    </a>
                                @else
                                    <em class="text-muted">No attachment</em>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this announcement?')"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash3-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-secondary text-center mt-4">
            No announcements posted yet.
        </div>
    @endif

</div>
@endsection
