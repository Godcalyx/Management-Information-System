@extends('layouts.guest')

@section('content')
<style>
  /* Reset / base */
  @font-face {
    font-family: 'Aptos';
    src: url('/fonts/Aptos.ttf') format('truetype');
  }
  @font-face {
    font-family: 'Aptos-Bold';
    src: url('/fonts/Aptos-Bold.ttf') format('truetype');
  }

  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: 'Aptos', sans-serif;
    background: radial-gradient(circle at top left, #0e2f0c 0%, #001a00 100%);
    color: #f2f2f2;
  }

  .container-enroll {
    min-height: 75vh;
    display: grid;
    place-items: center;
    padding: 32px;
    position: relative;
  }

  /* Subtle particles */
  .particles { position:absolute; inset:0; pointer-events:none; z-index:0; overflow:hidden; }
  .particle { position:absolute; width:3px; height:3px; background:rgba(255,255,255,0.6); border-radius:50%; opacity:0.35; transform: translateY(0); animation: float 6s infinite ease-in-out; }
  @keyframes float { 0%,100%{ transform:translateY(0); } 50%{ transform:translateY(-18px); } }

  /* Card / glass panel */
  .enroll-card {
    position: relative;
    z-index:1;
    width:100%;
    max-width: 920px;
    background: rgba(20,40,20,0.92);
    border-radius: 14px;
    padding: 26px;
    box-shadow: 0 18px 50px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,215,0,0.06);
    backdrop-filter: blur(6px);
  }

  /* Header */
  .enroll-header { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom: 18px; }
  .enroll-title { display:flex; gap:12px; align-items:center; }
  .enroll-title h1 { font-size:20px; margin:0; color:#FFD700; letter-spacing:0.2px; }
  .enroll-title p { margin:0; color:#cfcfcf; font-size:13px; }

  /* Progress steps */
  .progress-steps { display:flex; align-items:center; gap:12px; margin: 18px 0 24px; }
  .step { display:flex; align-items:center; gap:12px; }
  .step-dot {
    width:36px; height:36px; border-radius:50%;
    display:grid; place-items:center;
    background: rgba(255,255,255,0.04);
    color:#cfcfcf; font-weight:600;
    border: 2px solid rgba(255,255,255,0.04);
    transition: all .22s ease;
  }
  .step.active .step-dot {
    background: linear-gradient(90deg, #FFD700, #c89b00);
    color:#02210b;
    box-shadow: 0 6px 18px rgba(0,0,0,0.45), 0 6px 18px rgba(255,215,0,0.08);
    border-color: rgba(255,215,0,0.9);
  }
  .step-label { font-size:13px; color:#cfcfcf; max-width:120px; }
  .step-line { height:2px; background: rgba(255,255,255,0.04); flex:1; border-radius:2px; }

  /* Form sections */
  form.enroll-form { display:block; margin-top:10px; }
  .section { display:none; gap:16px; }
  .section.active { display:block; animation: fadeIn .18s ease; }
  @keyframes fadeIn { from{opacity:0; transform: translateY(6px);} to{opacity:1; transform:none;} }

  .panel { background: rgba(255,255,255,0.02); padding:16px; border-radius:10px; border:1px solid rgba(255,255,255,0.02); margin-bottom:16px; }
  .panel h3 { margin:0 0 8px 0; color:#ffd; font-size:15px; }
  .row { display:flex; gap:12px; flex-wrap:wrap; }
  .col { flex:1 1 220px; min-width:150px; }

  label { display:block; font-size:13px; color:#cfcfcf; margin-bottom:6px; }

  /* Inputs / selects / textareas */
  input.form-control,
  select.form-control,
  textarea.form-control {
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.06);
    color:#ffffff;
    font-size:14px;
    font-weight:500;
    transition: box-shadow .16s ease, border-color .12s ease;
  }

  input.form-control::placeholder,
  textarea.form-control::placeholder {
    color: rgba(255,255,255,0.7);
  }

  input.form-control:focus,
  select.form-control:focus,
  textarea.form-control:focus {
    outline:none;
    border-color: rgba(255,215,0,0.9);
    box-shadow:0 0 0 6px rgba(255,215,0,0.06);
    background: rgba(255,255,255,0.08);
    color:#ffffff;
  }

  /* File inputs */
  input[type="file"].form-control {
    color: #fff;
    background: rgba(255,255,255,0.05);
  }

  .note { font-size:13px; color:#bfbfbf; margin-top:6px; }

  .actions { display:flex; justify-content:space-between; gap:12px; margin-top:10px; }
  .btn { padding:10px 14px; border-radius:8px; font-weight:600; border: none; cursor:pointer; }
  .btn-ghost { background: transparent; color:#d6d6d6; border:1px solid rgba(255,255,255,0.04); }
  .btn-primary { background: linear-gradient(90deg,#FFD700,#c89b00); color:#02210b; box-shadow: 0 8px 20px rgba(0,0,0,0.4); }

  .small { font-size:13px; color:#ddd; }

  /* validation */
  .is-invalid { border-color:#ff4c4c !important; box-shadow: 0 0 0 6px rgba(255,76,76,0.06) !important; }
  .invalid-feedback { color:#ff8b8b; font-size:13px; margin-top:6px; }

  /* responsive */
  @media (max-width: 900px) { .row { gap:10px; } .enroll-card { padding:18px; } }
  @media (max-width: 560px) { .enroll-card { padding:14px; border-radius:10px; } .step-label { display:none; } .step-dot { width:30px; height:30px; font-size:13px; } }
</style>

<div class="container-enroll">
  <div class="enroll-card" role="main" aria-labelledby="enrollHeading">
    <div class="enroll-header">
      <div class="enroll-title">
        <img src="{{ asset('images/logo.jpg') }}" alt="CvSU" style="width:60px; border-radius:6px;">
        <div>
          <h1 id="enrollHeading">LSHS Student Enrollment</h1>
          <p class="small">Please follow the steps. You can go back to review before submitting.</p>
        </div>
      </div>
      <div class="small">Secure • Official • CvSU</div>
    </div>

    <!-- Progress -->
    <div class="progress-steps" id="progressSteps" aria-hidden="false">
      <div class="step active" data-step="1">
        <div class="step-dot">1</div>
        <div class="step-label">Enrollment</div>
        <div class="step-line"></div>
      </div>
      <div class="step" data-step="2">
        <div class="step-dot">2</div>
        <div class="step-label">Learner</div>
        <div class="step-line"></div>
      </div>
      <div class="step" data-step="3">
        <div class="step-dot">3</div>
        <div class="step-label">Address</div>
        <div class="step-line"></div>
      </div>
      <div class="step" data-step="4">
        <div class="step-dot">4</div>
        <div class="step-label">Parent</div>
        <div class="step-line"></div>
      </div>
      <div class="step" data-step="5">
        <div class="step-dot">5</div>
        <div class="step-label">Submit</div>
      </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('enroll.submit') }}" enctype="multipart/form-data" class="enroll-form" id="enrollForm" novalidate>
      @csrf

      <!-- Step 1: Enrollment Info -->
      <section class="section active" data-step="1" id="step-1">
        <div class="panel">
          <h3>Enrollment Information</h3>
          <div class="row">
            <div class="col">
              <label for="lrn">Learner Reference Number (LRN)</label>
             <input id="lrn" name="lrn" type="text" maxlength="12" class="form-control" pattern="\d{12}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
              <div class="invalid-feedback" id="lrnError" style="display:none;">LRN must be exactly 12 digits.</div>
            </div>
            <div class="col">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" class="form-control" required>
            </div>
            <div class="col">
              <label for="school_year">School Year</label>
              <input id="school_year" name="school_year" type="text" class="form-control" placeholder="e.g. 2024-2025">
            </div>
            <div class="col">
  <label for="grade_level">Grade Level to Enroll</label>
  {{-- <select id="grade_level" name="grade_level" class="form-control" 
          style="background: rgba(38, 53, 33, 0.95); color:rgba(239, 230, 230, 0.95); border-color:#ccc;">
    <option value="">-- Select Grade --</option>
    @for($i=7;$i<=10;$i++)
      <option value="{{ $i }}">{{ $i }}</option>
    @endfor
  </select> --}}
  <select name="grade_level_id" class="form-select" style="background: rgba(38, 53, 33, 0.95); color:rgba(239, 230, 230, 0.95); border-color:#ccc;" required>
    <option value="">-- Select Grade Level --</option>
    @foreach($gradeLevels as $grade)
        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
    @endforeach
</select>
</div>

          </div>
        </div>
        <div class="actions">
          <button type="button" class="btn btn-ghost" onclick="window.location.href='{{ route('login.student') }}'">Back to Login</button>
          <div>
            <button type="button" class="btn btn-primary" id="nextBtn1">Next</button>
          </div>
        </div>
      </section>

      <!-- Step 2: Learner Info -->
      <section class="section" data-step="2" id="step-2">
        <div class="panel">
          <h3>Learner Information</h3>
          <div class="row">
            <div class="mb-3">
    <label class="form-label fw-semibold">Last Name</label>
    <input type="text" name="last_name" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">First Name</label>
    <input type="text" name="first_name" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Middle Name</label>
    <input type="text" name="middle_name" class="form-control">
</div>


          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label for="extension_name">Extension Name</label>
              <input id="extension_name" name="extension_name" type="text" class="form-control" placeholder="Jr., III">
            </div>
            <div class="col">
              <label for="birthdate">Birthdate</label>
              <input id="birthdate" name="birthdate" type="date" class="form-control" required>
            </div>
            <div class="col">
              <label for="birthplace">Place of Birth</label>
              <input id="birthplace" name="birthplace" type="text" class="form-control" required>
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label for="sex">Sex</label>
              <select id="sex" name="sex" class="form-control" style="background: rgba(38, 53, 33, 0.95); color:rgba(239, 230, 230, 0.95); border-color:#ccc;" required>
                <option value="">-- Select --</option>
                <option>Male</option>
                <option>Female</option>
              </select>
            </div>
            <div class="col">
              <label for="mother_tongue">Mother Tongue</label>
              <input id="mother_tongue" name="mother_tongue" type="text" class="form-control" placeholder="e.g. Tagalog" required>
            </div>
            <div class="col">
              <label for="ip_community">Indigenous Community?</label>
              <select id="ip_community" name="ip_community" class="form-control">
                <option value="No">No</option><option value="Yes">Yes</option>
              </select>
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label for="ip_specify">If Yes, specify</label>
              <input id="ip_specify" name="ip_specify" type="text" class="form-control">
            </div>
            <div class="col">
              <label for="is_4ps">4Ps Beneficiary?</label>
              <select id="is_4ps" name="is_4ps" class="form-control">
                <option value="No">No</option><option value="Yes">Yes</option>
              </select>
            </div>
            <div class="col">
              <label for="household_id">4Ps Household ID</label>
              <input id="household_id" name="household_id" type="text" class="form-control">
            </div>
          </div>
        </div>

        <div class="actions">
          <button type="button" class="btn btn-ghost" id="prevBtn2">Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn2">Next</button>
        </div>
      </section>

      <!-- Step 3: Address -->
      <section class="section" data-step="3" id="step-3">
        <div class="panel">
          <h3>Current Address</h3>
          <div id="current-address">
            {{-- Uses prefix="current" so it matches controller fields --}}
            @include('partials.address-form', ['prefix' => 'current'])
          </div>

          <div style="margin-top: 12px;">
            <label class="small" style="display: flex; align-items: center; gap: 8px;">
              <input type="checkbox" id="same_address"> Same as Current Address
            </label>
          </div>

          <h3 style="margin-top: 14px;">Permanent Address</h3>
          <div id="permanent-address">
            {{-- Uses prefix="permanent" --}}
            @include('partials.address-form', ['prefix' => 'permanent'])
          </div>
        </div>

        <div class="actions">
          <button type="button" class="btn btn-ghost" id="prevBtn3">Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn3">Next</button>
        </div>
      </section>

      <!-- Step 4: Parent/Guardian -->
      <section class="section" data-step="4" id="step-4">
        <div class="panel">
          <h3>Parent / Guardian Information</h3>
          @include('partials.parent-guardian-form')
        </div>

        <div class="actions">
          <button type="button" class="btn btn-ghost" id="prevBtn4">Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn4">Next</button>
        </div>
      </section>

      <!-- Step 5: Uploads & Submit -->
      <section class="section" data-step="5" id="step-5">
        <div class="panel">
          <h3>Upload Documents</h3>

          <div class="row">
            <div class="col">
              <label for="report_card">Report Card / Form 138 *</label>
              <input id="report_card" name="documents[report_card]" type="file" class="form-control" accept=".pdf,.jpg,.png">
            </div>
            <div class="col">
              <label for="good_moral">Certificate of Good Moral *</label>
              <input id="good_moral" name="documents[good_moral]" type="file" class="form-control" accept=".pdf,.jpg,.png">
            </div>
          </div>

          <div class="row" style="margin-top:10px;">
            <div class="col">
              <label for="birth_cert">Birth Certificate</label>
              <input id="birth_cert" name="documents[birth_cert]" type="file" class="form-control" accept=".pdf,.jpg,.png">
            </div>
            <div class="col">
              <label for="id_picture">Learner Picture</label>
              <input id="id_picture" name="documents[id_picture]" type="file" class="form-control" accept=".jpg,.png">
            </div>
          </div>

          <p class="note">* Required files. Maximum size: 5MB each.</p>
        </div>

        <div class="actions">
          <button type="button" class="btn btn-ghost" id="prevBtn5">Previous</button>
          <button type="submit" class="btn btn-primary">Submit Enrollment</button>
        </div>
      </section>
    </form>
  </div>
</div>

<script>
  // Multi-step form logic with validation
  const sections = document.querySelectorAll('.section');
  let currentStep = 0;

  const showStep = (step) => {
    sections.forEach((s, i) => s.classList.toggle('active', i===step));
    document.querySelectorAll('.progress-steps .step').forEach((s,i)=>{
      s.classList.toggle('active', i<=step);
    });
  }

  const validateStep = (step) => {
    const inputs = sections[step].querySelectorAll('input, select, textarea');
    let valid = true;
    inputs.forEach(input => {
      if (input.hasAttribute('required') && !input.value) {
        valid = false;
        input.classList.add('is-invalid');
      } else {
        input.classList.remove('is-invalid');
      }
    });
    return valid;
  }

  const nextBtns = document.querySelectorAll('[id^="nextBtn"]');
  nextBtns.forEach(btn => btn.addEventListener('click', () => {
    if (validateStep(currentStep)) {
      if (currentStep < sections.length - 1) {
        currentStep++;
        showStep(currentStep);
      }
    } 
    // else {
    //   alert("Please fill in all required fields before proceeding.");
    // }
  }));

  const prevBtns = document.querySelectorAll('[id^="prevBtn"]');
  prevBtns.forEach(btn => btn.addEventListener('click', () => {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  }));

  // Optional: remove invalid highlight on input
  document.querySelectorAll('input, select, textarea').forEach(input => {
    input.addEventListener('input', () => input.classList.remove('is-invalid'));
  });

  document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('same_address');
    const currentFields = document.querySelectorAll('[name^="current_"]');
    const permanentFields = document.querySelectorAll('[name^="permanent_"]');

    if (!checkbox) return;

    // Copy current → permanent when checked
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            currentFields.forEach(el => {
                const fieldName = el.getAttribute('name').replace('current_', '');
                const permField = document.querySelector(`[name="permanent_${fieldName}"]`);
                if (permField) {
                    permField.value = el.value;
                    permField.readOnly = true; // lock permanent fields
                }
            });
        } else {
            // Clear and re-enable permanent fields
            permanentFields.forEach(el => {
                el.readOnly = false;
                el.value = '';
            });
        }
    });

    // Keep permanent synced live while typing in current
    currentFields.forEach(el => {
        el.addEventListener('input', function() {
            if (checkbox.checked) {
                const fieldName = this.getAttribute('name').replace('current_', '');
                const permField = document.querySelector(`[name="permanent_${fieldName}"]`);
                if (permField) permField.value = this.value;
            }
        });
    });
});

  // Intercept form submission
  const enrollForm = document.getElementById('enrollForm');
  enrollForm.addEventListener('submit', function(e) {
    // e.preventDefault(); // prevent default form submission

    // Optional: you can do Ajax submit here if needed
    // For now, just simulate success

    // Show a temporary success message
    const successDiv = document.createElement('div');
    successDiv.textContent = "Enrollment submitted successfully!";
    successDiv.style.position = 'fixed';
    successDiv.style.top = '20px';
    successDiv.style.left = '50%';
    successDiv.style.transform = 'translateX(-50%)';
    successDiv.style.padding = '16px 24px';
    successDiv.style.backgroundColor = '#28a745';
    successDiv.style.color = '#fff';
    successDiv.style.borderRadius = '8px';
    successDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.25)';
    successDiv.style.zIndex = '9999';
    document.body.appendChild(successDiv);

    setTimeout(() => {
      successDiv.remove();
      // Redirect to student login page after showing message
      window.location.href = "{{ route('login.student') }}";
    }, 2000); // 2 seconds
  });
</script>

@endsection
