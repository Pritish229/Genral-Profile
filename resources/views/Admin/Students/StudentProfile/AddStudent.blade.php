@extends('Admin.layout.app')

@section('title', 'Add Student | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Add Student"
        :links="['Home' => 'Admin.Dashboard', 'Students' => '', 'Add Student' => 'students.create']" />

    <div id="alert-box" class="mt-2"></div>

    <form id="studentForm" autocomplete="on">
        @csrf
        <div class="row g-3">

        <h5>Student info </h5>
        <hr>
            <div class="col-md-4">
                <x-inputbox id="first_name" label="First Name UID" type="text" placeholder="Enter First Name" name="first_name"
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
            <div class="col-md-6">
                <div class="mb-2">
                    <label for="admission_date" class="mb-2 labeltxt">DOB</label>
                    <input type="text" id="dob" name="dob" class="form-control flatpickr"
                        placeholder="Select admission date" value="{{ old('dob') }}">
                    <small class="mb-3 pt-1 helpertxt">Date of Birth </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <label for="gender" class="mb-2 labeltxt">Gender</label>
                    <select name="gender" class="form-select" id="gender">
                        <option value="male">male</option>
                        <option value="female">female</option>
                        <option value="other">other</option>
                        <option value="unspecified">unspecified</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Gender Type </small>
                </div>
            </div>
            
            <div class="col-md-6">
                <x-inputbox id="primary_email" label="Primary Email" type="email" placeholder="Enter student email" name="primary_email"
                    value="{{ old('primary_email') }}" :required="false" helpertxt="Parent or student Email" />
            </div>

            <div class="col-md-6">
                <x-inputbox id="primary_phone" label="Primary Phone" type="text" placeholder="Enter phone number" name="primary_phone"
                    value="{{ old('primary_phone') }}" :required="false" helpertxt="Parent or student number" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="cast" label="Cast" type="text" placeholder="Enter Cast" name="cast"
                    value="{{ old('cast') }}" :required="false"  />
            </div>
            <div class="col-md-6">
                <x-inputbox id="religion" label="Religion" type="text" placeholder="Enter Cast" name="religion"
                    value="{{ old('religion') }}" :required="false"  />
            </div>

    <h5>Adminstration info </h5>
    <hr>
            <div class="col-md-6">
                <x-inputbox id="student_uid" label="Student UID" type="text" placeholder="Enter unique student code" name="student_uid"
                    value="{{ old('student_uid') }}" :required="false" helpertxt="Unique code per tenant" />
            </div>

            

            <div class="col-md-6">
                <x-inputbox id="admission_no" label="Admission Number" type="text" placeholder="Enter roll/admission number" name="admission_no"
                    value="{{ old('admission_no') }}" :required="false" helpertxt="Institution-issued admission number" />
            </div>

            <div class="col-md-6">
                <x-inputbox id="univ_admission_no" label="University Admission No" type="text" placeholder="Enter university admission number" name="univ_admission_no"
                    value="{{ old('univ_admission_no') }}" :required="false" helpertxt="University provided code" />
            </div>

            <div class="col-md-6">
                <div class="mb-2">
                    <label for="admission_date" class="mb-2 labeltxt">Admission Date</label>
                    <input type="text" id="admission_date" name="admission_date" class="form-control flatpickr"
                        placeholder="Select admission date" value="{{ old('admission_date') }}">
                    <small class="mb-3 pt-1 helpertxt">Joining date</small>
                </div>
            </div>

            <div class="col-md-12">
                <x-textareabox id="notes" label="Notes" placeholder="Enter internal notes" name="notes" value="{{ old('notes') }}" helpertxt="For internal remarks" />
            </div>
        </div>

        <div class="mt-3">
            <button id="saveBtn" type="submit" class="btn btn-primary">Save and Contitnue</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    $(function() {
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true
        });

        $('#studentForm').on('submit', function(e) {
            e.preventDefault();

            let $btn = $('#saveBtn');
            $btn.prop('disabled', true);

            Swal.fire({
                title: 'Saving Student...',
                html: `
                    <div class="progress mt-3">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            role="progressbar" style="width: 0%">0%</div>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    updateProgress(10);
                }
            });

            $.ajax({
                url: "{{ route('students.store') }}",
                type: "POST",
                data: $(this).serialize(),
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            updateProgress(50);
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    updateProgress(80);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#studentForm')[0].reset();
                            window.location.href = "{{ route('students.create') }}";
                        });
                        updateProgress(30);
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    Swal.close();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#alert-box').html(errorHtml);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong, please try again.'
                        });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
