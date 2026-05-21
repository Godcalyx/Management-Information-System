@extends('layouts.admin') 

@section('title', 'Class Records')

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Class Records</h2>
        <div>
           <a href="{{ route('excel.exportAll') }}" class="btn btn-sm btn-success">
   Export All
</a>

        </div>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('classrecord.index') }}" class="mb-3 d-flex gap-2">
    <select name="grade_level" class="form-select" style="width:auto;">
        <option value="">-- All Grades --</option>
        @foreach(App\Models\GradeLevel::all() as $grade)
            <option value="{{ $grade->id }}" {{ $selectedGrade == $grade->id ? 'selected' : '' }}>
                {{ $grade->name }}
            </option>
        @endforeach
    </select>
<button type="submit" class="btn btn-primary">Filter</button>
    <input type="text" name="search" class="form-control" placeholder="Search by name or LRN" 
           value="{{ $searchTerm }}">

    
</form>

<table class="table table-bordered table-striped">
    <thead>
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
        @forelse($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->lrn }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->grade_level }}</td>
                <td>{{ $student->email }}</td>
                <td>
                    <a href="{{ route('excel.exportSingle', $student->id) }}" class="btn btn-sm btn-success">
                        Export
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No students found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

            </div>
        </div>
    </div>

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
    
document.addEventListener('DOMContentLoaded', () => {
    // Auto-submit search after typing
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let typingTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => searchInput.form.submit(), 400);
        });
    }

});



</script>
@endsection
