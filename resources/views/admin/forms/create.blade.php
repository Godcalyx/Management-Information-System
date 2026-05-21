@extends('layouts.admin')

@section('title', 'Add New Form')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Add New Form</h2>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.forms.store') }}" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4">
        @csrf

        {{-- Code --}}
        <div class="mb-3">
            <label for="code" class="form-label">Form Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <input type="text" name="category" id="category" class="form-control" value="{{ old('category') }}" placeholder="e.g., Enrollment, Requirements" required>
        </div>

        {{-- Visibility
        <div class="mb-3">
            <label for="visibility" class="form-label">Visibility <span class="text-danger">*</span></label>
            <select name="visibility" id="visibility" class="form-select" required>
                <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                <option value="restricted" {{ old('visibility') == 'restricted' ? 'selected' : '' }}>Admin Only</option>
            </select>
        </div> --}}

        {{-- File Upload --}}
        <div class="mb-3">
            <label for="file" class="form-label">Upload File <span class="text-danger">*</span></label>
            <input type="file" name="file" id="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif" required>
            <small class="text-muted">Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF</small>
        </div>

        {{-- File Preview --}}
        <div id="previewContainer" class="mb-3 d-none">
            <label class="form-label">File Preview:</label>
            <div id="filePreview" class="border p-2">
                <p class="text-muted">Preview available for PDF and images only.</p>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-upload"></i> Upload Form
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('file').addEventListener('change', function(e) {
    const file = this.files[0];
    const previewContainer = document.getElementById('previewContainer');
    const filePreview = document.getElementById('filePreview');

    if (!file) {
        previewContainer.classList.add('d-none');
        filePreview.innerHTML = '';
        return;
    }

    const fileType = file.name.split('.').pop().toLowerCase();

    if (fileType === 'pdf') {
        const url = URL.createObjectURL(file);
        filePreview.innerHTML = `<iframe src="${url}" style="width:100%; height:300px;" frameborder="0"></iframe>`;
    } else if (['jpg','jpeg','png','gif'].includes(fileType)) {
        const url = URL.createObjectURL(file);
        filePreview.innerHTML = `<img src="${url}" class="img-fluid" style="max-height:300px;">`;
    } else {
        filePreview.innerHTML = `<p class="text-muted">Preview not available for this file type.</p>`;
    }

    previewContainer.classList.remove('d-none');
});
</script>
@endsection
