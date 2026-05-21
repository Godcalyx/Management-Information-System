@extends('layouts.faculty')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-success mb-4">Attendance Monitoring</h2>

    {{-- Filter Section --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label fw-bold">Grade Level</label>
            <select id="grade_level_id" class="form-select">
                <option value="">Select Grade Level</option>

                {{-- Dynamically load grade levels --}}
                @foreach ($gradeLevels as $level)
                    <option value="{{ $level->id }}">
                        {{ 'Grade ' . $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Month</label>
            <select id="month" class="form-select">
                @foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">School Year</label>
            <input type="text" id="school_year" value="2025-2026" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Days of School</label>
            <input type="number" id="days_of_school" class="form-control" min="1" required>
        </div>

        <div class="col-md-3 d-flex align-items-end mt-2">
            <button class="btn btn-success w-100" id="loadStudentsBtn">Load Students</button>
        </div>
    </div>

    <div id="attendanceContainer">
        {{-- Students table will appear here --}}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const loadBtn = document.getElementById('loadStudentsBtn');
    const container = document.getElementById('attendanceContainer');

    attachSaveHandler();

    loadBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const grade_level_id = document.getElementById('grade_level_id').value;
        const month = document.getElementById('month').value;
        const school_year = document.getElementById('school_year').value;
        const days_of_school = document.getElementById('days_of_school').value;

        if (!grade_level_id || !month || !school_year || !days_of_school) {
            alert('Please complete all fields.');
            return;
        }

        container.innerHTML = '<p>Loading...</p>';

        axios.get("{{ route('professor.attendance.fetch') }}", {
            params: { grade_level_id, month, school_year, days_of_school }
        })
        .then(response => {
            container.innerHTML = response.data.html;
            attachSaveHandler();
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<div class="alert alert-danger">Failed to load students.</div>';
        });
    });

    function attachSaveHandler() {
        const form = document.getElementById('attendanceForm');
        if (!form) return;

        if (form.dataset.listenerAttached) return;
        form.dataset.listenerAttached = true;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            axios.post("{{ route('professor.attendance.store') }}", formData)
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: res.data.message || 'Attendance saved!',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to save attendance!',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        });
    }
});
</script>
@endsection
