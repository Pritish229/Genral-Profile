@extends('Admin.layout.app')

@section('title', 'Manage Individual Medias')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Individual Medias"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Vendors' => 'vendors.List',
            'Vendor Details' => ['vendors.viewDetails', $id],
            'Manage Individual Media' => ''
        ]" />

    <!-- Page Header -->
    <div class="mt-3">
        <h4 class="mb-3">
            <i class="fas fa-photo-video"></i>
            Individual Media
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
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaModalLabel">Add Individual Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="media_id" name="id">

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
    const vendorId = "{{ $id }}";
    const apiBase = "{{ url('/vendors') }}/" + vendorId + "/media/individual";

    $(document).ready(function() {
        // Initialize Select2 for tags
        $("#tags").select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Add tags (comma separated)",
            width: '100%',
            dropdownParent: $('#mediaModal')
        });

        loadMedias(); // Load individual medias

        // Reset modal on close
        $('#mediaModal').on('hidden.bs.modal', function() {
            $("#mediaForm")[0].reset();
            $("#media_id").val("");
            $("#tags").val(null).trigger('change');
            $("#file_required").show(); // Show required for file on add
            $("#mediaModalLabel").text("Add Individual Media");
        });
    });

    // Load medias for individual
    function loadMedias() {
        $.get(apiBase, function(res) {
            let html = '';

            // Add Media button (individuals only)
            html += `<div class="col-12 d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mediaModal" onclick="openAddModal()">
                            <i class="fas fa-plus"></i> Add Individual Media
                        </button>
                     </div>`;

            if (!res.success || !res.data || res.data.length === 0) {
                html += `<div class="col-12 text-center py-5">
                            <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No individual media available</h5>
                         </div>`;
            } else {
                res.data.forEach(media => {
                    const isImage = media.file_url.match(/\.(jpg|jpeg|png|gif)$/i);
                    const preview = isImage ? `<img src="${media.file_url}" class="card-img-top media-thumbnail" alt="${media.file_name ?? ''}">` : 
                                      `<div class="card-img-top media-thumbnail bg-light d-flex align-items-center justify-content-center">
                                           <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                       </div>`;
                    html += `
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            ${preview}
                            <div class="card-body">
                                <h5 class="card-title text-capitalize">${media.media_usage ?? '-'}</h5>
                                <p class="small"><strong>Subject:</strong> ${media.subject_name ?? '-'}</p>
                                <p class="small"><strong>Caption:</strong> ${media.caption ?? '-'}</p>
                                <p class="small"><strong>Tags:</strong> ${(media.tags || []).join(', ')}</p>
                                <div class="btn-group w-100" role="group">
                                    <a href="${media.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="${media.file_url}" download class="btn btn-sm btn-outline-success">Download</a>
                                </div>
                                <div class="dropdown float-end position-absolute top-0 end-0 mt-2 me-2">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="dropdown-item editMedia" data-id="${media.id}">Edit</a></li>
                                        <li><a href="#" class="dropdown-item text-danger deleteMedia" data-id="${media.id}">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            }

            $("#mediaList").html(html);
        }).fail(function(xhr) {
            Swal.fire("Error", "Failed to load medias.", "error");
        });
    }

    // Open add modal
    function openAddModal() {
        $("#mediaModalLabel").text("Add Individual Media");
        $("#media_id").val("");
        $("#file_required").show(); // File required for add
    }

    // Submit form
    $("#mediaForm").on("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const mediaId = $("#media_id").val();
        let url = apiBase;

        if (mediaId) {
            url += "/" + mediaId;
            formData.append("_method", "PUT");
        } else {
            url += "/store";
        }

        // If update, file is optional
        if (mediaId && $('#file_url_media')[0].files.length === 0) {
            formData.delete('file_url');
        }

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#mediaModal').modal('hide');
                loadMedias();
                Swal.fire("Success", res.message || "Media saved successfully!", "success");
            },
            error: function(xhr) {
                let errorMsg = "Error saving media.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                Swal.fire("Error", errorMsg, "error");
            }
        });
    });

    // Edit media
    $(document).on("click", ".editMedia", function(e) {
        e.preventDefault();
        const mediaId = $(this).data("id");
        $.get(apiBase + "/" + mediaId, function(res) {
            if (!res.success) {
                Swal.fire("Error", res.message || "Failed to fetch media.", "error");
                return;
            }
            const m = res.data;
            $("#media_id").val(m.id);
            $("#media_usage").val(m.media_usage);
            $("#subject_name").val(m.subject_name);
            $("#file_name_media").val(m.file_name);
            $("#caption").val(m.caption);
            $("#tags").val(m.tags || []).trigger('change');
            $("#file_required").hide(); // File optional on edit
            $("#mediaModalLabel").text("Edit Individual Media");
            $("#mediaModal").modal("show");
        }).fail(function(xhr) {
            Swal.fire("Error", "Failed to load media details.", "error");
        });
    });

    // Delete media
    $(document).on("click", ".deleteMedia", function(e) {
        e.preventDefault();
        const mediaId = $(this).data("id");
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
                    url: apiBase + "/" + mediaId,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        loadMedias();
                        Swal.fire("Deleted!", res.message || "Media has been deleted.", "success");
                    },
                    error: function(xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message || "Unable to delete media.", "error");
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
        width: 100%;
        object-fit: cover;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        background-color: #f8f9fa;
    }
    .dropdown-menu {
        min-width: 120px;
    }
    .card {
        position: relative;
    }
    .card-body p {
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection