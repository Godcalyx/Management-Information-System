@extends('layouts.admin')

@section('title', 'Students List')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Manage Students</h2>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('admin.students.index') }}" class="row gy-2 gx-2 align-items-center mb-4">
        <div class="col-md-3">
            <select name="grade_level_id" class="form-select" onchange="this.form.submit()">
    <option value="">All Grade Levels</option>
    @foreach($gradeLevels as $level)
        <option value="{{ $level->id }}" {{ request('grade_level_id') == $level->id ? 'selected' : '' }}>
            Grade {{ $level->name }}
        </option>
    @endforeach
</select>


        </div>
        <div class="col-md-4">
            <input type="search" name="search" class="form-control"
                placeholder="Search by name and LRN..."
                value="{{ request('search') }}">
        </div>
    </form>

    <!-- Success/Error Messages -->
    @foreach (['success', 'error'] as $msg)
        @if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '{{ session('success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '{{ session('error') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
</script>
@endif

    @endforeach

    <!-- Promote All Button -->
    <div class="mb-3 d-flex align-items-center flex-wrap gap-2">
        <label for="bulk_grade_level" class="me-2">Promote all students in:</label>
        <select name="grade_level_id" id="bulk_grade_level" class="form-select w-auto">
    <option value="">Select grade level</option>
    @foreach($gradeLevels->where('id','<',10) as $level)
        <option value="{{ $level->id }}">Grade {{ $level->name }}</option>
    @endforeach
</select>

        <button type="button" class="btn btn-success btn-sm" id="promote-all-btn">Promote All</button>
    </div>

    <!-- Modal for students who did not meet criteria -->
    <div class="modal fade" id="promotionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Students Not Meeting Promotion Criteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="promotion-modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    @if($students->count())
    <div class="shadow-sm rounded-3 mt-4">
        <table class="table table-striped align-middle text-center mb-0">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>LRN</th>
                    <th>Grade Level</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    {{-- Note: $student is an Enrollment (per your controller). We store needed fields in data- attributes for easy client-side population --}}
                    <tr
    data-enroll-id="{{ $student->id }}"
    data-id="{{ $student->user_id }}"
    data-first_name="{{ $student->user->first_name }}"
    data-middle_name="{{ $student->user->middle_name }}"
    data-last_name="{{ $student->user->last_name }}"
    data-email="{{ $student->user->email }}"
    data-lrn="{{ $student->user->lrn }}"
    data-grade_level_id="{{ $student->grade_level_id }}"
>
    <td>{{ $loop->iteration }}</td>
    <td class="text-start">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
    <td>{{ $student->user->email }}</td>
    <td>{{ $student->user->lrn }}</td>
    <td>
        <span>
            Grade {{ $student->gradeLevel?->name ?? 'N/A' }}
        </span>
    </td>
    <td class="position-relative text-center">
        <!-- Kebab button -->
        <button class="btn btn-sm bg-transparent p-1 kebab-btn">
            <i class="bi bi-three-dots-vertical fs-5"></i>
        </button>
        <div class="kebab-menu bg-white border rounded shadow-sm py-2">
            <a class="dropdown-item export-form137" href="{{ route('admin.form137.export', $student->user_id) }}" target="_blank">
                <i class="bi bi-file-earmark-text me-2"></i> Export Form 137
            </a>
            <button class="dropdown-item edit-student" data-user="{{ $student->user_id }}">
                <i class="bi bi-pencil-square me-2"></i> Edit
            </button>
            <button class="dropdown-item delete-student" data-user="{{ $student->user_id }}">
                <i class="bi bi-trash me-2"></i> Delete
            </button>
            @if($student->grade_level_id == 10 && $student->promotion_status != 'completed')
    <button class="dropdown-item promote-single" data-enroll="{{ $student->id }}">
        <i class="bi bi-arrow-up-circle me-2"></i> Complete
    </button>
@endif

        </div>
    </td>
</tr>

                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @else
        <div class="alert alert-secondary text-center mt-4">No students found.</div>
    @endif

</div>

<!-- Kebab CSS -->
<style>
.table-responsive {
    overflow-x: hidden; /* prevent scroll */
}

.kebab-menu {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 160px;
    max-width: 220px; /* prevent extra-wide menus */
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 0.25rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
    padding: 0.25rem 0;
    z-index: 1050;
    transform: translateX(-10px);
    opacity: 0;
    pointer-events: none;
    transition: transform 0.15s ease, opacity 0.15s ease;
    overflow: hidden;
}
.kebab-menu.show {
    transform: translateX(0);
    opacity: 1;
    pointer-events: auto;
}
.kebab-menu .dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: flex-start;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    white-space: nowrap;
}
.kebab-menu .dropdown-item:hover {
    background-color: #e1e1e1ff;
}

body.dark-mode .kebab-menu {
    background: #111827;
    border-color: #334155;
    box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.35);
}

body.dark-mode .kebab-menu .dropdown-item {
    color: #e5e7eb;
}

body.dark-mode .kebab-menu .dropdown-item:hover {
    background-color: #1f2937;
    color: #f8fafc;
}

/* Offcanvas width for slide-in panels */
.offcanvas-end.custom-width {
    width: 420px;
    max-width: 95%;
}
</style>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // Auto-submit search (with small debounce)
    const searchInput = document.querySelector('input[name="search"]');
    if(searchInput){
        let typingTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(()=> searchInput.form.submit(), 400);
        });
    }

    // Bulk promote logic
    const promoteBtn = document.getElementById('promote-all-btn');
    if(promoteBtn){
        const gradeSelect = document.getElementById('bulk_grade_level');
        promoteBtn.addEventListener('click', () => {
            const gradeId = gradeSelect.value;
            if(!gradeId) return Swal.fire('Error','Please select a grade level to promote.','error');

            const studentRows = document.querySelectorAll('tbody tr');
            const exists = Array.from(studentRows).some(row => parseInt(row.dataset.grade_level_id) === parseInt(gradeId));
            if(!exists){
                return Swal.fire('Error', `No students found in this grade for promotion.`, 'error');
            }

            Swal.fire({
                title:'Are you sure?',
                text:`This will evaluate and promote all students in the selected grade`,
                icon:'question',
                showCancelButton:true,
                confirmButtonText:'Yes, Promote',
                cancelButtonText:'Cancel'
            }).then(result=>{
                if(!result.isConfirmed) return;

                fetch("{{ route('admin.students.promote.bulk') }}",{
                    method:'POST',
                    headers:{
                        'X-CSRF-TOKEN':"{{ csrf_token() }}",
                        'Content-Type':'application/json',
                        'Accept':'application/json'
                    },
                    body: JSON.stringify({ grade_level_id: gradeId })
                })
                .then(res=>res.json())
                .then(data=>{
                    if(data.failed && data.failed.length>0){
                        let html='<ul class="list-group">';
                        data.failed.forEach(s=>{
                            html+=`<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <div class="me-3"><strong>${s.name}</strong><br><small>GPA: ${s.gpa}, MSR Avg: ${s.msr_avg}</small></div>
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm review-grades" data-user="${s.user_id}" data-enroll="${s.enrollment_id}">Review</button>
                                    <button class="btn btn-success btn-sm promote-single" data-enroll="${s.enrollment_id}">Promote</button>
                                </div>
                            </li>`;
                        });
                        html+='</ul>';
                        document.getElementById('promotion-modal-body').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('promotionModal')).show();
                    } else if(data.message){
                        Swal.fire('Success',data.message,'success').then(()=> location.reload());
                    }
                })
                .catch(err=> Swal.fire('Error','Something went wrong. Check console.','error'));
            });
        });
    }

    // Kebab menu logic
    document.addEventListener('click', function(e){
        const kebabBtn = e.target.closest('.kebab-btn');
        document.querySelectorAll('.kebab-menu.show-body').forEach(menu => menu.remove());

        if(kebabBtn){
            const menu = kebabBtn.parentElement.querySelector('.kebab-menu');
            const clone = menu.cloneNode(true);
            clone.classList.add('show','show-body');
            document.body.appendChild(clone);

            const rect = kebabBtn.getBoundingClientRect();
            clone.style.position = 'absolute';
            clone.style.top = `${rect.top + window.scrollY}px`;

            let leftPos = rect.right - clone.offsetWidth;
            if (leftPos + clone.offsetWidth > window.innerWidth - 10) leftPos = rect.left - clone.offsetWidth;
            if (leftPos < 10) leftPos = 10;
            clone.style.left = `${leftPos + window.scrollX}px`;

            const closeOnScroll = () => { clone.remove(); window.removeEventListener('scroll', closeOnScroll); };
            window.addEventListener('scroll', closeOnScroll, { once: true });
        } else {
            if(!e.target.closest('.kebab-menu')) document.querySelectorAll('.kebab-menu.show-body').forEach(menu => menu.remove());
        }
    });

    // Review Grades modal with school year
    document.body.addEventListener('click', function(e){
        const reviewBtn = e.target.closest('.review-grades');
        if(!reviewBtn) return;
        const userId = reviewBtn.dataset.user;
        const enrollmentId = reviewBtn.dataset.enroll;
        const modalId = `reviewGradesModal-${userId}`;

        const existingModal = document.getElementById(modalId);
        if(existingModal) existingModal.remove();

        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Loading...</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Fetching grades...</p>
                        </div>
                    </div>
                </div>
            </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalEl = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        fetch(`/admin/students/view-grades/${enrollmentId}`)
        .then(res => res.json())
        .then(data => {
            let subjects = {};
            data.grades.forEach(g => { 
                if(!subjects[g.subject]) subjects[g.subject]={}; 
                subjects[g.subject][g.quarter]=g.grade; 
            });

            let table = `<table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-white text-center">
                    <tr><th rowspan="2">Subject</th><th colspan="4">Quarter Grades</th><th rowspan="2">Final Rating</th></tr>
                    <tr><th>1</th><th>2</th><th>3</th><th>4</th></tr>
                </thead><tbody>`;
            
            let total=0,count=0;
            for(const [subject,q] of Object.entries(subjects)){
                let q1=q[1]??'', q2=q[2]??'', q3=q[3]??'', q4=q[4]??'';
                let grades=[q1,q2,q3,q4].filter(v=>v!='');
                let final = grades.length ? (grades.reduce((a,b)=>a+parseFloat(b),0)/grades.length).toFixed(2) : '';
                if(final!=''){ total+=parseFloat(final); count++; }

                const highlight=g=> (g!=''&&parseFloat(g)<75)?`<span class="text-danger fw-bold">${g}</span>`:g;
                const highlightFinal = final!=='' && parseFloat(final)<75? `<span class="text-danger fw-bold">${final}</span>`:final;

                table += `<tr>
                    <td class="text-start">${subject}</td>
                    <td class="text-center">${highlight(q1)}</td>
                    <td class="text-center">${highlight(q2)}</td>
                    <td class="text-center">${highlight(q3)}</td>
                    <td class="text-center">${highlight(q4)}</td>
                    <td class="text-center">${highlightFinal}</td>
                </tr>`;
            }
            const genAve = count? (total/count).toFixed(2):'N/A';
            table += `<tr class="fw-bold text-center table-secondary"><td colspan="5" class="text-end">General Average</td><td>${genAve}</td></tr></tbody></table>`;
            
            modalEl.querySelector('.modal-title').textContent = `${data.student_name} — Grades (${data.school_year})`;
            modalEl.querySelector('.modal-body').innerHTML = table;
        })
        .catch(()=>{modalEl.querySelector('.modal-body').innerHTML='<div class="alert alert-danger">Error loading grades.</div>';});
    });

    // Promote single student
document.body.addEventListener('click', function(e){
    const promoteSingle = e.target.closest('.promote-single');
    if(!promoteSingle) return;

    const enrollmentId = promoteSingle.dataset.enroll;
    const row = promoteSingle.closest('tr');

    fetch(`/admin/students/promote-approved/${enrollmentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            Swal.fire('Success', data.success || data.message, 'success')
                .then(()=> location.reload());
        } else if(data.error && !data.error.includes('Incomplete grades')){
            // Show only unexpected errors
            Swal.fire('Error', data.error, 'error');
        } else {
            // If error is "Incomplete grades", add to modal instead of alert
            const modalBody = document.getElementById('promotion-modal-body');
            if(modalBody){
                let html = modalBody.innerHTML;
                html += `<div class="alert alert-warning mb-2">${data.error}</div>`;
                modalBody.innerHTML = html;
                new bootstrap.Modal(document.getElementById('promotionModal')).show();
            }
        }
    })
    .catch(()=> Swal.fire('Error', 'Something went wrong.', 'error'));
});


    // Delete student instantly
    document.body.addEventListener('click', function(e){
        const deleteBtn = e.target.closest('.delete-student');
        if(!deleteBtn) return;

        const userId = deleteBtn.dataset.user;
        const row = deleteBtn.closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the student!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if(!result.isConfirmed) return;

            fetch(`/admin/students/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    Swal.fire('Deleted!', data.message, 'success');
                    row.remove();
                } else {
                    Swal.fire('Error', data.error || 'Could not delete', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Something went wrong.', 'error'));
        });
    });

    document.body.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.edit-student');
    if(!editBtn) return;

    const userId = editBtn.dataset.user;
    const modalId = `editStudentModal-${userId}`;

    // Remove existing modal if any
    document.getElementById(modalId)?.remove();

    // Insert modal skeleton
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit Student</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Loading student data...</p>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modalEl = document.getElementById(modalId);
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Fetch student data
    fetch(`/admin/students/${userId}/edit`)
        .then(res => res.json())
        .then(data => {
            const gradeOptions = data.all_grade_levels.map(g =>
                `<option value="${g.id}" ${g.id == data.grade_level_id ? 'selected' : ''}>${g.name}</option>`
            ).join('');

            const formHtml = `
                <form id="editStudentForm-${userId}">
                    <div class="mb-3 text-start">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="${data.first_name}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="${data.middle_name}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="${data.last_name}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="${data.email}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">LRN</label>
                        <input type="text" name="lrn" class="form-control" value="${data.lrn}">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level_id" class="form-select">
                            ${gradeOptions}
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            `;
            modalEl.querySelector('.modal-body').innerHTML = formHtml;

            // Submit form via AJAX
           document.getElementById(`editStudentForm-${userId}`).addEventListener('submit', function(e){
    e.preventDefault();
    const formData = Object.fromEntries(new FormData(this).entries());

    fetch(`/admin/students/${userId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(resp => {
        if(resp.success){
            Swal.fire('Success', resp.message, 'success');
            bootstrap.Modal.getInstance(modalEl).hide();

            // Update table row immediately
            const row = document.querySelector(`tr[data-id="${userId}"]`);
            if(row){
                // Update name
                row.querySelector('td:nth-child(2)').textContent = 
                    [resp.enrollment.first_name, resp.enrollment.middle_name, resp.enrollment.last_name]
                        .filter(Boolean).join(' ');

                // Update email & LRN
                row.querySelector('td:nth-child(3)').textContent = formData.email;
                row.querySelector('td:nth-child(4)').textContent = formData.lrn;

                // Update grade level with "Grade X" prefix
                row.querySelector('td:nth-child(5) span').textContent = 'Grade ' + resp.enrollment.grade_level_name;

                // Update data-grade_level_id attribute for future bulk actions
                row.dataset.grade_level_id = formData.grade_level_id;
            }
        } else {
            Swal.fire('Error', resp.error || 'Could not update student', 'error');
        }
    })
    .catch(()=> Swal.fire('Error', 'Something went wrong.', 'error'));
});

        })
        .catch(()=> modalEl.querySelector('.modal-body').innerHTML = '<div class="alert alert-danger">Failed to load student data.</div>');
});



// Promote single student (Grade 10) or modal promotion
document.body.addEventListener('click', function(e){
    const promoteBtn = e.target.closest('.promote-single');
    if(!promoteBtn) return;

    const enrollmentId = promoteBtn.dataset.enroll;
    const row = promoteBtn.closest('tr');
    const gradeLevel_id = row ? parseInt(row.dataset.grade_level_id) : null;

    // Only show special confirmation for Grade 10
    const showGrade10Confirm = gradeLevel_id === 10;

    const confirmAction = () => {
        fetch(`/admin/students/promote-approved/${enrollmentId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                Swal.fire('Success', data.message, 'success').then(()=> location.reload());
            } else {
                Swal.fire('Error', data.error || 'Something went wrong', 'error');
            }
        })
        .catch(()=> Swal.fire('Error', 'Something went wrong.', 'error'));
    };

    if(showGrade10Confirm){
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will mark the student as completers.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, complete',
            cancelButtonText: 'Cancel'
        }).then(result=>{
            if(result.isConfirmed){
                confirmAction();
            }
        });
    } else {
        // For lower grades, promote directly without extra alert
        confirmAction();
    }
});


});
</script>
@endsection

