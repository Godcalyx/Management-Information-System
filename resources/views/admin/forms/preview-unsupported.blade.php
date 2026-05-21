@extends('layouts.admin')

@section('title', 'Preview Not Supported')

@section('content')
<div class="container mt-5">
    <div class="alert alert-warning text-center shadow-sm">
        <h4 class="fw-bold">Preview Not Supported</h4>
        <p>Preview is not available for this file type: <strong>{{ $form->file_type }}</strong>.</p>
        <a href="{{ $downloadUrl }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-download me-1"></i> Click here to download
        </a>
    </div>
</div>
@endsection
