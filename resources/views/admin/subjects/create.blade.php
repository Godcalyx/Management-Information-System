@extends('layouts.admin')
@section('content')
<div class="container mt-4">
    <h3>Add Subject</h3>
    <form action="{{ route('admin.subjects.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <input type="text" name="description" class="form-control">
        </div>
        <div class="mb-3">
            <label>Assign to Grade Levels</label>
            <select name="grade_levels[]" class="form-select" multiple>
                @foreach($gradeLevels as $gl)
                    <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Save</button>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection
