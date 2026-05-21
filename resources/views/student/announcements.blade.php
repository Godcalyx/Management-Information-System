@extends('layouts.app')

@section('title', 'Announcements')
@section('header', 'Announcements')

@section('content')
<div class="container mt-5">

    <!-- Sort Filter -->
    <form method="GET" action="{{ route('announcements.index') }}" class="mb-4 d-flex align-items-center gap-2">
        <label for="sort" class="fw-semibold me-2">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()" class="form-select w-auto">
            <option value="desc" {{ $sort == 'desc' ? 'selected' : '' }}>Latest</option>
            <option value="asc" {{ $sort == 'asc' ? 'selected' : '' }}>Oldest</option>
        </select>
    </form>

    @if($announcements->count())
        @foreach($announcements as $index => $announcement)
            <div 
                class="card mb-3 shadow-sm announcement-card {{ $index >= 3 ? 'd-none' : '' }}" 
                data-id="{{ $announcement->id }}"
                data-bs-toggle="modal" 
                data-bs-target="#announcementModal" 
                data-title="{{ $announcement->title }}"
                data-date="{{ $announcement->created_at->format('F j, Y • h:i A') }}"
                data-content="{{ e($announcement->content) }}"
                data-grades='@json($announcement->target_grades)'
                data-attachment="{{ $announcement->attachment_url ?? '' }}"
                data-postedby="{{ $announcement->user->name ?? 'System' }}"
                style="cursor: pointer;"
            >
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $announcement->title }}</strong>
                        @if($announcement->users->where('id', auth()->id())->isEmpty())
                            <span class="badge bg-warning text-dark ms-2">New</span>
                        @endif
                    </div>
                    <span class="text-light small">
                        {{ $announcement->created_at->format('F j, Y • h:i A') }}
                    </span>
                </div>

                <div class="card-body">
                    <p>{{ Str::limit($announcement->content, 30) }}</p>

                    @if($announcement->target_grades)
                        @foreach(json_decode($announcement->target_grades) as $grade)
                            <span class="badge bg-primary">{{ $grade }}</span>
                        @endforeach
                    @else
                        <span class="badge bg-secondary">All</span>
                    @endif

                    @if ($announcement->attachment_url)
                        <div class="mt-2 mb-2">
                            <a href="{{ $announcement->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                <i class="bi bi-paperclip"></i> View Attachment
                            </a>
                        </div>
                    @endif

                    <p class="text-muted small mb-0">Posted by: {{ $announcement->user->name ?? 'System' }}</p>
                </div>
            </div>
        @endforeach

        @if($announcements->count() > 3)
            <div class="text-center mt-3">
                <button id="seeMoreBtn" class="btn btn-outline-success">See More</button>
            </div>
        @endif
    @else
        <div class="alert alert-secondary text-center mt-4">
            No announcements available.
        </div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="announcementModalLabel"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-1" id="announcementDate"></p>
        <p id="announcementContent" class="mt-3"></p>
        <div id="announcementGrades" class="mb-3"></div>

        <!-- Attachment Preview -->
        <div id="attachmentContainer" class="my-3 d-none">
            <p class="fw-semibold mb-2">Attachment Preview:</p>
            <div id="attachmentPreviewArea" class="border rounded p-2 bg-light"></div>
        </div>

        <!-- Fallback link -->
        <a id="announcementAttachment" href="#" target="_blank" class="btn btn-sm btn-outline-dark d-none">
          <i class="bi bi-paperclip"></i> Open Attachment
        </a>

        <p class="text-muted mt-3 small" id="announcementPostedBy"></p>
      </div>
    </div>
  </div>
</div>

<!-- Script -->
<script>
    const announcementModal = document.getElementById('announcementModal');
    announcementModal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;
        const announcementId = card.getAttribute('data-id'); // Add this to your cards

    // Mark as read via AJAX
    fetch(`/student/announcements/${announcementId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    }).then(() => {
        card.querySelector('.badge.bg-warning')?.remove(); // Remove "New" badge
        // Optionally update sidebar count
        const sidebarBadge = document.getElementById('announcementCount');
        if (sidebarBadge) {
            let count = parseInt(sidebarBadge.textContent) - 1;
            sidebarBadge.textContent = count > 0 ? count : '';
        }
    });

        const title = card.getAttribute('data-title');
        const date = card.getAttribute('data-date');
        const content = card.getAttribute('data-content') || '';
        const grades = JSON.parse(card.getAttribute('data-grades') || '[]');
        const attachment = card.getAttribute('data-attachment');
        const postedBy = card.getAttribute('data-postedby');

        this.querySelector('#announcementModalLabel').textContent = title;
        this.querySelector('#announcementDate').textContent = date;
        this.querySelector('#announcementPostedBy').textContent = 'Posted by: ' + postedBy;

        // Content with line breaks + fallback
        const contentContainer = this.querySelector('#announcementContent');
        contentContainer.innerHTML = content.trim()
            ? content.replace(/\n/g, '<br>')
            : '<em class="text-muted">No content provided.</em>';

        // Grades
        const gradesContainer = this.querySelector('#announcementGrades');
        gradesContainer.innerHTML = '';
        if (grades.length) {
            grades.forEach(g => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary me-1';
                badge.textContent = g;
                gradesContainer.appendChild(badge);
            });
        } else {
            gradesContainer.innerHTML = '<span class="badge bg-secondary">All</span>';
        }

        // Attachment handling
        const attachmentContainer = this.querySelector('#attachmentContainer');
        const attachmentPreviewArea = this.querySelector('#attachmentPreviewArea');
        const attachmentLink = this.querySelector('#announcementAttachment');

        if (attachment) {
            attachmentContainer.classList.remove('d-none');
            attachmentLink.classList.remove('d-none');
            attachmentLink.href = attachment;

            const ext = attachment.split('.').pop().toLowerCase();
            let previewHTML = '';

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                previewHTML = `<img src="${attachment}" alt="Attachment Image" class="img-fluid rounded shadow-sm" loading="lazy">`;
            } else if (ext === 'pdf') {
                previewHTML = `<iframe src="${attachment}" width="100%" height="400px" class="border-0 rounded" loading="lazy"></iframe>`;
            } else {
                previewHTML = `<a href="${attachment}" target="_blank" class="btn btn-outline-dark btn-sm">
                                  <i class="bi bi-file-earmark"></i> Open File
                               </a>`;
            }

            attachmentPreviewArea.innerHTML = previewHTML;
        } else {
            attachmentContainer.classList.add('d-none');
            attachmentLink.classList.add('d-none');
            attachmentPreviewArea.innerHTML = '';
        }
    });

    // See More Button
    document.getElementById('seeMoreBtn')?.addEventListener('click', function () {
        document.querySelectorAll('.announcement-card.d-none').forEach(card => {
            card.classList.remove('d-none');
        });
        this.style.display = 'none';
    });
</script>

<style>
    .announcement-card:hover {
        background-color: #ffffa5c9;
        transition: background-color 0.3s;
    }
</style>
@endsection
