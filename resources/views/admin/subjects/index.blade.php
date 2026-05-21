@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-success">Subjects</h3>
        <button type="button" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="bi bi-plus-circle me-1"></i> Add Subject
        </button>
    </div>

    {{-- Toast Messages --}}
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:1060;">
            @if(session('success'))
                <div class="toast text-bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">{{ session('success') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="toast text-bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">{{ session('error') }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Add Subject Modal --}}
   <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.subjects.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Subject Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="version" class="form-label">Version</label>
                        <input type="text" name="version" id="version" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="grade_levels" class="form-label">Grade Levels</label>
                        <select name="grade_levels[]" id="grade_levels" class="form-select" multiple required>
                            @foreach($grade_levels as $gl)
                                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Subject</button>
                </div>
            </div>
        </form>
    </div>
</div>

    {{-- End Add Modal --}}

    {{-- Subjects List --}}
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-0">

            @if($subjects->count() == 0)
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-folder-x fs-1"></i>
                    <p class="mt-2">No subjects found.</p>
                </div>
            @else
               <table class="table table-hover mb-0 align-middle">
    <thead class="table-success">
        <tr>
            <th style="width: 40%">Subject Name</th>
            <th style="width: 20%">Version</th>
            <th style="width: 30%">Assigned Grade Levels</th>
            <th class="text-end" style="width: 10%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subjects as $sub)
            <tr>
                <td class="fw-semibold">{{ $sub->name }}</td>
                <td>{{ $sub->version }}</td>
                <td class="text-muted">
                    @php
                        // Get all assigned grade levels names via pivot
                        $assigned = $sub->gradeLevels->pluck('name')->toArray();
                        
                        // Always include main grade level
                        if ($sub->gradeLevel) {
                            $assigned[] = $sub->gradeLevel->name;
                        }

                        // Remove duplicates
                        $assigned = array_unique($assigned);
                    @endphp

                    {{ count($assigned) > 0 ? implode(', ', $assigned) : 'None' }}
                </td>
                <td class="text-end">
                    <!-- Edit Button trigger modal -->
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editSubjectModal{{ $sub->id }}">
                        Edit
                    </button>

                    <!-- Delete Form -->
                    <form action="{{ route('admin.subjects.destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subject?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                    </form>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editSubjectModal{{ $sub->id }}" tabindex="-1" aria-labelledby="editSubjectModalLabel{{ $sub->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.subjects.update', $sub->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editSubjectModalLabel{{ $sub->id }}">Edit Subject</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name{{ $sub->id }}" class="form-label">Subject Name</label>
                                            <input type="text" name="name" id="name{{ $sub->id }}" value="{{ $sub->name }}" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="version{{ $sub->id }}" class="form-label">Version</label>
                                            <input type="text" name="version" id="version{{ $sub->id }}" value="{{ $sub->version }}" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="grade_levels{{ $sub->id }}" class="form-label">Grade Levels</label>
                                            <select name="grade_levels[]" id="grade_levels{{ $sub->id }}" class="form-select" multiple>
                                                @foreach($grade_levels as $gl)
                                                    <option value="{{ $gl->id }}" {{ $sub->gradeLevels->contains($gl->id) || ($sub->grade_level_id == $gl->id) ? 'selected' : '' }}>
                                                        {{ $gl->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Edit Modal -->

                </td>
            </tr>
        @endforeach
    </tbody>
</table>

            @endif

        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.forEach((toastEl) => {
        const t = new bootstrap.Toast(toastEl, { delay: 3000 });
        t.show();
    });
});
</script>
@endpush

@endsection
