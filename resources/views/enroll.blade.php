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
  margin:0;
  font-family: 'Aptos', sans-serif;
  background: radial-gradient(circle at top left, #0e2f0c 0%, #001a00 100%);
  color:#f2f2f2;
}

  .container-enroll {
    min-height: 75vh;
    display: grid;
    place-items: center;
    padding: 32px;
    position: relative;
  }

  /* Subtle particles (low-cost) */
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
  .enroll-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom: 18px;
  }
  .enroll-title { display:flex; gap:12px; align-items:center; }
  .enroll-title h1 { font-size:20px; margin:0; color:#FFD700; letter-spacing:0.2px; }
  .enroll-title p { margin:0; color:#cfcfcf; font-size:13px; }

  /* Progress steps */
  .progress-steps { display:flex; align-items:center; gap:12px; margin: 18px 0 24px; }
  .step {
    display:flex;
    align-items:center;
    gap:12px;
  }
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
  input.form-control, select.form-control, textarea.form-control {
    width:100%; padding:10px 12px; border-radius:8px; border:1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.03); color:#f5f5f5; font-size:14px;
    transition: box-shadow .16s ease, border-color .12s ease;
  }
  input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
    outline:none; border-color: rgba(255,215,0,0.9); box-shadow:0 0 0 6px rgba(255,215,0,0.06);
    background: rgba(255,255,255,0.04);
  }

  .note { font-size:13px; color:#bfbfbf; margin-top:6px; }

  .actions { display:flex; justify-content:space-between; gap:12px; margin-top:10px; }
  .btn { padding:10px 14px; border-radius:8px; font-weight:600; border: none; cursor:pointer; }
  .btn-ghost { background: transparent; color:#d6d6d6; border:1px solid rgba(255,255,255,0.04); }
  .btn-primary { background: linear-gradient(90deg,#FFD700,#c89b00); color:#02210b; box-shadow: 0 8px 20px rgba(0,0,0,0.4); }

  .small { font-size:13px; color:#ddd; }

  /* file input style */
  .custom-file { display:flex; gap:8px; align-items:center; }
  .custom-file input[type=file] { flex:1; }

  /* validation */
  .is-invalid { border-color:#ff4c4c !important; box-shadow: 0 0 0 6px rgba(255,76,76,0.06) !important; }
  .invalid-feedback { color:#ff8b8b; font-size:13px; margin-top:6px; }

  /* responsive */
  @media (max-width: 900px) {
    .row { gap:10px; }
    .enroll-card { padding:18px; }
  }
  @media (max-width: 560px) {
    .enroll-card { padding:14px; border-radius:10px; }
    .step-label { display:none; }
    .step-dot { width:30px; height:30px; font-size:13px; }
  }
</style>

<div class="container-enroll">
  {{-- <div class="particles" aria-hidden="true">
    <div class="particle" style="top:6%; left:12%; animation-delay:0s"></div>
    <div class="particle" style="top:25%; left:78%; animation-delay:1.2s"></div>
    <div class="particle" style="top:64%; left:34%; animation-delay:2.4s"></div>
    <div class="particle" style="top:84%; left:86%; animation-delay:1.8s"></div>
  </div> --}}

  <div class="enroll-card" role="main" aria-labelledby="enrollHeading">
    <div class="enroll-header">
      <div class="enroll-title">
        <img src="{{ asset('images/logo123-removebg-preview.png') }}" alt="CvSU" style="width:60px; border-radius:6px;">
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
              <input id="lrn" name="lrn" type="text" maxlength="12" class="form-control" pattern="\d{12}" required>
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
              <select id="grade_level" name="grade_level" class="form-control" required>
                <option value="">-- Select Grade --</option>
                @for($i=7;$i<=10;$i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
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
            <div class="col">
              <label for="last_name">Last Name</label>
              <input id="last_name" name="last_name" type="text" class="form-control" required>
            </div>
            <div class="col">
              <label for="first_name">First Name</label>
              <input id="first_name" name="first_name" type="text" class="form-control" required>
            </div>
            <div class="col">
              <label for="middle_name">Middle Name</label>
              <input id="middle_name" name="middle_name" type="text" class="form-control">
            </div>
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
              <select id="sex" name="sex" class="form-control" required>
                <option value="">-- Select --</option>
                <option>Male</option><option>Female</option>
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

          <div class="row" style="margin-top:12px;">
            <div class="col">
              <label for="birth_certificate">Birth Certificate (PSA/NSO)</label>
              <input id="birth_certificate" name="documents[birth_certificate]" type="file" class="form-control" accept=".pdf,.jpg,.png">
            </div>
            <div class="col">
              <label for="id_photo">1x1 or 2x2 ID Photo</label>
              <input id="id_photo" name="documents[id_photo]" type="file" class="form-control" accept=".jpg,.png">
            </div>
          </div>

          <p class="note">Accepted formats: PDF, JPG, PNG. Please bring originals for verification during the first week of classes.</p>
        </div>

        <div class="actions">
          <button type="button" class="btn btn-ghost" id="prevBtn5">Previous</button>
          <button type="submit" class="btn btn-primary" id="submitEnroll">Submit Enrollment</button>
        </div>
      </section>

    </form>
  </div>
</div>

<script>
  (function() {
    // helpers
    const steps = Array.from(document.querySelectorAll('.step'));
    const sections = Array.from(document.querySelectorAll('.section'));
    const form = document.getElementById('enrollForm');

    function goToStep(n) {
      // clamp n
      n = Math.max(1, Math.min(steps.length, n));
      // activate steps and sections
      steps.forEach(s => s.classList.toggle('active', parseInt(s.dataset.step) === n));
      sections.forEach(sec => sec.classList.toggle('active', parseInt(sec.dataset.step) === n));
      // scroll into view for mobile
      if (window.innerWidth <= 576) window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateSection(stepNumber) {
      const sec = document.querySelector('.section[data-step="'+stepNumber+'"]');
      if(!sec) return true;
      const required = sec.querySelectorAll('input[required], select[required], textarea[required]');
      for (let el of required) {
        if (el.type === 'file') continue; // optional file check left to server or final submit
        if (el.value === null || (typeof el.value === 'string' && el.value.trim() === '')) {
          el.classList.add('is-invalid'); el.focus();
          return false;
        }
        // specific pattern checks
        if (el.pattern && el.value) {
          const re = new RegExp('^' + el.pattern + '$');
          if (!re.test(el.value)) { el.classList.add('is-invalid'); el.focus(); return false; }
        }
        el.classList.remove('is-invalid');
      }
      // LRN additional check
      if (stepNumber === 1) {
        const lrn = document.getElementById('lrn');
        if (lrn && lrn.value.replace(/\D/g,'').length !== 12) {
          lrn.classList.add('is-invalid');
          document.getElementById('lrnError').style.display = 'block';
          lrn.focus();
          return false;
        } else {
          if (document.getElementById('lrnError')) document.getElementById('lrnError').style.display = 'none';
        }
      }
      return true;
    }

    // Next / previous bindings
    document.getElementById('nextBtn1').addEventListener('click', () => { if (validateSection(1)) goToStep(2); });
    document.getElementById('nextBtn2').addEventListener('click', () => { if (validateSection(2)) goToStep(3); });
    document.getElementById('nextBtn3').addEventListener('click', () => { if (validateSection(3)) goToStep(4); });
    document.getElementById('nextBtn4').addEventListener('click', () => { if (validateSection(4)) goToStep(5); });

    document.getElementById('prevBtn2').addEventListener('click', () => goToStep(1));
    document.getElementById('prevBtn3').addEventListener('click', () => goToStep(2));
    document.getElementById('prevBtn4').addEventListener('click', () => goToStep(3));
    document.getElementById('prevBtn5').addEventListener('click', () => goToStep(4));

    // allow clicking steps (only previous or current)
    steps.forEach(s => {
      s.addEventListener('click', () => {
        const target = parseInt(s.dataset.step);
        const active = parseInt(document.querySelector('.step.active').dataset.step);
        if (target <= active) goToStep(target);
      });
    });

    // LRN input behaviour - digits only and max 12
    const lrnInput = document.getElementById('lrn');
    if (lrnInput) {
      lrnInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g,'').slice(0,12);
        if (this.value.length === 12) { this.classList.remove('is-invalid'); document.getElementById('lrnError').style.display='none'; }
      });
    }

    // copy present -> permanent address when checkbox checked
    const sameCB = document.getElementById('same_as_present');
    if (sameCB) {
      sameCB.addEventListener('change', function() {
        const fields = ['house','street','barangay','city','province','region','zip'];
        fields.forEach(f => {
          const present = document.querySelector(`[name="present_${f}"]`);
          const permanent = document.querySelector(`[name="permanent_${f}"]`);
          if (present && permanent) {
            if (sameCB.checked) {
              permanent.value = present.value;
              permanent.setAttribute('readonly','true');
            } else {
              permanent.removeAttribute('readonly');
              permanent.value = '';
            }
          }
        });
      });
      // keep present -> permanent sync live while checked
      document.querySelectorAll('[name^="present_"]').forEach(el => {
        el.addEventListener('input', () => {
          if (sameCB.checked) {
            const name = el.getAttribute('name').replace('present_','');
            const perm = document.querySelector(`[name="permanent_${name}"]`);
            if (perm) perm.value = el.value;
          }
        });
      });
    }

    // final submit pre-validation
    form.addEventListener('submit', function(e) {
      // validate current visible step
      const activeStep = parseInt(document.querySelector('.step.active').dataset.step);
      for (let i=1;i<=activeStep;i++) {
        if (!validateSection(i)) { e.preventDefault(); return false; }
      }
      // all good -> allow normal submit
      return true;
    });

    // initialize to step 1
    goToStep(1);
  })();
</script>

<!-- ✅ Script to copy Current Address → Permanent Address -->
<script>
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
</script>

@endsection
