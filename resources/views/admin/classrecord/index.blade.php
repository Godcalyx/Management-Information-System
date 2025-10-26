@extends('layouts.admin') 

@section('title', 'Class Records')

@section('content')
<div class="container mt-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="fw-bold mb-3 mb-md-0">Class Records</h2>
        <div>
            <a href="{{ route('excel.exportAll') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-arrow-down"></i> Export All
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('classrecord.index') }}" class="row g-2 align-items-center mb-4">
        <div class="col-md-3">
            <select name="grade_level" class="form-select" onchange="this.form.submit()">
                <option value="">All Grade Levels</option>
                @foreach([7,8,9,10] as $level)
                    <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                        Grade {{ $level }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <input type="search" name="search" class="form-control" placeholder="Search by name or LRN..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>

    {{-- Students Table --}}
    <div class="card shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>#</th>
                            <th>LRN</th>
                            <th>Name</th>
                            <th>Grade Level</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->lrn }}</td>
                                <td class="text-start">{{ $student->name }}</td>
                                <td>
                                    <span class="badge 
                                        @if($student->grade_level == 7) bg-success
                                        @elseif($student->grade_level == 8) bg-warning text-dark
                                        @elseif($student->grade_level == 9) bg-danger
                                        @elseif($student->grade_level == 10) bg-primary
                                        @else bg-secondary
                                        @endif">
                                        Grade {{ $student->grade_level }}
                                    </span>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#exportModal" 
                                        data-id="{{ $student->id }}" 
                                        data-name="{{ $student->name }}">
                                        <i class="bi bi-file-earmark-arrow-down"></i> Export
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No students found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    {{-- @if($students->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $students->appends(request()->query())->links() }}
        </div>
    @endif --}}

</div>

{{-- Reusable AJAX Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exportModalLabel">Export Report Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Do you want to export the report card for <strong id="studentName"></strong>?</p>
                <div id="spinner" class="d-none mt-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Exporting...</p>
                </div>
                <div id="successMsg" class="alert alert-success d-none mt-3">
                    Export successful! The download should start automatically.
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button id="exportBtn" class="btn btn-success">
                    <i class="bi bi-file-earmark-arrow-down"></i> Export
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const exportModal = document.getElementById('exportModal')
    const exportBtn = document.getElementById('exportBtn')
    const spinner = document.getElementById('spinner')
    const successMsg = document.getElementById('successMsg')
    const studentNameEl = document.getElementById('studentName')

    let exportUrl = ''

    exportModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const studentId = button.getAttribute('data-id')
        const studentName = button.getAttribute('data-name')

        studentNameEl.textContent = studentName
        exportUrl = `/excel/exportSingle/${studentId}`

        spinner.classList.add('d-none')
        successMsg.classList.add('d-none')
        exportBtn.disabled = false
    })

    exportBtn.addEventListener('click', () => {
        spinner.classList.remove('d-none')
        exportBtn.disabled = true

        fetch(exportUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            if (!response.ok) throw new Error('Network response was not ok')
            const blob = await response.blob()
            
            // Download file
            const url = window.URL.createObjectURL(blob)
            const a = document.createElement('a')
            a.href = url

            // Extract filename from headers if provided
            const disposition = response.headers.get('content-disposition')
            let filename = 'report-card.xlsx'
            if (disposition && disposition.indexOf('filename=') !== -1) {
                filename = disposition.split('filename=')[1].replace(/"/g, '')
            }
            a.download = filename
            document.body.appendChild(a)
            a.click()
            a.remove()

            spinner.classList.add('d-none')
            successMsg.classList.remove('d-none')
        })
        .catch(err => {
            console.error(err)
            spinner.classList.add('d-none')
            alert('Export failed. Please try again.')
            exportBtn.disabled = false
        })
    })
</script>
@endsection
