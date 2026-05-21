@extends('layouts.admin')

@section('title', 'Grade Levels')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Grade Levels</h2>
        <!-- Button trigger modal for adding new grade level -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addGradeLevelModal">
            Add Grade Level
        </button>
    </div>

    <!-- Grade Levels Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Grade Level</th>
                <th>Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grade_levels as $index => $grade_level)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $grade_level->name }}</td>
                <td>{{ $grade_level->order }}</td>
                <td>
                    <!-- Edit Button trigger modal -->
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editGradeLevelModal{{ $grade_level->id }}">
                        Edit
                    </button>

                    <!-- Delete Form -->
                    <form action="{{ route('admin.grade-levels.destroy', $grade_level->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this grade level?')">Delete</button>
                    </form>
                </td>
            </tr>

            <!-- Edit Grade Level Modal -->
            <div class="modal fade" id="editGradeLevelModal{{ $grade_level->id }}" tabindex="-1" aria-labelledby="editGradeLevelModalLabel{{ $grade_level->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.grade-levels.update', $grade_level->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editGradeLevelModalLabel{{ $grade_level->id }}">Edit Grade Level</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name{{ $grade_level->id }}" class="form-label">Grade Level Name</label>
                                    <input type="text" class="form-control" id="name{{ $grade_level->id }}" name="name" value="{{ $grade_level->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="order{{ $grade_level->id }}" class="form-label">Order</label>
                                    <input type="number" class="form-control" id="order{{ $grade_level->id }}" name="order" value="{{ $grade_level->order }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Grade Level</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Grade Level Modal -->
<div class="modal fade" id="addGradeLevelModal" tabindex="-1" aria-labelledby="addGradeLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.grade-levels.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGradeLevelModalLabel">Add Grade Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Grade Level Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Order</label>
                        <input type="number" class="form-control" id="order" name="order" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Grade Level</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
