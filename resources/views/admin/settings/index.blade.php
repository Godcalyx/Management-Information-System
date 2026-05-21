@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-1">System Settings</h2>
            <p class="text-muted mb-0">Manage and customize your system behavior.</p>
        </div>
    </div>

    {{-- SETTINGS CARD --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white fw-bold">Settings</div>

        <div class="card-body">
            <div class="row">

                {{-- LEFT SIDEBAR --}}
                <div class="col-md-3 border-end">
                    <div class="nav flex-column nav-pills gap-1" id="settingsTabs" role="tablist" aria-orientation="vertical">
                        {{-- SYSTEM --}}
                        <small class="text-gray-50 px-3"><i class="bi bi-gear-fill me-1"></i> System</small>
                        <button class="nav-link active text-start" data-bs-toggle="pill" data-bs-target="#general" type="button">
                            <i class="bi bi-gear me-2"></i> General
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#backup" type="button">
                            <i class="bi bi-database me-2"></i> Backup & Export
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#security" type="button">
                            <i class="bi bi-shield-lock me-2"></i> Security
                        </button>

                        {{-- ACADEMIC --}}
                        <small class="text-gray-50 px-3 mt-3"><i class="bi bi-book-fill me-1"></i> Academic</small>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#academicGradeLevels" type="button">
                            <i class="bi bi-journal-bookmark me-2"></i> Grade Levels
                        </button>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#academicSubjects" type="button">
                            <i class="bi bi-journal-bookmark me-2"></i> Subjects
                        </button>

                        {{-- ACCOUNT --}}
                        <small class="text-gray-50 px-3 mt-3"><i class="bi bi-person-fill me-1"></i> Account</small>
                        <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#changePassword" type="button">
                            <i class="bi bi-key me-2"></i> Change Password
                        </button>
                    </div>
                </div>

                {{-- RIGHT CONTENT --}}
                <div class="col-md-9">
                    <div class="tab-content ps-md-4">

                        {{-- GENERAL --}}
                        <div class="tab-pane fade show active" id="general">
                            <h5 class="fw-bold">General Settings</h5>
                            <p class="text-muted">Basic system preferences that help personalize the platform.</p>

                            <form action="{{ route('admin.settings.updateGeneral') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="fw-semibold">School Name</label>
                                    <input type="text" class="form-control" name="school_name"
                                           value="{{ setting('school_name', 'Lingayen Science High School') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">School Logo</label>
                                    <input type="file" name="school_logo" class="form-control" accept="image/*" id="schoolLogoInput">
                                    <small class="text-muted">JPG, PNG or SVG (max 2MB)</small>
                                </div>

                                <div class="mb-3">
                                    <strong>Current Logo:</strong><br>
                                    @if(setting('school_logo'))
                                        <img src="{{ Storage::url(setting('school_logo')) }}" class="img-thumbnail mt-2" style="max-height:150px;">
                                    @else
                                        <em>No logo uploaded yet</em>
                                        <img id="schoolLogoPreview" class="img-thumbnail d-none mt-2" style="max-height:150px;">
                                    @endif
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-success">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        {{-- BACKUP --}}
                        <div class="tab-pane fade" id="backup">
                            <h5 class="fw-bold">Backup & Export</h5>
                            <p class="text-muted">Download and backup important data.</p>

                            <button class="btn btn-primary" id="backupBtn">
                                <i class="bi bi-database-down me-1"></i> Backup Database
                            </button>
                            <span id="backupSpinner" class="spinner-border spinner-border-sm text-primary ms-2 d-none" role="status" aria-hidden="true"></span>

                            <div class="alert alert-info mt-3">Automated backups can be added later.</div>
                        </div>

                        {{-- SECURITY --}}
                        <div class="tab-pane fade" id="security">
                            <h5 class="fw-bold">Security Settings</h5>
                            <p class="text-muted">Protect user data and system access.</p>

                            <form action="{{ route('admin.settings.updateSecurity') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="fw-semibold">Minimum Password Length</label>
                                    <input type="number" name="security_min_password_length" class="form-control" min="6"
                                           value="{{ setting('security_min_password_length', 8) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">Require Special Characters</label>
                                    <select name="security_require_special_char" class="form-select">
                                        <option value="1" {{ setting('security_require_special_char') ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ !setting('security_require_special_char') ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">Session Timeout (Minutes)</label>
                                    <input type="number" name="security_session_timeout" class="form-control" min="5"
                                           value="{{ setting('security_session_timeout', 30) }}">
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-success">Save Security Settings</button>
                                </div>
                            </form>
                        </div>

                        {{-- ACADEMIC GRADE LEVELS --}}
                        <div class="tab-pane fade" id="academicGradeLevels">
                            <h5 class="fw-bold">Grade Levels</h5>
                            <p class="text-muted">Manage all grade levels available in the system.</p>
                            <a href="{{ route('admin.grade-levels.index') }}" class="btn btn-success mb-2">Manage Grade Levels</a>
                        </div>

                        {{-- ACADEMIC SUBJECTS --}}
                        <div class="tab-pane fade" id="academicSubjects">
                            <h5 class="fw-bold">Subjects</h5>
                            <p class="text-muted">Manage all subjects available in the system.</p>
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-success mb-2">Manage Subjects</a>
                        </div>

                        {{-- CHANGE PASSWORD --}}
                        <div class="tab-pane fade" id="changePassword">
                            <h5 class="fw-bold">Change Password</h5>
                            <p class="text-muted">Update your account password.</p>

                            <form action="{{ route('admin.change-password.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="fw-semibold">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                    @error('current_password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                    @error('new_password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="fw-semibold">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <a href="{{ route('admin.password.request') }}" class="text-decoration-none">Forgot your password?</a>
                                </div>

                                <button class="btn btn-warning text-dark">
                                    <i class="bi bi-key-fill me-1"></i> Change Password
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('schoolLogoInput')?.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('schoolLogoPreview');
        img.src = e.target.result;
        img.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
});

// Backup button
document.getElementById('backupBtn')?.addEventListener('click', function() {
    const btn = this;
    const spinner = document.getElementById('backupSpinner');

    btn.disabled = true;
    spinner.classList.remove('d-none');

    fetch("{{ route('admin.settings.backup.database') }}")
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'backup_{{ env('DB_DATABASE') }}_{{ now()->format('Y-m-d_H-i-s') }}.sql';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(err => {
            alert('Backup failed. Check server logs.');
        })
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('d-none');
        });
});
</script>
@endpush
