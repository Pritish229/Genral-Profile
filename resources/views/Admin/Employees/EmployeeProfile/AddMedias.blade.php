@extends('Admin.layout.app')

@section('title', 'Home | employees | Documents & Media')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Documents & Media"
        :links="['Home' => 'Admin.Dashboard', 'employees' => 'employees.employeelist' ,'Documents & Media'=>'' ]" />

    <!-- employee Details -->
    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="employee-details">
                Loading details...
            </div>
        </div>
    </div>

    <!-- Alert Box -->
    <div id="alert-box" class="mt-2"></div>

    <!-- Progress Bar -->
    <div class="progress mb-3" style="height: 25px; display:none;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width:0%;" id="progressBar">0%</div>
    </div>

    <!-- Media Form -->
    <h5 class="mt-4">Upload employee Media</h5>
    <hr style="color:#5156be">
    <form id="mediaForm" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="media_usage">Media Usage</label>
                <select class="form-select" id="media_usage" name="media_usage" required>
                    <option value="" disabled selected>-- Select Usage --</option>
                    <option value="profile">Profile</option>
                    <option value="banner">Banner</option>
                    <option value="gallery">Gallery</option>
                    <option value="kyc">KYC</option>
                    <option value="doc_scan">Document Scan</option>
                    <option value="other">Other</option>
                </select>
                <small class="form-text text-muted">Purpose of the media file.</small>
            </div>
            <div class="col-md-6" id="subject_name_wrapper">
                <x-inputbox id="subject_name" label="Subject Name" type="text" placeholder="Enter custom purpose" name="subject_name" />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <x-inputbox id="file_name_media" label="File Name" type="text" placeholder="Original file name" name="file_name_media" />
            </div>
            <div class="col-md-6">
                <label for="file_url_media">Upload File</label>
                <input type="file" class="form-control" id="file_url_media" name="file_url" required>
                <small class="form-text text-muted">Upload media (image, pdf, etc.).</small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <x-inputbox id="caption" label="Caption" type="text" placeholder="Short description" name="caption" />
            </div>
            <div class="col-md-6">
                <label for="tags">Tags</label>
                <select id="tags" name="tags[]" class="js-example-basic-single js-states form-control" multiple></select>
                <small class="form-text text-muted">Add labels (press Enter to create new).</small>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Media & Finish</button>
            <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
        </div>
    </form>

</div>
@endsection
@section('script')
<script>
    function updateProgress(percent) {
        $("#progressContainer").show();
        $("#progressBar").css("width", percent + "%").text(percent + "%");
    }

    $(document).ready(function() {
        let baseUrl = "{{ url('/employees') }}";
        let employee_id = "{{ $id }}";

        $("#tags").select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Add tags",
            width: '100%'
        });

        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true
        });

        // Fetch employee details
        

        function fetchDetails() {
            $.ajax({
                type: "GET",
                url: `${baseUrl}/${employee_id}/Basicinfo/Details`,
                dataType: "json",
                success: function(response) {
                    if (response.success) {

                        let imgSrc = `/storage/${response.data.avatar_url}`;
                        $("#employee-details").html(`
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
                        $("#employee-details").html(`<p class="text-danger">${response.errors}</p>`);
                    }
                },
                error: function(xhr) {
                    $("#employee-details").html(`<p class="text-danger">Something went wrong.</p>`);
                    console.error(xhr.responseText);
                }
            });
        }

        fetchDetails();
        updateProgress(80);

        // Document Upload
        $("#documentForm").on("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            updateProgress(0);

            $.ajax({
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            let percentComplete = Math.round((evt.loaded / evt.total) * 50); // 0-50%
                            updateProgress(percentComplete);
                        }
                    }, false);
                    return xhr;
                },
                type: "POST",
                url: `${baseUrl}/${employee_id}/storeDocument`,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        updateProgress(50);
                        Swal.fire({
                            icon: 'success',
                            title: 'Document Saved!',
                            text: 'Document uploaded successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
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
                    }
                }
            });
        });

        // Media Upload
        $("#mediaForm").on("submit", function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            let percentComplete = 50 + Math.round((evt.loaded / evt.total) * 50); // 50-100%
                            updateProgress(percentComplete);
                        }
                    }, false);
                    return xhr;
                },
                type: "POST",
                url: "{{ route('employees.Bank.storeMedia', ['id' => $id]) }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        updateProgress(100);
                        Swal.fire({
                            icon: 'success',
                            title: 'All Steps Completed!',
                            text: 'employee document & media uploaded successfully.',
                            timer: 1500,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = "{{ route('employees.create') }}";
                        });
                    }
                },
                error: function(xhr) {
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
                    }
                }
            });
        });

        // Skip Button
        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped!',
                text: 'You have skipped this step.',
                timer: 1200,
                showConfirmButton: false,
                allowOutsideClick: false
            }).then(() => {
                window.location.href = "{{ route('employees.create') }}";
            });
        });
    });
</script>
@endsection