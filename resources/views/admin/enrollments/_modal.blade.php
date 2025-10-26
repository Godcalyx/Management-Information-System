<div class="modal fade" id="viewModal{{ $enrollment->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $enrollment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-3">

            {{-- Header --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-semibold" id="viewModalLabel{{ $enrollment->id }}">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Enrollment Details – {{ $enrollment->first_name }} {{ $enrollment->last_name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">

                {{-- Section: Enrollment Info --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-success border-bottom pb-1 mb-3">Enrollment Info</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>LRN:</strong> {{ $enrollment->lrn ?? 'N/A' }}</div>
                        <div class="col-md-6 mb-2"><strong>Email:</strong> {{ $enrollment->email }}</div>
                        <div class="col-md-6 mb-2"><strong>School Year:</strong> {{ $enrollment->school_year ?? 'N/A' }}</div>
                        <div class="col-md-6 mb-2"><strong>Grade Level:</strong> {{ $enrollment->grade_level ?? 'N/A' }}</div>
                    </div>
                </div>

                {{-- Section: Learner Info --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-success border-bottom pb-1 mb-3">Learner Info</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>Full Name:</strong> {{ $enrollment->first_name }} {{ $enrollment->middle_name }} {{ $enrollment->last_name }} {{ $enrollment->extension_name }}</div>
                        <div class="col-md-6 mb-2"><strong>Birthdate:</strong> {{ $enrollment->birthdate }}</div>
                        <div class="col-md-6 mb-2"><strong>Place of Birth:</strong> {{ $enrollment->birthplace }}</div>
                        <div class="col-md-6 mb-2"><strong>Sex:</strong> {{ $enrollment->sex }}</div>
                        <div class="col-md-6 mb-2"><strong>Mother Tongue:</strong> {{ $enrollment->mother_tongue }}</div>
                        <div class="col-md-6 mb-2"><strong>Indigenous Community?:</strong> {{ $enrollment->ip_community }}</div>
                        <div class="col-md-6 mb-2"><strong>If Yes, specify:</strong> {{ $enrollment->ip_specify }}</div>
                        <div class="col-md-6 mb-2"><strong>4Ps Beneficiary?:</strong> {{ $enrollment->is_4ps }}</div>
                        <div class="col-md-6 mb-2"><strong>4Ps Household ID:</strong> {{ $enrollment->household_id }}</div>
                    </div>
                </div>

                {{-- Section: Address Info --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-success border-bottom pb-1 mb-3">Address Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Current Address:</strong></p>
                            <p class="small">
                                {{ $enrollment->current_house }}, {{ $enrollment->current_street }},
                                {{ $enrollment->current_barangay }}, {{ $enrollment->current_city }},
                                {{ $enrollment->current_province }}
                                {{ $enrollment->current_zip ? ', ' . $enrollment->current_zip : '' }}
                                {{ $enrollment->current_country ? ', ' . $enrollment->current_country : '' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Permanent Address:</strong></p>
                            <p class="small">
                                {{ $enrollment->permanent_house }}, {{ $enrollment->permanent_street }},
                                {{ $enrollment->permanent_barangay }}, {{ $enrollment->permanent_city }},
                                {{ $enrollment->permanent_province }}
                                {{ $enrollment->permanent_zip ? ', ' . $enrollment->permanent_zip : '' }}
                                {{ $enrollment->permanent_country ? ', ' . $enrollment->permanent_country : '' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Section: Parent / Guardian Info --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-success border-bottom pb-1 mb-3">Parent / Guardian Information</h6>

                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-secondary fw-semibold">Father</h6>
                            <p class="small mb-1"><strong>Name:</strong> {{ $enrollment->father_first }} {{ $enrollment->father_middle }} {{ $enrollment->father_last }}</p>
                            <p class="small mb-0"><strong>Contact:</strong> {{ $enrollment->father_contact }}</p>
                        </div>

                        <div class="col-md-4">
                            <h6 class="text-secondary fw-semibold">Mother</h6>
                            <p class="small mb-1"><strong>Name:</strong> {{ $enrollment->mother_first }} {{ $enrollment->mother_middle }} {{ $enrollment->mother_last }}</p>
                            <p class="small mb-0"><strong>Contact:</strong> {{ $enrollment->mother_contact }}</p>
                        </div>

                        <div class="col-md-4">
                            <h6 class="text-secondary fw-semibold">Guardian</h6>
                            <p class="small mb-1"><strong>Name:</strong> {{ $enrollment->guardian_first }} {{ $enrollment->guardian_middle }} {{ $enrollment->guardian_last }}</p>
                            <p class="small mb-0"><strong>Contact:</strong> {{ $enrollment->guardian_contact }}</p>
                        </div>
                    </div>
                </div>

                {{-- Section: Learning Modality
                <div>
                    <h6 class="fw-bold text-success border-bottom pb-1 mb-3">Learning Modality</h6>
                    @php
                        $modalities = is_string($enrollment->modality) 
                            ? json_decode($enrollment->modality, true) 
                            : (is_array($enrollment->modality) ? $enrollment->modality : []);
                    @endphp
                    <p class="mb-0">{{ implode(', ', $modalities) ?: 'N/A' }}</p>
                </div> --}}

            </div>

            {{-- Footer --}}
            <div class="modal-footer bg-light">
                <a href="{{ route('admin.enrollments.export', $enrollment->id) }}" 
                   class="btn btn-primary" target="_blank">
                    <i class="bi bi-download me-1"></i> Download PDF
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>

        </div>
    </div>
</div>
