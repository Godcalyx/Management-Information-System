@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
<div class="container mt-5">

    <!-- Header & Stats -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Manage Announcements</h2>

       <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">

            {{-- <span class="fw-semibold text-muted small">
                <i class="bi bi-info-circle-fill me-1"></i> Legends
            </span> --}}

            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                <i class="bi bi-megaphone-fill me-1"></i>
                Total Announcements:
                <strong>{{ $announcements->total() }}</strong>
            </span>

            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                <i class="bi bi-paperclip me-1"></i>
                With Attachments:
                <strong>{{ $announcements->whereNotNull('attachment')->count() }}</strong>
            </span>

            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                <i class="bi bi-people-fill me-1"></i>
                Visible to All Grades:
                <strong>{{ $announcements->whereNull('target_grades')->count() }}</strong>
            </span>

        </div>
    </div>
</div>

    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Post New Announcement -->
    <div class="card shadow-sm rounded-3 mb-5">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="bi bi-megaphone-fill me-1"></i> Post a New Announcement
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control shadow-sm" placeholder="Enter announcement title..." required maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Content</label>
                    <textarea name="content" class="form-control shadow-sm" rows="4" placeholder="Write your announcement details..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Target Grades</label>
                    <select name="target_grades[]" class="form-select shadow-sm" multiple>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                    </select>
                    <small class="text-muted">Leave empty to make announcement visible to all grades</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Attach Image/Document (optional)</label>
                    <input type="file" name="attachment" class="form-control shadow-sm" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                    <small class="text-muted">Allowed formats: JPG, PNG, PDF, DOCX</small>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-send-fill me-1"></i> Post Announcement</button>
            </form>
        </div>
    </div>

    <!-- Existing Announcements Table -->
    @if($announcements->count())
    <div class="table-responsive shadow-sm rounded-3">
        <table class="table table-hover align-middle text-center mb-0 small">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Target Grades</th>
                    <th>Posted At</th>
                    <th>Attachment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($announcements as $announcement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold text-start" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#announcementModal{{ $announcement->id }}">{{ $announcement->title }}</td>
                    <td>{{ Str::limit($announcement->content, 40) }}</td>
                    <td>
                        @if($announcement->target_grades)
                            @foreach(json_decode($announcement->target_grades) as $grade)
                                <span class="badge bg-primary">{{ $grade }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary">All</span>
                        @endif
                    </td>
                    <td>{{ $announcement->created_at->format('M d, Y • h:i A') }}</td>
                    <td>
                        @if($announcement->attachment)
                            <a href="{{ $announcement->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-paperclip"></i>
                            </a>
                        @else
                            <em class="text-muted">No attachment</em>
                        @endif
                    </td>
                   <td class="d-flex justify-content-center gap-1">

    <!-- Edit Button -->
    <button class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#editAnnouncementModal{{ $announcement->id }}">
        <i class="bi bi-pencil-square"></i>
    </button>

    <!-- Delete -->
    <form action="{{ route('admin.announcements.destroy', $announcement->id) }}"
          method="POST"
          onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger">
            <i class="bi bi-trash3-fill"></i>
        </button>
    </form>

</td>

                </tr>

                <!-- Modal for Full Content -->
                <div class="modal fade" id="announcementModal{{ $announcement->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">{{ $announcement->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="white-space: pre-wrap;">
                                {{ $announcement->content }}
                                @if($announcement->attachment)
                                    <hr>
                                    <a href="{{ $announcement->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-paperclip"></i> View Attachment
                                    </a>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editAnnouncementModal{{ $announcement->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <form method="POST"
              action="{{ route('admin.announcements.update', $announcement->id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-1"></i> Edit Announcement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ $announcement->title }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Content</label>
                        <textarea name="content"
                                  class="form-control"
                                  rows="4"
                                  required>{{ $announcement->content }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Grades</label>
                        @php
                            $targets = $announcement->target_grades
                                ? json_decode($announcement->target_grades)
                                : [];
                        @endphp
                        <select name="target_grades[]" class="form-select" multiple>
                            @foreach(['Grade 7','Grade 8','Grade 9','Grade 10'] as $grade)
                                <option value="{{ $grade }}"
                                    {{ in_array($grade, $targets) ? 'selected' : '' }}>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Replace Attachment (optional)</label>
                        <input type="file"
                               name="attachment"
                               class="form-control"
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">

                        @if($announcement->attachment)
                            <small class="text-muted d-block mt-1">
                                Current:
                                <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Announcement Image" class="img-fluid">
                                <a href="{{ $announcement->attachment_url }}" target="_blank">View Attachment</a>
                            </small>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $announcements->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @else
    <div class="alert alert-secondary text-center mt-4">No announcements posted yet.</div>
    @endif

</div>
@endsection
