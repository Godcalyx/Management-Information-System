<div class="modal-header">
    <h5 class="modal-title">Edit Student</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <form id="updateStudentForm">

        <input type="hidden" name="id" value="{{ $enrollment->id }}">

        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="{{ $student->first_name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="{{ $student->middle_name }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="{{ $student->last_name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">LRN</label>
            <input type="text" name="lrn" class="form-control" value="{{ $student->lrn }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <select name="grade_level" class="form-control" required>
                <option value="7" {{ $enrollment->grade_level == 7 ? 'selected' : '' }}>Grade 7</option>
                <option value="8" {{ $enrollment->grade_level == 8 ? 'selected' : '' }}>Grade 8</option>
                <option value="9" {{ $enrollment->grade_level == 9 ? 'selected' : '' }}>Grade 9</option>
                <option value="10" {{ $enrollment->grade_level == 10 ? 'selected' : '' }}>Grade 10</option>
            </select>
        </div>

    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary" id="saveStudentBtn">Save Changes</button>
</div>
