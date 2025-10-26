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
            <div class="card mb-3 shadow-sm announcement-card {{ $index >= 3 ? 'd-none' : '' }}">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <strong>{{ $announcement->title }}</strong>
                    <span class="text-light small">
                        {{ $announcement->created_at->format('F j, Y • h:i A') }}
                    </span>
                </div>

                <div class="card-body">
                    <p>{{ $announcement->content }}</p>

                    @if ($announcement->attachment)
                        <a href="{{ $announcement->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-dark mb-2">
                            <i class="bi bi-paperclip"></i> View Attachment
                        </a>
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

<!-- See More Script -->
<script>
    document.getElementById('seeMoreBtn')?.addEventListener('click', function () {
        document.querySelectorAll('.announcement-card.d-none').forEach(card => {
            card.classList.remove('d-none');
        });
        this.style.display = 'none';
    });
</script>
@endsection
