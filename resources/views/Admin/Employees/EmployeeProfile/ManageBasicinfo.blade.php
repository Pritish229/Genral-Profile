@extends('Admin.layout.app')

@section('title', 'Home | Update Basic Info')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Update Basic Info"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Employees' => 'employees.Employeelist.all',
            'Employee Detail' => ['employees.viewDetails', $id],
            'Update Basic Info' => ''
        ]" />

    <form id="employeeForm" enctype="multipart/form-data">
        <div class="row align-items-center">
            <!-- Profile Picture -->
            <div class="col-md-3 mb-3 text-center">
                <label for="avatar" class="form-label">Profile Picture</label>
                <div class="input-images"></div>
                <small class="helpertxt d-block mt-1">Upload a profile picture (jpg, png)</small>
            </div>

            <!-- Name Fields -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4">
                        <x-inputbox id="first_name" label="First Name" type="text" placeholder="Enter First Name" name="first_name"
                            :required="true" helpertxt="First Name Maximum 70 Characters" />
                    </div>
                    <div class="col-md-4">
                        <x-inputbox id="middle_name" label="Middle Name" type="text" placeholder="Enter Middle Name" name="middle_name"
                            :required="false" helpertxt="Middle Name Maximum 70 Characters" />
                    </div>
                    <div class="col-md-4">
                        <x-inputbox id="last_name" label="Last Name" type="text" placeholder="Enter Last Name" name="last_name"
                            :required="false" helpertxt="Last Name Maximum 70 Characters" />
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dob" class="mb-1">DOB</label>
                            <input type="text" id="dob" name="dob" class="form-control flatpickr" placeholder="Select date of birth">
                            <small class="helpertxt">Date of Birth</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gender" class="mb-1">Gender</label>
                            <select name="gender" class="form-select" id="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="unspecified">Unspecified</option>
                            </select>
                            <small class="helpertxt">Select Gender Type</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job & Emergency Info -->
        <div class="row mt-3">
            <div class="col-md-4">
                <x-inputbox id="designation" label="Designation" type="text" placeholder="Enter Designation" name="designation"
                    :required="false" helpertxt="Designation" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="department" label="Department" type="text" placeholder="Enter Department" name="department"
                    :required="false" helpertxt="Department" />
            </div>
            <div class="col-md-4">
                <label for="manager_id">Manager</label>
                <select id="manager_id" name="manager_id" class="form-control select2"></select>
            </div>

            <div class="col-md-3 mt-3">
                <label for="employment_type">Employee Type</label>
                <select name="employment_type" class="form-select" id="employment_type">
                    <option value="full_time">Full time</option>
                    <option value="part_time">Part time</option>
                    <option value="contract">Contract</option>
                    <option value="intern">Intern</option>
                    <option value="consultant">Consultant</option>
                </select>
            </div>
            <div class="col-md-3 mt-3">
                <x-inputbox id="salary_currency" label="Salary Currency" type="text" placeholder="Enter Salary Currency" name="salary_currency"
                    :required="false" helpertxt="Salary currency Max 3 Digits" />
            </div>
            <div class="col-md-3 mt-3">
                <x-inputbox id="base_salary" label="Base Salary" type="number" placeholder="Enter Base Salary" name="base_salary"
                    :required="false" helpertxt="Base Salary must be a valid number" />
            </div>
            <div class="col-md-3 mt-3">
                <x-inputbox id="experience_years" label="Experience Years" type="text" placeholder="Enter Experience Years" name="experience_years"
                    :required="false" helpertxt="Experience Years Must Be Number Eg: 2.3" />
            </div>

            <div class="col-md-3 mt-3">
                <x-inputbox id="emergency_contact_name" label="Emergency Contact Person" type="text" placeholder="Enter Emergency Contact Person" name="emergency_contact_name"
                    :required="false" helpertxt="Maximum 70 Characters" />
            </div>
            <div class="col-md-3 mt-3">
                <x-inputbox id="emergency_relation" label="Emergency Contact Relation" type="text" placeholder="Enter Emergency Contact Relation" name="emergency_relation"
                    :required="false" helpertxt="Maximum 70 Characters" />
            </div>
            <div class="col-md-3 mt-3">
                <x-inputbox id="emergency_contact_phone" label="Emergency Contact Number" type="text" placeholder="Enter Emergency Contact Number" name="emergency_contact_phone"
                    :required="false" helpertxt="Maximum 20 Characters" />
            </div>
            <div class="col-md-3 mt-3">
                <x-inputbox id="location" label="Location" type="text" placeholder="Enter Location" name="location"
                    :required="false" helpertxt="Maximum 100 Characters" />
            </div>
            <div class="col-md-6 mt-3">
                <label for="skills">Skills</label>
                <select id="skills" name="skills[]" class="form-control" multiple></select>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
        </div>
    </form>
