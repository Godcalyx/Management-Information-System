@extends('layouts.admin')

@section('title', 'Forms Management')

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Manage Forms</h2>
        <a href="{{ route('admin.forms.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Upload New Form
        </a>
    </div>

    {{-- STATS CARDS --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-success border-start-4 animate__animated animate__fadeInUp">
                <div class="card-body text-center">
                    <h5 class="fw-bold">Total Forms</h5>
                    <h3 class="text-success">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-primary border-start-4 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body text-center">
                    <h5 class="fw-bold">Categories</h5>
                    <h3 class="text-primary">{{ $stats['categories'] }}</h3>
                </div>
            </div>
        </div>

        {{-- <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-info border-start-4 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body text-center">
                    <h5 class="fw-bold">Public</h5>
                    <h3 class="text-info">{{ $stats['public'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-warning border-start-4 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-body text-center">
                    <h5 class="fw-bold">Restricted</h5>
                    <h3 class="text-warning">{{ $stats['restricted'] }}</h3>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- FILTERS + SEARCH --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.forms.index') }}" class="row g-3 align-items-center">

                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search code or description..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}"
                                {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- <div class="col-md-3">
                    <select name="visibility" class="form-select">
                        <option value="">All Visibility</option>
                        <option value="public" {{ request('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                        <option value="restricted" {{ request('visibility') == 'restricted' ? 'selected' : '' }}>Restricted</option>
                    </select>
                </div> --}}

                <div class="col-md-2">
                    <button class="btn btn-success w-100">Filter</button>
                </div>

            </form>
        </div>
    </div>

    {{-- FORMS TABLE --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white fw-bold">
            Uploaded Forms
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Category</th>
                        {{-- <th>Visibility</th> --}}
                        <th>File</th>
                        <th>Size</th>
                        <th>Uploaded</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($forms as $form)

                    @php
                        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
                        $isPreviewable = in_array(strtolower($form->file_type), $previewableTypes);
                    @endphp

                    <tr class="table-row-hover">
                        <td>{{ $form->code }}</td>
                        <td>{{ $form->description }}</td>
                        <td>{{ $form->category }}</td>
                        {{-- <td>
                            <span class="badge {{ $form->visibility === 'public' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($form->visibility) }}
                            </span>
                        </td> --}}
                        <td>
                            <span class="badge bg-secondary">
                                {{ strtoupper($form->file_type) }}
                            </span>
                        </td>
                        <td>{{ number_format($form->file_size / 1024, 2) }} KB</td>
                        <td>{{ $form->created_at->format('M d, Y') }}</td>

                        <td class="text-center">

    @php
        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
        $isPreviewable = in_array(strtolower($form->file_type), $previewableTypes);
    @endphp

    {{-- PREVIEW OR DOWNLOAD --}}
    @if ($isPreviewable)
        <a href="{{ route('admin.forms.preview', $form->id) }}"
           target="_blank"
           class="btn btn-sm btn-info">
            Preview
        </a>
    @else
        <a href="{{ route('admin.forms.download', $form->id) }}"
           class="btn btn-sm btn-success">
            Download
        </a>
    @endif

    {{-- Edit --}}
    <a href="{{ route('admin.forms.edit', $form->id) }}"
       class="btn btn-sm btn-warning">
        Edit
    </a>

    {{-- Delete --}}
    <button type="button"
            class="btn btn-sm btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal{{ $form->id }}">
        Delete
    </button>


                            {{-- DELETE MODAL --}}
                            <div class="modal fade" id="deleteModal{{ $form->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete <strong>{{ $form->description }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin.forms.destroy', $form->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            No forms uploaded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $forms->links() }}
    </div>

</div>

@push('styles')
<style>
    .table-row-hover:hover {
        background-color: #e6f4ea;
        transition: background-color 0.1s ease;
    }
    .border-start-4 {
        border-left-width: 4px !important;
    }
</style>
@endpush

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
