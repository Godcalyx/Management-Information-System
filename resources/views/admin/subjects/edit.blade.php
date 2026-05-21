@extends('layouts.admin')
@section('content')
<div class="container mt-4">
    <h3>Edit Subject</h3>
    <form action="{{ route('admin.subjects.update',$subject->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="version" class="form-control" value="{{ $subject->version }}" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $subject->name }}" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <input type="text" name="description" class="form-control" value="{{ $subject->description }}">
        </div>
        <div class="mb-3">
            <label>Assign to Grade Levels</label>
            <select name="grade_levels[]" class="form-select" multiple>
                @foreach($gradeLevels as $gl)
                    <option value="{{ $gl->id }}" {{ in_array($gl->id, $assignedLevels) ? 'selected' : '' }}>{{ $gl->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection
