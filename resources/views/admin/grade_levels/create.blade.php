@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0 fw-bold">➕ Add Grade Level</h4>
        </div>

        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>There were some issues with your input:</strong>
                </div>
            @endif

            <form action="{{ route('admin.grade-levels.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Grade Level Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" 
                        placeholder="e.g., Grade 11" value="{{ old('name') }}" required>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" name="description" class="form-control" 
                        placeholder="Optional short description"
                        value="{{ old('description') }}">
                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Order</label>
                    <input type="number" name="order" class="form-control"
                        placeholder="Optional: 1 = first"
                        value="{{ old('order') }}">
                    <small class="text-muted">Leave blank to add this grade level last.</small>
                    @error('order')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-success px-4">Create</button>
                    <a href="{{ route('admin.grade-levels.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
