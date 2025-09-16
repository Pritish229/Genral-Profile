@extends('Admin.layout.app')

@section('title', 'Home | employees | Add employee')

@section('content')
<div class="page-content">
  <x-breadcrumb
    title="Add Employee"
    :links="['Home' => 'Admin.Dashboard', 'Employees' => 'employees.employeelist', 'Add Employee' => 'employees.create']" />

  <div id="alert-box" class="mt-2"></div>

  <form id="employeeForm" autocomplete="on" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
      <h5>Employee info </h5>
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
          <div class="col-md-6">
            <div class="mb-2">
              <label for="dob" class="mb-2 labeltxt">DOB</label>
              <input type="text" id="dob" name="dob" class="form-control flatpickr"
                placeholder="Select date of birth" value="{{ old('dob') }}">
              <small class="mb-3 pt-1 helpertxt">Date of Birth</small>
            </div>
          </div>
          <div class="col-md-6">
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

          <div class="col-md-6">
            <x-inputbox id="primary_email" label="Primary Email" type="email" placeholder="Enter employee email" name="primary_email"
              value="{{ old('primary_email') }}" :required="false" helpertxt="Parent or employee Email" />
          </div>

          <div class="col-md-6">
            <x-inputbox id="primary_phone" label="Primary Phone" type="text" placeholder="Enter phone number" name="primary_phone"
              value="{{ old('primary_phone') }}" :required="false" helpertxt="Parent or employee number" />
          </div>

        </div>

      </div>

      <div class="col-md-4">
        <label for="profile_picture">Profile Picture</label>
        <div class="input-images"></div>
      </div>





      <h5>Management info </h5>
      <hr style="color:#5156be">
      <div class="col-md-6">
        <x-inputbox id="employee_uid" label="Employee UID" type="text" placeholder="Enter unique employee code" name="employee_uid"
          value="{{ old('employee_uid') }}" :required="false" helpertxt="Unique code per tenant" />
      </div>

     

      <div class="col-md-6">
        <div class="mb-2">
          <label for="hire_date" class="mb-2 labeltxt">Hire Date</label>
          <input type="text" id="hire_date" name="hire_date" class="form-control flatpickr"
            placeholder="Select admission date" value="{{ old('hire_date') }}">
          <small class="mb-3 pt-1 helpertxt">Joining date</small>
        </div>
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
    let baseUrl = "{{ url('/employees') }}";

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
      localStorage.setItem("employeeProgress", value);
      $('#progress-bar').css('width', value + '%').text(value + '%');
    }

    function getProgress() {
      return parseInt(localStorage.getItem("employeeProgress") || 0, 10);
    }

    // Restore saved progress (if reload happens)
    setProgress(getProgress());

    // --- Form Submit ---
    $('#employeeForm').on('submit', function(e) {
      e.preventDefault();
      const $btn = $('#saveBtn').prop('disabled', true);
      const formData = new FormData(this);

      // 1️⃣ Show initial loader
      Swal.fire({
        title: 'Saving employee...',
        html: 'Please wait',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
      });

      $.ajax({
          url: "{{ route('employees.store') }}",
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
              document.getElementById('employeeForm').reset();
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