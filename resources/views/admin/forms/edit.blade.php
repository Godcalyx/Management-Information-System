@extends('layouts.admin')

@section('title', 'Edit Form')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Edit Form: {{ $form->code }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.forms.update', $form->id) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4">
        @csrf
        @method('PUT')

        {{-- Code --}}
        <div class="mb-3">
            <label for="code" class="form-label">Form Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $form->code) }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description', $form->description) }}</textarea>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <input type="text" name="category" id="category" class="form-control" value="{{ old('category', $form->category) }}" required>
        </div>

        {{-- Visibility
        <div class="mb-3">
            <label for="visibility" class="form-label">Visibility <span class="text-danger">*</span></label>
            <select name="visibility" id="visibility" class="form-select" required>
                <option value="public" {{ old('visibility', $form->visibility) == 'public' ? 'selected' : '' }}>Public</option>
                <option value="restricted" {{ old('visibility', $form->visibility) == 'restricted' ? 'selected' : '' }}>Admin Only</option>
            </select>
        </div> --}}

        {{-- Current File Preview --}}
        @php $fileUrl = asset('storage/' . $form->file_path); @endphp
        <div class="mb-3">
            <label class="form-label">Current File Preview:</label>
            <div id="currentFilePreview" class="border p-2 mb-2">
                @if($form->file_type === 'pdf')
                    <iframe src="{{ $fileUrl }}" style="width:100%; height:600px;" frameborder="4"></iframe>
                @elseif(in_array($form->file_type, ['jpg','jpeg','png','gif']))
                    <img src="{{ $fileUrl }}" class="img-fluid" style="max-height:300px;">
                @else
                    <p class="text-muted">Preview not available for this file type.</p>
                @endif
            </div>
        </div>

        {{-- File Replacement --}}
        <div class="mb-3">
            <label for="file" class="form-label">Replace File</label>
            <input type="file" name="file" id="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
            <small class="text-muted">Optional. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF</small>
        </div>

        {{-- Buttons --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update Form
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('file').addEventListener('change', function(e) {
    const file = this.files[0];
    const previewContainer = document.getElementById('currentFilePreview');

    if (!file) return;

    const fileType = file.name.split('.').pop().toLowerCase();

    if (fileType === 'pdf') {
        const url = URL.createObjectURL(file);
        previewContainer.innerHTML = `<iframe src="${url}" style="width:100%; height:300px;" frameborder="0"></iframe>`;
    } else if (['jpg','jpeg','png','gif'].includes(fileType)) {
        const url = URL.createObjectURL(file);
        previewContainer.innerHTML = `<img src="${url}" class="img-fluid" style="max-height:300px;">`;
    } else {
        previewContainer.innerHTML = `<p class="text-muted">Preview not available for this file type.</p>`;
    }
});
</script>
@endsection
