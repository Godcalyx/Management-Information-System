@extends('layouts.admin')

@section('content')
<main class="content-wrapper">
    <div class="container mt-5">
        <h2 class="mb-4 fw-bold">Promotion Management – School Year {{ $schoolYear }}</h2>

        {{-- Evaluation Summary --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Evaluation Summary</h5>
                <p>Total Students: <strong>{{ $totalStudents }}</strong></p>
                <p>Evaluated Students: <strong>{{ $evaluatedCount ?? 0 }}</strong></p>
                <p>Pending Evaluation: <strong>{{ $pendingCount ?? 0 }}</strong></p>

                {{-- Always show Evaluate button --}}
                <button id="evaluateBtn" class="btn btn-primary">
                    <span id="evaluateText">Evaluate Promotion</span>
                    <span id="loader" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>

                @if(($pendingCount ?? 0) == 0)
                    <p class="text-success mt-2">
                        All students have been evaluated — you can re-run evaluation if needed.
                    </p>
                @endif
            </div>
        </div>

        {{-- Students for Manual Review --}}
        <div id="reviewSection" class="card shadow-sm {{ $studentsForReview->count() ? '' : 'd-none' }}">
            <div class="card-body">
                <h5 class="card-title mb-3">Students for Manual Review</h5>

                <form id="reviewForm" method="POST" action="{{ route('admin.promotion.bulkUpdateStatus') }}">
                    @csrf
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>LRN</th>
                                <th>Name</th>
                                <th>Grade Level</th>
                                <th>Promotion Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="reviewTableBody">
                            @forelse($studentsForReview as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->lrn ?? '-' }}</td>
                                    <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name ?? '' }}</td>
                                    <td>{{ $student->grade_level }}</td>
                                    <td>{{ ucfirst($student->promotion_status) }}</td>
                                    <td>
                                        <select name="statuses[{{ $index }}][promotion_status]" class="form-select">
                                            <option value="promoted">Promote</option>
                                            <option value="retained">Retain</option>
                                        </select>
                                        <input type="hidden" name="statuses[{{ $index }}][id]" value="{{ $student->id }}">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No students require manual review.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($studentsForReview->count() > 0)
                        <button type="submit" class="btn btn-success mt-3">Save Promotion Decisions</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('styles')
<style>
.content-wrapper {
    margin-left: 250px; /* adjust based on sidebar */
    padding: 20px;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const evaluateBtn = document.getElementById('evaluateBtn');
    const loader = document.getElementById('loader');
    const evaluateText = document.getElementById('evaluateText');
    const reviewSection = document.getElementById('reviewSection');
    const reviewTableBody = document.getElementById('reviewTableBody');

    evaluateBtn?.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to run the promotion evaluation?')) return;

        evaluateBtn.disabled = true;
        loader.classList.remove('d-none');
        evaluateText.textContent = "Evaluating, please wait...";

        try {
            const response = await fetch("{{ route('admin.promotion.evaluate', ['schoolYear' => $schoolYear]) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            loader.classList.add('d-none');
            evaluateText.textContent = "Evaluate Promotion";
            evaluateBtn.disabled = false;

            if (data.students_for_review.length > 0) {
                reviewSection.classList.remove('d-none');
                reviewTableBody.innerHTML = '';

                data.students_for_review.forEach((student, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${student.lrn ?? '-'}</td>
                        <td>${student.last_name}, ${student.first_name} ${student.middle_name ?? ''}</td>
                        <td>${student.grade_level}</td>
                        <td>${student.promotion_status}</td>
                        <td>
                            <select name="statuses[${index}][promotion_status]" class="form-select">
                                <option value="promoted">Promote</option>
                                <option value="retained">Retain</option>
                            </select>
                            <input type="hidden" name="statuses[${index}][id]" value="${student.id}">
                        </td>
                    `;
                    reviewTableBody.appendChild(row);
                });
            } else {
                reviewSection.classList.add('d-none');
                alert('All students met promotion criteria. No manual review needed.');
            }
        } catch (error) {
            console.error(error);
            loader.classList.add('d-none');
            evaluateBtn.disabled = false;
            evaluateText.textContent = "Evaluate Promotion";
            alert('An error occurred while evaluating promotions.');
        }
    });
});
</script>
@endsection
