@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Media')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Media"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List' ,'Media'=>'' ]" />

    <!-- vendor Details -->
    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="vendor-details">
                Loading details...
            </div>
        </div>
    </div>

    <h5>Upload Vendor Media</h5>
    <hr style="color:#5156be">

    <!-- Alert Box -->
    <div id="alert-box" class="mt-2"></div>

    <!-- Progress Bar -->
    <div class="progress mb-3" style="height: 25px;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width:80%;" id="progressBar">80%</div>
    </div>

    <!-- Document Form -->
    <form id="documentForm" enctype="multipart/form-data">
        @csrf
        <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="media_usage">Media Usage <span class="text-danger">*</span></label>
                            <select class="form-select" id="media_usage" name="media_usage" required>
                                <option value="" disabled selected>-- Select Usage --</option>
                                <!-- <option value="logo">Logo</option> -->
                                <option value="profile">Profile</option>
                                <option value="banner">Banner</option>
                                <option value="gallery">Gallery</option>
                                <option value="kyc">KYC</option>
                                <option value="doc_scan">Document Scan</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="subject_name">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter custom purpose">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="file_name_media">File Name</label>
                            <input type="text" class="form-control" id="file_name_media" name="file_name" placeholder="Original file name">
                        </div>
                        <div class="col-md-6">
                            <label for="file_url_media">Upload File <span class="text-danger" id="file_required">*</span></label>
                            <input type="file" class="form-control" id="file_url_media" name="file_url" accept="image/jpeg,image/png,application/pdf">
                            <small class="text-muted">Max size: 5MB. Formats: JPG, PNG, PDF</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="caption">Caption</label>
                            <input type="text" class="form-control" id="caption" name="caption" placeholder="Short description">
                        </div>
                        <div class="col-md-6">
                            <label for="tags">Tags</label>
                            <select id="tags" name="tags[]" class="form-select" multiple></select>
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
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let imgSrc = `storage/${response.data.avatar_url}`;
                    let manageBankUrl = `/vendors/${response.data.id}/manageBank`;
                    let manageDocUrl = `/vendors/${response.data.id}/manageDocument`;
                    let managemediaUrl = `/vendors/${response.data.id}/Media/manage`;

                    $("#vendor-details").html(`
                <div class="d-flex align-items-start justify-content-between">
                    <!-- Profile + Info -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 160px;">
                            <img src="{{asset('${imgSrc}')}}" class="img-thumbnail w-100" alt="Profile picture">
                        </div>
                        <div class="flex-grow-1">
                                <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.vendor_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Gender:</strong> ${response.data.gender}</p>
                                <p><strong>Occupation:</strong> ${response.data.occupation}</p>
                                <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                            </div>
                        </div>
                    </div>

                    
            `);

                } else {
                    $("#vendor-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#vendor-details").html(`<p class="text-danger">Something went wrong.</p>`);
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
        $("#tags").select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Add tags (comma separated)",
            width: '100%'
            // Removed dropdownParent since no #mediaModal exists on this page
        });

        fetchDetails();

        // Show initial progress
        updateProgress(80); // Set to 80% on load

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
                url: `${baseUrl}/${vendor_id}/media/individual/store`,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        // Update progress to 90% on success
                        updateProgress(90);
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Document saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${vendor_id}/individual/OnlineProfile`;
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

            updateProgress(90);
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `${baseUrl}/${vendor_id}/individual/OnlineProfile`;
            });
        });
    });
</script>
@endsection