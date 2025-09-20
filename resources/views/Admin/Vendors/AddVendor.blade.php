@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Add Vendor')

@section('content')
<div class="page-content">
  <x-breadcrumb
    title="Add Vendor"
    :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.vendorlist', 'Add Vendor' => 'vendors.create']" />

  <div id="alert-box" class="mt-2"></div>

  <form id="vendorForm" autocomplete="on" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
      <h5>Vendor info </h5>
      <hr style="color:#5156be">

      <div class="col-md-4">
        <x-inputbox id="first_name" label="First Name" type="text" placeholder="Enter First Name" name="first_name"
          value="{{ old('first_name') }}" :required="true" helpertxt="First Name Maximum 70 Character" />
      </div>
      <div class="col-md-4">
        <x-inputbox id="middle_name" label="Middle Name" type="text" placeholder="Enter Middle Name" name="middle_name"
          value="{{ old('middle_name') }}" :required="false" helpertxt="Middle Name Maximum 70 Character" />
      </div>
      <div class="col-md-4">
        <x-inputbox id="last_name" label="Last Name" type="text" placeholder="Enter Last Name" name="last_name"
          value="{{ old('last_name') }}" :required="false" helpertxt="Last Name Maximum 70 Character" />
      </div>

      <div class="col-md-8">
        <div class="row">
          <div class="col-md-4">
            <div class="mb-2">
              <label for="dob" class="mb-2 labeltxt">DOB</label>
              <input type="text" id="dob" name="dob" class="form-control flatpickr"
                placeholder="Select date of birth" value="{{ old('dob') }}">
              <small class="mb-3 pt-1 helpertxt">Date of Birth</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-2">
              <label for="gender" class="mb-2 labeltxt">Gender</label>
              <select name="gender" class="form-select" id="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
                <option value="unspecified">Unspecified</option>
              </select>
              <small class="mb-3 pt-1 helpertxt">Select Gender Type</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-2">
              <label for="marital_status" class="mb-2 labeltxt">Maritial Status</label>
              <select name="marital_status" class="form-select" id="marital_status">
                <option value="male">Single</option>
                <option value="married">Married</option>
                <option value="divorsed">Divorsed</option>
                <option value="widowed">Widowed</option>
                <option value="other">Other</option>
              </select>
              <small class="mb-3 pt-1 helpertxt">Select Gender Type</small>
            </div>
          </div>

          <div class="col-md-6">
            <x-inputbox id="primary_email" label="Primary Email" type="email" placeholder="Enter vendor email" name="primary_email"
              value="{{ old('primary_email') }}" :required="false" helpertxt="Vendor Email" />
          </div>

          <div class="col-md-6">
            <x-inputbox id="primary_phone" label="Primary Phone" type="text" placeholder="Enter phone number" name="primary_phone"
              value="{{ old('primary_phone') }}" :required="false" helpertxt="Vendor number" />
          </div>
          <div class="col-md-6">
            <x-inputbox id="occupation" label="Occupation" type="text" placeholder="Enter occupation" name="occupation"
              value="{{ old('occupation') }}" :required="false" helpertxt="Enter Occupation" />
          </div>
          <div class="col-md-6">
            <x-inputbox id="Nationality" label="Nationality" type="text" placeholder="Enter Nationality" name="nationality"
              value="{{ old('nationality') }}" :required="false" helpertxt="Enter Nationality" />
          </div>
          

        </div>

      </div>

      <div class="col-md-4">
        <label for="profile_picture">Profile Picture</label>
        <div class="input-images"></div>
      </div>





      <h5>Adminstration info </h5>
      <hr style="color:#5156be">
      <div class="col-md-6">
        <x-inputbox id="vendor_uid" label="Vendor UID" type="text" placeholder="Enter unique vendor code" name="vendor_uid"
          value="{{ old('vendor_uid') }}" :required="false" helpertxt="Unique code per tenant" />
      </div>
      <div class="col-md-6">
        <div class="mb-2">
          <label for="onboarding_channel" class="mb-2 labeltxt">On Boarding</label>
          <select name="onboarding_channel" class="form-select" id="onboarding_channel">
            <option value="web">Web</option>
            <option value="mobile">Mobile</option>
            <option value="partner">Partner</option>
            <option value="import">import</option>
            <option value="other">Other</option>
          </select>
          <small class="mb-3 pt-1 helpertxt">Select On Boarding Type</small>
        </div>
      </div>
      <div class="col-md-6">
            <x-inputbox id="preferred_language" label="Preferred Language" type="text" placeholder="Enter Preferred Language" name="preferred_language"
              value="{{ old('preferred_language') }}" :required="false" helpertxt="Max 10 Character" />
          </div>
          <div class="col-md-6">
            <x-inputbox id="preferred_currency" label="Preferred Currency" type="text" placeholder="Enter Preferred Currency" name="preferred_currency"
              value="{{ old('preferred_currency') }}" :required="false" helpertxt="Enter Preferred Currency" />
          </div>



      <div class="col-md-12">
        <x-textareabox id="notes" label="Notes" placeholder="Enter internal notes" name="notes" value="{{ old('notes') }}" helpertxt="For internal remarks" />
      </div>
    </div>

    <div class="mt-3">
      <button id="saveBtn" type="submit" class="btn btn-primary">Save and Continue</button>
    </div>
  </form>