</div>
@endsection

@section('style')
<style>
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none !important;
    }
</style>
@endsection

@section('script')
<script>
$(document).ready(function() {
    const employeeId = "{{ $id }}";

    // Initialize file uploader
    $('.input-images').imageUploader({
        multiple: false,
        imagesInputName: 'avatar_url',
        preloadedInputName: 'preloaded',
        label: 'Click to upload profile picture',
        preloaded: []
    });

    // Initialize datepicker
    $(".flatpickr").flatpickr({
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        allowInput: true
    });

    // Skills select2
    $("#skills").select2({
        tags: true,
        tokenSeparators: [','],
        placeholder: "Add Skills",
        width: '100%'
    });

    // Manager select2 with AJAX
    $('#manager_id').select2({
        placeholder: "Search & select Manager",
        allowClear: true,
        width: '100%',
        ajax: {
            url: "{{ route('employees.managerList') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || '',
                    exclude_id: employeeId
                };
            },
            processResults: function(response) {
                return {
                    results: response.data.map(item => ({
                        id: item.id,
                        text: item.full_name
                    }))
                };
            }
        }
    });

    // Fetch existing employee details
    $.get("{{ url('employees') }}/" + employeeId + "/Basicinfo/Details", function(res) {
        if(res.success) {
            let data = res.data || {};
            let primary = res.primary_details || {};

            $('#first_name').val(data.first_name || primary.first_name);
            $('#middle_name').val(data.middle_name || primary.middle_name);
            $('#last_name').val(data.last_name || primary.last_name);
            $('#designation').val(data.designation || primary.designation);
            $('#department').val(data.department || primary.department);
            $('#employment_type').val(data.employment_type || primary.employment_type);
            $('#salary_currency').val(data.salary_currency || primary.salary_currency);
            $('#base_salary').val(data.base_salary || primary.base_salary);
            $('#experience_years').val(data.experience_years || primary.experience_years);
            $('#gender').val(data.gender || primary.gender);

            if(data.dob) {
                document.querySelector("#dob")._flatpickr.setDate(data.dob, true);
            }

            // Prefill manager
            if(res.manager) {
                let option = new Option(res.manager.full_name, res.manager.id, true, true);
                $('#manager_id').append(option).trigger('change');
            }

            // Prefill skills
            if(data.skills) {
                let skills = Array.isArray(data.skills) ? data.skills : JSON.parse(data.skills);
                skills.forEach(skill => {
                    let option = new Option(skill, skill, true, true);
                    $('#skills').append(option);
                });
                $('#skills').trigger('change');
            }
        }
    });

    // Form submission
    $('#employeeForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "{{ url('employees') }}/" + employeeId + "/Basicinfo/Update",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                Swal.fire('Success', res.message, 'success');
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorList = '';
                    $.each(errors, function(key, msgs) {
                        msgs.forEach(msg => { errorList += msg + '\n'; });
                    });
                    Swal.fire('Validation Error', errorList, 'error');
                } else {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                }
            }
        });
    });
});
</script>
@endsection
