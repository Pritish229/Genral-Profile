@extends('Admin.layout.app')

@section('title', 'Home | Students | Basic info')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Basic info"
        :links="['Home' => 'Admin.Dashboard', 'Students' => 'students.Studentlist', 'Basic info' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-1" id="student-details">
                Loading details...
            </div>
        </div>
    </div>

    <h5>Add More</h5>
    <hr style="color:#5156be">

    <!-- Progress bar -->
    <div class="progress mb-3" style="height: 25px;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="progress-bar">0%</div>
    </div>

    <form id="studentForm">
        <div class="row">
            <div class="col-md-6">
                <x-inputbox id="designation" label="Designation" type="text" placeholder="Enter Designation" name="designation"
                    value="{{ old('designation') }}" :required="false" helpertxt="Designation" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="department" label="Department" type="text" placeholder="Enter Department" name="department"
                    value="{{ old('department') }}" :required="false" helpertxt="Department" />
            </div>
            <div class="col-md-3">
                <div class="mb-2">
                    <label for="employment_type" class="mb-2 labeltxt">Employment Type</label>
                    <select name="employment_type" class="form-select" id="employment_type">
                        <option value="full_time">Full time</option>
                        <option value="part_time">Part time</option>
                        <option value="contract">Contract</option>
                        <option value="intern">Intern</option>
                        <option value="consultant">Consultant</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Employment Type</small>
                </div>
            </div>
            <div class="col-md-3">
                <x-inputbox id="salary_currency" label="Salary Currency" type="text" placeholder="Enter Salary Currency" name="salary_currency"
                    value="{{ old('salary_currency') }}" :required="false" helpertxt="Salary currency  Max 3 Digits" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="base_salary" label="Base Salary" type="number" placeholder="Enter Base Salary" name="base_salary"
                    value="{{ old('base_salary') }}" :required="false" helpertxt="Base Salary must be valid number" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="experience_years" label="Experience Years" type="text" placeholder="Enter Experience Years" name="experience_years"
                    value="{{ old('experience_years') }}" :required="false" helpertxt="Experience Years Must Be Number Eg: 2.3" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="emergency_contact_name" label="Emergency Contact Person " type="text" placeholder="Enter Emergency Contact Person" name="emergency_contact_name"
                    value="{{ old('emergency_contact_name') }}" :required="false" helpertxt="Maximum 70 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="emergency_relation" label="Emergency Contact Relation " type="text" placeholder="Enter Emergency Contact Relation" name="emergency_relation"
                    value="{{ old('emergency_relation') }}" :required="false" helpertxt="Maximum 70 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="emergency_contact_phone" label="Emergency Contact Number " type="text" placeholder="Enter Emergency Contact Number" name="emergency_contact_phone"
                    value="{{ old('emergency_contact_phone') }}" :required="false" helpertxt="Maximum 20 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="location" label="Location " type="text" placeholder="Enter Location" name="location"
                    value="{{ old('location') }}" :required="false" helpertxt="Maximum 100 Character" />
            </div>
            <div class="col-md-6">
                <label for="skills">Skills</label>
                <select id="skills" name="skills[]" class="form-control" multiple></select>
            </div>
            <div class="col-md-6">
                <label for="manager">Manager</label>
                <select id="manager_id" name="manager_id" class="form-control select2"></select>
            </div>

            <div class="col-md-6">
                <x-inputbox id="blood_group" label="Blood Group" type="text" placeholder="Enter Blood Group" name="blood_group"
                    value="{{ old('blood_group') }}" :required="false" helpertxt="Maximum 5 Character" />
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save & Continue</button>
            <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/employees') }}";
    let student_id = "{{ $id }}";

    // --- Progress Helpers ---
    function setProgress(value) {
        localStorage.setItem("studentProgress", value);
        $('#progress-bar').css('width', value + '%').text(value + '%');
    }

    function getProgress() {
        return parseInt(localStorage.getItem("studentProgress") || 0, 10);
    }

    // Restore progress bar on page load
    $(document).ready(function() {
        $('#progressContainer').show();
        setProgress(getProgress());

        fetchDetails();

        $("#studentForm").on("submit", function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we update the student profile.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                type: "POST",
                url: `${baseUrl}/${student_id}/Basicinfo/Update`,
                data: $(this).serialize(),
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Employee Profile has been updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            setProgress(20); // ✅ Step 2 milestone = 20%
                            window.location.href = `${baseUrl}/${student_id}/Address`;
                        });
                    } else {
                        Swal.fire("Failed", JSON.stringify(response.errors), "error");
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire("Error", "Something went wrong.", "error");
                    console.error(xhr.responseText);
                }
            });
        });

        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                setProgress(20);
                window.location.href = `${baseUrl}/${student_id}/Address`;
            });
        });
    });

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${student_id}/Basicinfo/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    console.log(response);

                    let imgSrc = `/storage/${response.data.avatar_url}`;
                    $("#student-details").html(`
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex: 0 0 150px;">
                                <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                            </div>
                            <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.employee_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Status:</strong> ${response.primary_details.status}</p>
                            </div>
                        </div>
                    `);
                } else {
                    $("#student-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#student-details").html(`<p class="text-danger">Something went wrong.</p>`);
                console.error(xhr.responseText);
            }
        });
    }

    // --- Select2 Setup ---
    $(document).ready(function() {
        $("#skills").select2({
            tags: true,
            tokenSeparators: [','], // only comma
            placeholder: "Add Skills",
            width: '100%',
            createTag: function(params) {
                let term = $.trim(params.term);
                if (term === '') {
                    return null;
                }
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        }).on("select2:close", function() {
            var input = $('.select2-search__field');
            if (input && input.val() !== '') {
                var newTag = input.val();
                $("#skills").append(new Option(newTag, newTag, true, true)).trigger('change');
                input.val('');
            }
        });

        // ✅ Manager dropdown with AJAX search
        $('#manager_id').select2({
            placeholder: "Search & select Manager",
            allowClear: true,
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: "{{ route('employees.managerList') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        exclude_id: "{{ $id }}" // ✅ pass current employee id
                    };
                },
                processResults: function(response) {
                    let items = response.data || [];
                    return {
                        results: items.map(item => ({
                            id: item.id,
                            text: item.full_name
                        }))
                    };
                }
            }
        });
    });
</script>
@endsection

@section('style')

<style>
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none !important;
    }
</style>

@endsection