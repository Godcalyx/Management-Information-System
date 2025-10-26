@extends('layouts.admin')

@section('title', 'Manage Professors')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Manage Professors</h2>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
            <i class="bi bi-person-plus"></i> Add New Professor
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <form method="GET" action="{{ route('admin.professors.index') }}" class="mb-4">
        <div class="input-group">
            <input 
                type="search" 
                name="search" 
                class="form-control" 
                placeholder="Search by name or email..." 
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-outline-success">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>

    <!-- Add Professor Modal -->
    <div class="modal fade" id="addProfessorModal" tabindex="-1" aria-labelledby="addProfessorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.professors.store') }}">
                @csrf
                <div class="modal-content shadow-sm rounded-3">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add New Professor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle"></i> A temporary password will be generated and emailed to the professor.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Professor
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Professors Table -->
    @if($professors->count())
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-striped align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($professors as $professor)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $professor->name }}</td>
                        <td>{{ $professor->email }}</td>
                        <td>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <!-- View -->
                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#viewProfessorModal{{ $professor->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>

                                <!-- Edit -->
                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editProfessorModal{{ $professor->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

                                <!-- Delete -->
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProfessorModal{{ $professor->id }}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- View Modal -->
                    <div class="modal fade" id="viewProfessorModal{{ $professor->id }}" tabindex="-1" aria-labelledby="viewProfessorModalLabel{{ $professor->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-sm rounded-3">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">Professor Details</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Name:</strong> {{ $professor->name }}</p>
                                    <p><strong>Email:</strong> {{ $professor->email }}</p>
                                    <hr>
                                    <h6 class="fw-bold mb-2">Assigned Subjects</h6>
                                    @if(isset($assignments[$professor->id]) && count($assignments[$professor->id]) > 0)
                                        <ul class="mb-3">
                                            @foreach($assignments[$professor->id] as $assignment)
                                                <li>{{ $assignment->subject->name }} (Grade {{ $assignment->grade_level }})</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No subjects assigned.</p>
                                    @endif

                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal{{ $professor->id }}">
                                        <i class="bi bi-journal-plus"></i> Assign Subject
                                    </button>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Modal -->
                    <div class="modal fade" id="assignModal{{ $professor->id }}" tabindex="-1" aria-labelledby="assignModalLabel{{ $professor->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.professors.assign') }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $professor->id }}">
                                <div class="modal-content shadow-sm rounded-3">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Assign Subject & Grade Level</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Grade Level</label>
                                            <select name="grade_level" class="form-select" required>
                                                <option value="" disabled selected>Select grade level</option>
                                                @foreach($subjectsGrouped as $grade => $groupedSubjects)
                                                    <option value="{{ $grade }}">Grade {{ $grade }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Subject</label>
                                            <select name="subject_id" class="form-select" required>
                                                <option value="" disabled selected>Select subject</option>
                                                @foreach($subjectsGrouped as $grade => $groupedSubjects)
                                                    <optgroup label="Grade {{ $grade }}">
                                                        @foreach($groupedSubjects as $subject)
                                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Assign
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editProfessorModal{{ $professor->id }}" tabindex="-1" aria-labelledby="editProfessorModalLabel{{ $professor->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.professors.update', $professor->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content shadow-sm rounded-3">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Professor</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Full Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $professor->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $professor->email }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning text-white">
                                            <i class="bi bi-save"></i> Save Changes
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteProfessorModal{{ $professor->id }}" tabindex="-1" aria-labelledby="deleteProfessorModalLabel{{ $professor->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-sm rounded-3">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirm Deletion</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete <strong>{{ $professor->name }}</strong>? This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('admin.professors.destroy', $professor) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash"></i> Yes, Delete
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $professors->appends(request()->query())->links() }}
        </div>

    @else
        <div class="alert alert-secondary text-center mt-4">
            No professors found.
        </div>
    @endif
</div>
@endsection
