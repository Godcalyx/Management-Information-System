<div class="row mb-3">
    <div class="col-md-6">
        <label for="{{ $prefix }}_house" class="form-label">House No./Street</label>
        <input type="text" name="{{ $prefix }}_house" id="{{ $prefix }}_house" class="form-control" placeholder="e.g., 123-B" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $prefix }}_street" class="form-label">Street Name</label>
        <input type="text" name="{{ $prefix }}_street" id="{{ $prefix }}_street" class="form-control" placeholder="e.g., Mabini St." required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6 col-lg-4">
        <label for="{{ $prefix }}_barangay" class="form-label">Barangay</label>
        <input type="text" name="{{ $prefix }}_barangay" id="{{ $prefix }}_barangay" class="form-control" required>
    </div>
    <div class="col-md-6 col-lg-4">
        <label for="{{ $prefix }}_city" class="form-label">Municipality/City</label>
        <input type="text" name="{{ $prefix }}_city" id="{{ $prefix }}_city" class="form-control" required>
    </div>
    <div class="col-md-6 col-lg-4">
        <label for="{{ $prefix }}_province" class="form-label">Province</label>
        <input type="text" name="{{ $prefix }}_province" id="{{ $prefix }}_province" class="form-control" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="{{ $prefix }}_country" class="form-label">Country</label>
        <input type="text" name="{{ $prefix }}_country" id="{{ $prefix }}_country" class="form-control" value="Philippines" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $prefix }}_zip" class="form-label">Zip Code</label>
        <input type="text" name="{{ $prefix }}_zip" id="{{ $prefix }}_zip" class="form-control" pattern="\d{4}" maxlength="4" placeholder="e.g., 4115" required>
    </div>
</div>