</div>
@endsection

@section('script')

<script>
  jQuery(function($) {
    let baseUrl = "{{ url('/vendors') }}";

    // Avatar uploader
    $('.input-images').imageUploader({
      multiple: false,
      imagesInputName: 'avatar_url',
      preloadedInputName: 'preloaded',
      label: 'Click to upload profile picture',
      preloaded: []
    });

    // Date pickers
    $(".flatpickr").flatpickr({
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "j F Y",
      allowInput: true
    });

    // --- Progress Helpers ---
    function setProgress(value) {
      localStorage.setItem("vendorProgress", value);
      $('#progress-bar').css('width', value + '%').text(value + '%');
    }

    function getProgress() {
      return parseInt(localStorage.getItem("vendorProgress") || 0, 10);
    }

    // Restore saved progress (if reload happens)
    setProgress(getProgress());

    // --- Form Submit ---
    $('#vendorForm').on('submit', function(e) {
      e.preventDefault();
      const $btn = $('#saveBtn').prop('disabled', true);
      const formData = new FormData(this);

      // 1️⃣ Show initial loader
      Swal.fire({
        title: 'Saving vendor...',
        html: 'Please wait',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
      });

      $.ajax({
          url: "{{ route('vendors.store') }}",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false
        })
        .done(function(response) {
          Swal.close(); // close loader

          if (response && response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message || 'Saved.',
              timer: 1600,
              showConfirmButton: false
            }).then(() => {
              // 👉 Step 1 milestone = 10%
              setProgress(10);

              // Reset form & uploader
              document.getElementById('vendorForm').reset();
              const $box = $('.input-images').empty();
              $box.imageUploader({
                multiple: false,
                imagesInputName: 'avatar_url',
                preloadedInputName: 'preloaded',
                label: 'Click to upload profile picture',
                preloaded: []
              });

              // Redirect to Step 2 (Basic Info)
              window.location.href = `${baseUrl}/${response.data.id}/Basicinfo`;
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: (response && response.message) ? response.message : 'Request failed.'
            });
          }
        })
        .fail(function(xhr) {
          Swal.close();
          $btn.prop('disabled', false);

          if (xhr.status === 422) {
            const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};
            let html = '<div class="alert alert-danger"><ul>';
            Object.keys(errors).forEach(k => {
              const v = errors[k];
              html += '<li>' + (Array.isArray(v) ? v[0] : v) + '</li>';
            });
            html += '</ul></div>';
            $('#alert-box').html(html);
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Something went wrong, please try again.'
            });
          }
        })
        .always(function() {
          $btn.prop('disabled', false);
        });
    });
  });
</script>
@endsection