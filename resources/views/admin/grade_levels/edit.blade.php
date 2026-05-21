@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark py-3">
            <h4 class="mb-0 fw-bold">✏️ Edit Grade Level</h4>
        </div>

        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the errors below.</strong>
                </div>
            @endif

            <form action="{{ route('admin.grade-levels.update', $gradeLevel->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Grade Level Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $gradeLevel->name) }}" required>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" name="description" class="form-control"
                        value="{{ old('description', $gradeLevel->description) }}">
                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Order</label>
                    <input type="number" name="order" class="form-control"
                        value="{{ old('order', $gradeLevel->order) }}" required>
                    @error('order')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-warning text-dark px-4">Update</button>
                    <a href="{{ route('admin.grade-levels.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
