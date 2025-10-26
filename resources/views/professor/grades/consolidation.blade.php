@extends('layouts.faculty')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Encode Grades</h2>
    </div>

    <!-- ✅ Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" id="successAlert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Grade Level & Quarter Selection -->
    <form method="GET" action="{{ route('grades.consolidation') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="grade_level" class="form-label">Select Grade Level</label>
                <select name="grade_level" id="grade_level" class="form-select" onchange="this.form.submit()">
                    <option disabled selected>-- Choose Grade Level --</option>
                    @foreach($gradeLevels as $level)
                        <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                            Grade {{ $level }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="quarter" class="form-label">Select Quarter</label>
                <select name="quarter" id="quarter" class="form-select" onchange="this.form.submit()">
                    <option disabled selected>-- Choose Quarter --</option>
                    @foreach(['1' => '1st Quarter', '2' => '2nd Quarter', '3' => '3rd Quarter', '4' => '4th Quarter'] as $key => $label)
                        <option value="{{ $key }}" {{ request('quarter') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if($subjects->isEmpty())
        <div class="alert alert-warning text-center">No subjects assigned for this grade level.</div>

    @elseif(request('quarter'))
        <!-- Student Search -->
        <div class="mb-3">
            <label for="searchInput" class="form-label">Search Student</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Search student...">
        </div>

        <!-- Grades Table Form -->
        <form action="{{ route('grades.store') }}" method="POST" id="gradesForm">
            @csrf
            <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
            <input type="hidden" name="quarter" value="{{ request('quarter') }}">

            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-striped align-middle text-center mb-0" id="gradesTable" style="font-size: 14px;">
                    <thead class="table-success">
                        <tr>
                            <th>LRN</th>
                            <th class="text-start">Student Name</th>
                            @foreach($subjects as $subject)
                                <th>{{ $subject->name }}</th>
                            @endforeach
                            <th>Average</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->lrn }}</td>
                                <td class="text-start">
                                    {{ $student->last_name }}, {{ $student->first_name }}
                                    @if($student->middle_name) {{ $student->middle_name }} @endif
                                    @if($student->extension_name) {{ $student->extension_name }} @endif
                                </td>
                                @foreach($subjects as $subject)
                                    <td>
                                        <input type="number"
                                               name="grades[{{ $student->id }}][{{ $subject->id }}]"
                                               step="0.01" min="0" max="100"
                                               class="form-control grade-input text-center"
                                               value="{{ old("grades.{$student->id}.{$subject->id}") }}" />
                                    </td>
                                @endforeach
                                <td class="average-cell fw-bold">—</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Submit Button triggers modal -->
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                    <i class="bi bi-upload me-1"></i> Submit Grades
                </button>
            </div>
        </form>

        <!-- ✅ Confirmation Modal -->
        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="confirmSubmitModalLabel"><i class="bi bi-exclamation-circle me-2"></i>Confirm Submission</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to submit these grades?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Yes, Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Search Filter
                const searchInput = document.getElementById('searchInput');
                searchInput.addEventListener('keyup', () => {
                    const filter = searchInput.value.toLowerCase();
                    document.querySelectorAll('#gradesTable tbody tr').forEach(row => {
                        const lrn = row.cells[0].innerText.toLowerCase();
                        const studentName = row.cells[1].innerText.toLowerCase();
                        row.style.display = lrn.includes(filter) || studentName.includes(filter) ? '' : 'none';
                    });
                });

                // Grade color-coding & average
                function updateVisuals(row) {
                    const inputs = row.querySelectorAll('input[type="number"]');
                    let sum = 0, count = 0;

                    inputs.forEach(input => {
                        const value = parseFloat(input.value);
                        const parent = input.parentElement;
                        parent.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'text-white', 'text-dark');

                        if (!isNaN(value)) {
                            sum += value;
                            count++;

                            if (value >= 90) parent.classList.add('bg-success', 'text-white');
                            else if (value >= 75) parent.classList.add('bg-warning', 'text-dark');
                            else parent.classList.add('bg-danger', 'text-white');

                            input.setCustomValidity((value < 0 || value > 100) ? 'Grade must be between 0 and 100' : '');
                        }
                    });

                    const avgCell = row.querySelector('.average-cell');
                    avgCell.innerText = count > 0 ? ((sum / count).toFixed(2) % 1 === 0 ? parseInt(sum / count) : (sum / count).toFixed(2)) : '—';
                }

                document.querySelectorAll('#gradesTable tbody tr').forEach(row => {
                    row.querySelectorAll('input[type="number"]').forEach(input => {
                        input.addEventListener('input', () => updateVisuals(row));
                    });
                    updateVisuals(row);
                });

                // Confirm modal submission
                const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
                confirmSubmitBtn.addEventListener('click', () => {
                    document.getElementById('gradesForm').submit();
                });

                // Auto-hide success alert
                const successAlert = document.getElementById('successAlert');
                if(successAlert){
                    setTimeout(() => {
                        const alert = new bootstrap.Alert(successAlert);
                        alert.close();
                    }, 3000); // 3 seconds
                }
            });
        </script>
    @endif
</div>
@endsection
