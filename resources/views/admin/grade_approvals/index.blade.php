@extends('layouts.admin')
@section('title', 'Pending Grades for Approval')

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Pending Grades for Approval</h2>
        <form method="POST" action="{{ route('admin.grades.approve.all') }}">
            @csrf
            <button type="submit" class="btn btn-success shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Approve All Pending
            </button>
        </form>
    </div>

    {{-- Grade Level Filter --}}
    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="grade_level" class="form-select" onchange="this.form.submit()">
                    <option value="">All Grade Levels</option>
                    @foreach($gradeLevels as $level)
                        <option value="{{ $level->id }}" @selected($selectedLevel == $level->id)>
                            Grade {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- Alerts --}}
    @foreach (['success', 'info'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg }} shadow-sm">{{ session($msg) }}</div>
        @endif
    @endforeach

    @if($students->isEmpty())
        <div class="alert alert-info shadow-sm">No students with submitted grades found.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 text-center">
                    <thead class="table-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>LRN</th>
                            <th class="text-start">Student</th>
                            <th>Grade Level</th>
                            <th>School Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $enrollment)
                            @php
                                $pendingGrades = $enrollment->grades->where('status','submitted');
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $enrollment->student->lrn ?? 'N/A' }}</td>
                                <td class="text-start fw-semibold text-truncate" style="max-width:250px;">
                                    {{ $enrollment->student->name }}
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        Grade {{ $enrollment->gradeLevel->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $enrollment->school_year ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm shadow-sm"
                                        onclick="viewGrades({{ $enrollment->student->id }})">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- View Grades Modal --}}
<div class="modal fade" id="viewGradesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewGradesTitle">Submitted Grades</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewGradesBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading grades...</p>
                </div>
            </div>
            <div class="modal-footer justify-content-between" id="viewGradesFooter" style="display:none;">
                <button class="btn btn-warning shadow-sm" id="returnButton">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Return Selected Grades
                </button>
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Return Grades Modal --}}
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="returnForm">
            @csrf
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Return Grades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Provide a reason for returning the selected grades. This helps the teacher correct them.
                    </p>
                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-semibold">Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" id="remarks" class="form-control" required></textarea>
                        <div class="invalid-feedback">Please provide a reason for returning the grades.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning shadow-sm" id="returnSubmitBtn">Return</button>
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
let returnFormListenerAdded = false;

function viewGrades(studentId) {
    const modalEl = document.getElementById('viewGradesModal');
    const modal = new bootstrap.Modal(modalEl);
    const title = document.getElementById('viewGradesTitle');
    const body = document.getElementById('viewGradesBody');
    const footer = document.getElementById('viewGradesFooter');
    const returnButton = document.getElementById('returnButton');

    modal.show();
    body.innerHTML = `<div class="text-center py-3">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2">Loading grades...</p>
    </div>`;
    footer.style.display = 'none';

    fetch(`/admin/grades/student/${studentId}`)
        .then(res => res.json())
        .then(data => {
            title.textContent = `${data.student_name} — Submitted Grades`;

            if (!data.grades.length) {
                body.innerHTML = `<div class="alert alert-info">No submitted grades found.</div>`;
                return;
            }

            const subjects = {};
            data.grades.forEach(g => {
                if (!subjects[g.subject]) subjects[g.subject] = {};
                subjects[g.subject][g.quarter] = g.grade;
            });

            let table = `<div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-dark text-white text-center">
                    <tr>
                        <th rowspan="2">Subject</th>
                        <th colspan="4">Quarterly Grades</th>
                        <th rowspan="2">Final Rating</th>
                    </tr>
                    <tr><th>1</th><th>2</th><th>3</th><th>4</th></tr>
                </thead><tbody>`;

            let totalFinal = 0, subjectCount = 0;
            for (const [subject, quarters] of Object.entries(subjects)) {
                let qGrades = [1,2,3,4].map(q => quarters[q] ?? '');
                const validGrades = qGrades.filter(v => v !== '' && !isNaN(v));
                const final = validGrades.length ? (validGrades.reduce((a,b)=>a+parseFloat(b),0)/validGrades.length).toFixed(2) : '';
                if(final !== '') { totalFinal += parseFloat(final); subjectCount++; }

                table += `<tr class="text-center">
                    <td class="text-start">${subject}</td>
                    ${qGrades.map((g, idx) => `<td class="${g!=='' && g<75 ? 'text-danger fw-bold':''}">
                        ${g}
                        <input type="checkbox" class="form-check-input ms-1 return-grade-checkbox"
                            data-subject="${subject}" data-quarter="${idx+1}" />
                    </td>`).join('')}
                    <td class="${final!=='' && final<75 ? 'text-danger fw-bold':''}"><strong>${final}</strong></td>
                </tr>`;
            }

            const genAve = subjectCount ? (totalFinal/subjectCount).toFixed(2) : 'N/A';
            table += `<tr class="fw-bold text-center table-secondary">
                        <td colspan="5" class="text-end">General Average</td>
                        <td>${genAve}</td>
                      </tr></tbody></table></div>`;

            body.innerHTML = table;

            // Setup Return button
            returnButton.onclick = () => showReturnModal(studentId);
            footer.style.display = 'flex';
        })
        .catch(() => {
            body.innerHTML = `<div class="alert alert-danger">Failed to load grades. Please try again.</div>`;
        });

    if(!returnFormListenerAdded){
        document.getElementById('returnForm').addEventListener('submit', function(event){
            const remarks = document.getElementById('remarks').value.trim();
            if(!remarks){
                event.preventDefault();
                document.getElementById('remarks').classList.add('is-invalid');
                return;
            } else {
                document.getElementById('remarks').classList.remove('is-invalid');
            }

            const checkedGrades = Array.from(document.querySelectorAll('.return-grade-checkbox:checked'))
                .map(cb => ({
                    subject: cb.dataset.subject,
                    quarter: cb.dataset.quarter
                }));

            if(checkedGrades.length === 0){
                event.preventDefault();
                alert("Please select at least one grade to return.");
                return;
            }

            // Add hidden inputs dynamically
            checkedGrades.forEach((g,i)=>{
                const subjInput = document.createElement('input');
                subjInput.type = 'hidden';
                subjInput.name = `grades[${i}][subject]`;
                subjInput.value = g.subject;
                this.appendChild(subjInput);

                const qInput = document.createElement('input');
                qInput.type = 'hidden';
                qInput.name = `grades[${i}][quarter]`;
                qInput.value = g.quarter;
                this.appendChild(qInput);
            });
        });
        returnFormListenerAdded = true;
    }

    document.getElementById('returnModal').addEventListener('show.bs.modal', () => {
        document.getElementById('remarks').classList.remove('is-invalid');
        document.getElementById('remarks').value = '';
    });
}

function showReturnModal(studentId){
    const form = document.getElementById('returnForm');
    form.action = `/admin/grades/return/student/${studentId}`;
    new bootstrap.Modal(document.getElementById('returnModal')).show();
}
</script>
@endsection
@endsection
