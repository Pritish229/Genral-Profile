@extends('Admin.layout.app')

@section('title', 'Home | Student | Documents')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Documents"
        :links="['Home' => 'Admin.Dashboard', 'Students' => 'students.Studentlist' ,'Documents'=>'' ]" />

    <!-- Student Details -->
    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="student-details">
                Loading details...
            </div>
        </div>
    </div>

    <h5>Upload Student Documents</h5>
    <hr style="color:#5156be">

    <!-- Alert Box -->
    <div id="alert-box" class="mt-2"></div>

    <!-- Progress Bar -->
    <div class="progress mb-3" style="height: 25px;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width:70%;" id="progressBar">70%</div>
    </div>

    <!-- Document Form -->
    <form id="documentForm" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-6">
                <x-inputbox
                    id="document_type"
                    label="Document Type"
                    type="text"
                    placeholder="e.g., Passport, Aadhar Card"
                    name="document_type" />
            </div>
            <div class="col-md-6">
                <x-inputbox
                    id="document_number"
                    label="Document Number"
                    type="text"
                    placeholder="Enter Document Number"
                    name="document_number" />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="issue_date">Issue Date</label>
                <input type="text"
                    id="issue_date"
                    name="issue_date"
                    class="form-control flatpickr"
                    placeholder="Select issue date">
            </div>
            <div class="col-md-6">
                <label for="expiry_date">Expiry Date</label>
                <input type="text"
                    id="expiry_date"
                    name="expiry_date"
                    class="form-control flatpickr"
                    placeholder="Select expiry date">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <x-inputbox
                    id="file_name"
                    label="File Name"
                    type="text"
                    placeholder="e.g., Passport Scan"
                    name="file_name" />
            </div>
            <div class="col-md-6">
                <label for="file_url">Upload File</label>
                <input type="file" class="form-control" id="file_url" name="file_url">
                <small class="form-text text-muted">Upload scanned copy or PDF.</small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <x-textareabox
                    id="remarks"
                    label="Remarks"
                    name="remarks"
                    placeholder="Enter additional remarks about this document" />
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Document</button>
            <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/students') }}";
    let student_id = "{{ $id }}";

    // Fetch student basic info
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

    // Update Progress Bar
    function updateProgress(value) {
        $("#progressContainer").show();
        $("#progressBar").css("width", value + "%").text(value + "%");
    }

    $(document).ready(function() {
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true
        });

        fetchDetails();

        // Show initial progress
        updateProgress(70); // 70% = after Basic + Address

        // Handle Save
        $("#documentForm").on("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the document.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                type: "POST",
                url: `${baseUrl}/${student_id}/storeDocument`,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                         // Update progress to 90%
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Document saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            updateProgress(90); 
                            window.location.href = `${baseUrl}/${student_id}/Media`;
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let html = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(key, value) {
                            html += '<li>' + value[0] + '</li>';
                        });
                        html += '</ul></div>';
                        $("#alert-box").html(html);
                    } else {
                        Swal.fire("Error", "Something went wrong.", "error");
                        console.error(xhr.responseText);
                    }
                }
            });
        });

        // Handle Skip
        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `${baseUrl}/${student_id}/Media`;
            });
        });
    });
</script>
@endsection