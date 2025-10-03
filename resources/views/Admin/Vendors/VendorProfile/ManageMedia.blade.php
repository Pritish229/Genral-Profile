@extends('Admin.layout.app')

@section('title', 'Manage Medias')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Medias"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Vendors' => 'vendors.List',
            'Vendor Detail' => ['vendors.viewDetails', $id],
            'Manage Media' => ''
        ]" />

    <!-- Page Header -->
    <div class="mt-3">
        <h4 class="mb-3">
            <i class="fas fa-photo-video"></i>
            <span id="mediaTypeTitle">{{ $type === 'individual' ? 'Personal' : 'Business' }} Media</span>
        </h4>
    </div>

    <!-- Media List -->
    <div class="row" id="mediaList"></div>
</div>

<!-- Media Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="mediaForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaModalLabel">Add Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="media_id">
                    <input type="hidden" name="profile_type" id="profile_type">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="media_usage">Media Usage</label>
                            <select class="form-select" id="media_usage" name="media_usage" required>
                                <option value="" disabled selected>-- Select Usage --</option>
                                <option value="logo">Logo</option>
                                <option value="profile">Profile</option>
                                <option value="banner">Banner</option>
                                <option value="gallery">Gallery</option>
                                <option value="kyc">KYC</option>
                                <option value="doc_scan">Document Scan</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="subject_name" label="Subject Name" type="text" name="subject_name" placeholder="Enter custom purpose" value="" helpertxt="" :required="true" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-inputbox id="file_name_media" label="File Name" type="text" name="file_name" placeholder="Original file name" value="" helpertxt="" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <label for="file_url_media">Upload File</label>
                            <input type="file" class="form-control" id="file_url_media" name="file_url">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-inputbox id="caption" label="Caption" type="text" name="caption" placeholder="Short description" value="" helpertxt="" :required="false" />
                        </div>
                        <div class="col-md-6">
                            <label for="tags">Tags</label>
                            <select id="tags" name="tags[]" class="form-control" multiple></select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let vendorId = "{{ $id }}";
    let vendorType = "{{ $type }}"; // Get type from backend

    $(document).ready(function() {
        // Initialize Select2
        $("#tags").select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Add tags",
            width: '100%'
        });

        loadMedias();

        // Reset modal on close
        $('#mediaModal').on('hidden.bs.modal', function() {
            $("#mediaForm")[0].reset();
            $("#media_id").val("");
            $("#profile_type").val("");
            $("#tags").val(null).trigger('change');
            let mediaTypeLabel = vendorType === 'individual' ? 'Personal' : 'Business';
            $("#mediaModalLabel").text("Add " + mediaTypeLabel + " Media");
        });
    });

    // Load all media files
    function loadMedias() {
        $.get("{{ url('/vendors') }}/" + vendorId + "/" + vendorType + "/medias", function(res) {
            let html = '';

            // Always show Add Media button
            html += `<div class="col-12 d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mediaModal" id="addMediaBtn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Media
                    </button>
                 </div>`;

            if (!res.data || res.data.length === 0) {
                html += `<div class="col-12 text-center py-5">
                        <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No media available</h5>
                     </div>`;
            } else {
                res.data.forEach(media => {
                    html += `
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <img src="${media.file_url}" class="card-img-top media-thumbnail"  alt="${media.file_name ?? ''}">
                        <div class="card-body">
                            <h5 class="card-title">${media.media_usage ?? '-'}</h5>
                            <p><strong>Caption:</strong> ${media.caption ?? '-'}</p>
                            <p><strong>Tags:</strong> ${(media.tags || []).join(', ')}</p>
                            <a href="${media.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="${media.file_url}" download class="btn btn-sm btn-outline-success">Download</a>
                            <div class="dropdown float-end">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="dropdown-item editMedia" data-id="${media.id}">Edit</a></li>
                                    <li><a href="#" class="dropdown-item deleteMedia" data-id="${media.id}">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>`;
                });
            }

            $("#mediaList").html(html);
        });
    }

    // Open add modal with current profile type
    function openAddModal() {
        $("#profile_type").val(vendorType);
        let mediaTypeLabel = vendorType === 'individual' ? 'Personal' : 'Business';
        $("#mediaModalLabel").text("Add " + mediaTypeLabel + " Media");
        $("#media_id").val("");
    }

    // Add / Update Media
    $("#mediaForm").on("submit", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let mediaId = $("#media_id").val();
        let url, method;

        if (mediaId) {
            url = `{{ url('/vendors') }}/${vendorId}/${vendorType}/medias/${mediaId}`;
            method = "POST";
            formData.append("_method", "PUT");
        } else {
            url = `{{ url('/vendors') }}/${vendorId}/storeMedia`;
            method = "POST";
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function() {
                $("#mediaModal").modal('hide');
                loadMedias();
                Swal.fire("Success", "Media saved successfully!", "success");
            },
            error: function(err) {
                Swal.fire("Error", "Error saving media. Please check your inputs.", "error");
            }
        });
    });

    // Edit media
    $(document).on("click", ".editMedia", function() {
        let mediaId = $(this).data("id");
        $.get(`{{ url('/vendors') }}/${vendorId}/${vendorType}/medias/${mediaId}`, function(res) {
            console.log(res);

            let m = res.data;
            $("#media_id").val(m.id);
            $("#media_usage").val(m.media_usage);
            $("#subject_name").val(m.subject_name);
            $("#file_name_media").val(m.file_name);
            $("#caption").val(m.caption);
            $("#tags").val(m.tags || []).trigger("change");
            $("#profile_type").val(m.profile_type);
            let mediaTypeLabel = vendorType === 'individual' ? 'Personal' : 'Business';
            $("#mediaModalLabel").text("Edit " + mediaTypeLabel + " Media");
            $("#mediaModal").modal("show");
        });
    });

    // Delete media
    $(document).on("click", ".deleteMedia", function() {
        let mediaId = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This media will be permanently deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/vendors') }}/${vendorId}/${vendorType}/medias/${mediaId}`,
                    type: "DELETE",
                    success: function() {
                        loadMedias();
                        Swal.fire("Deleted!", "Media has been deleted.", "success");
                    },
                    error: function() {
                        Swal.fire("Error", "Unable to delete media.", "error");
                    }
                });
            }
        });
    });
</script>
@endsection

@section('style')
<style>
    .media-thumbnail {
        height: 220px;
        /* fixed height for all previews */
        width: 100%;
        /* take full card width */
        object-fit: cover;
        /* crop instead of stretch */
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
</style>
@endsection