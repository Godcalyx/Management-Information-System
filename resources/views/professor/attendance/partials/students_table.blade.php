@if($students->count())
<form id="attendanceForm">
    @csrf

    {{-- Hidden inputs --}}
    <input type="hidden" name="grade_level_id" value="{{ $request->grade_level_id }}">
    <input type="hidden" name="month" value="{{ $request->month }}">
    <input type="hidden" name="school_year" value="{{ $request->school_year }}">
    <input type="hidden" name="days_of_school" value="{{ $daysOfSchool }}">

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-success text-center">
                <tr>
                    <th>LRN</th>
                    <th>Student Name</th>
                    <th>Days of School</th>
                    <th>Days Present</th>
                    <th>Times Tardy</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    @php
                        $enrollment = $student->enrollments->first();
                        $rec = $records[$enrollment->id] ?? null;
                    @endphp

                    <tr>
                        <td class="text-center">{{ $student->lrn ?? 'N/A' }}</td>
                        <td>{{ $student->name }}</td>

                        <td>
                            <input type="number" class="form-control text-center"
                                   value="{{ $daysOfSchool }}" readonly>
                        </td>

                        <td>
                            <input type="number"
                                   min="0"
                                   max="{{ $daysOfSchool }}"
                                   name="records[{{ $student->id }}][days_present]"
                                   value="{{ $rec->days_present ?? '' }}"
                                   class="form-control text-center"
                                   required>
                        </td>

                        <td>
                            <input type="number"
                                   min="0"
                                   name="records[{{ $student->id }}][times_tardy]"
                                   value="{{ $rec->times_tardy ?? '' }}"
                                   class="form-control text-center"
                                   required>
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary px-4">Save Attendance</button>
    </div>
</form>

@else
<div class="alert alert-warning">
    No students found for this grade level ({{ $request->month }}, {{ $request->school_year }}).
</div>
@endif
