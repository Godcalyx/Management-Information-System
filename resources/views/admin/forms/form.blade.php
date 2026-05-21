<form action="{{ isset($form) ? route('admin.forms.update', $form->id) : route('admin.forms.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($form))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="code">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $form->code ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="description">Description</label>
        <input type="text" name="description" class="form-control" value="{{ old('description', $form->description ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="file">File</label>
        <input type="file" name="file" class="form-control" {{ isset($form) ? '' : 'required' }}>
        @if(isset($form))
            <small>Current file: {{ basename($form->file_path) }}</small>
        @endif
    </div>

    <button class="btn btn-primary">{{ isset($form) ? 'Update' : 'Upload' }}</button>
</form>
