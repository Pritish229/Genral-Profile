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
                <x-inputbox id="guardian_name" label="Guardian Name" type="text" placeholder="Enter Guardian Name" name="guardian_name"
                    value="{{ old('guardian_name') }}" :required="false" helpertxt="Guardian Name" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="guardian_relation" label="Guardian Relation" type="text" placeholder="Enter Guardian Relation" name="guardian_relation"
                    value="{{ old('guardian_relation') }}" :required="false" helpertxt="Guardian Relation" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="guardian_occupation" label="Guardian Occupation" type="text" placeholder="Enter Guardian Occupation" name="guardian_occupation"
                    value="{{ old('guardian_occupation') }}" :required="false" helpertxt="Guardian Occupation" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="guardian_phone" label="Guardian Phone no" type="text" placeholder="Enter Guardian Phone no" name="guardian_phone"
                    value="{{ old('guardian_phone') }}" :required="false" helpertxt="Phone number must be 10 Digits" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="guardian_email" label="Guardian Email" type="email" placeholder="Enter Guardian Email" name="guardian_email"
                    value="{{ old('guardian_email') }}" :required="false" helpertxt="Guardian email must be valid email" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="parent_income" label="Annual income" type="text" placeholder="Enter Annual income" name="parent_income"
                    value="{{ old('parent_income') }}" :required="false" helpertxt="Annual income Must Be Number" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="nationality" label="Nationality " type="text" placeholder="Enter Nationality" name="nationality"
                    value="{{ old('nationality') }}" :required="false" helpertxt="Maximum 70 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="mother_tongue" label="Mother Tongue" type="text" placeholder="Enter Mother Tongue" name="mother_tongue"
                    value="{{ old('mother_tongue') }}" :required="false" helpertxt="Maximum 70 Character" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="blood_group" label="Blood Group" type="text" placeholder="Enter Blood Group" name="blood_group"
                    value="{{ old('blood_group') }}" :required="false" helpertxt="Maximum 5 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="current_class" label="Current Class" type="text" placeholder="Enter Current Class" name="current_class"
                    value="{{ old('current_class') }}" :required="false" helpertxt="Maximum 40 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="section" label="Section" type="text" placeholder="Enter section " name="section"
                    value="{{ old('section') }}" :required="false" helpertxt="Maximum 10 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="rollno" label="Rollno" type="text" placeholder="Enter Rollno" name="rollno"
                    value="{{ old('rollno') }}" :required="false" helpertxt="Maximum 30 Character" />
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
    let baseUrl = "{{ url('/students') }}";
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
                            text: 'Student profile has been updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // ✅ Step 2 milestone = 20%
                            setProgress(20);
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
                // ✅ Still count this step as completed
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
                    let imgSrc = `/storage/${response.data.avatar_url}`;
                    $("#student-details").html(`
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex: 0 0 150px;">
                                <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                            </div>
                            <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.student_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Gender:</strong> ${response.data.gender}</p>
                                <p><strong>Caste:</strong> ${response.data.caste}</p>
                                <p><strong>Religion:</strong> ${response.data.religion}</p>
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
</script>
@endsection