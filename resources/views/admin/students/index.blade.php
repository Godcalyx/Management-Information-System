@extends('layouts.admin')

@section('title', 'Manage Students')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="fw-bold mb-3 mb-md-0">Manage Students</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="bi bi-person-plus"></i> Add New Student
        </button>
    </div>

    <!-- Search and Filter -->
    <form method="GET" action="{{ route('admin.students.index') }}" class="row gy-2 gx-2 align-items-center mb-4">
        <div class="col-md-5">
            <input 
                type="search" 
                name="search" 
                class="form-control" 
                placeholder="Search by name or LRN..." 
                value="{{ request('search') }}"
            >
        </div>
        <div class="col-md-3">
            <select name="grade_level" class="form-select" onchange="this.form.submit()">
                <option value="">All Grade Levels</option>
                @for ($i = 7; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ request('grade_level') == $i ? 'selected' : '' }}>
                        Grade {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </form>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.students.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-semibold" id="addStudentModalLabel">
                            <i class="bi bi-person-plus"></i> Add New Student
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">LRN</label>
                            <input type="text" class="form-control" name="lrn" value="{{ old('lrn') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grade Level</label>
                            <select name="grade_level" class="form-select" required>
                                @for ($i = 7; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('grade_level') == $i ? 'selected' : '' }}>Grade {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="alert alert-info">
                            A temporary password will be generated and emailed to the student.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Student</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    @if ($students->count())
        <div class="table-responsive shadow-sm rounded-3 mt-4">
            <table class="table table-striped align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>LRN</th>
                        <th>Grade Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
                            <td>{{ $student->lrn }}</td>
                            <td>
                                <span class="badge 
                                    @if($student->grade_level == 7) bg-success
                                    @elseif($student->grade_level == 8) bg-warning text-dark
                                    @elseif($student->grade_level == 9) bg-danger
                                    @elseif($student->grade_level == 10) bg-primary
                                    @else bg-secondary
                                    @endif">
                                    Grade {{ $student->grade_level }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Edit -->
                                    <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <!-- Delete (opens modal) -->
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal{{ $student->id }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-labelledby="editStudentModalLabel{{ $student->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('admin.students.update', $student) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title fw-semibold">Edit Student</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Middle Name</label>
                                                        <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">LRN</label>
                                                        <input type="text" class="form-control" name="lrn" value="{{ old('lrn', $student->lrn) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Grade Level</label>
                                                        <select name="grade_level" class="form-select" required>
                                                            @for ($i = 7; $i <= 10; $i++)
                                                                <option value="{{ $i }}" {{ $student->grade_level == $i ? 'selected' : '' }}>Grade {{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-warning text-white">Save Changes</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteStudentModal{{ $student->id }}" tabindex="-1" aria-labelledby="deleteStudentModalLabel{{ $student->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-semibold">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Confirm Delete
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p>Are you sure you want to delete <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>?</p>
                                                <p class="text-muted small mb-0">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger px-4">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $students->appends(request()->query())->links() }}
        </div>
    @else
        <div class="alert alert-secondary text-center mt-4">
            No students found.
        </div>
    @endif
</div>
@endsection
