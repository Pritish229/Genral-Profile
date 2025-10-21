@extends('Admin.layout.app')

@section('title', 'Manage Business Medias')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Media"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Customers' => 'customers.List',
        'Customer Details' => ['customers.viewDetails', ['id' => $id]],
        'Business List' => ['customers.Businesslist', $id],
            'Business Details' => ['customers.BusinessDetails', ['id' => $id, 'business_id' => $business_id]],
            'Business Media' => ''
        ]" />

    <div class="mt-3">
        <h4 class="mb-3"><i class="fas fa-photo-video"></i> Business Media</h4>
    </div>

    <div class="row" id="mediaList"></div>
</div>

<!-- ==================== MODAL ==================== -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="mediaForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaModalLabel">Add Business Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="media_id" name="id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Media Usage <span class="text-danger">*</span></label>
                            <select class="form-select" id="media_usage" name="media_usage" required>
                                <option value="" disabled selected>-- Select Usage --</option>
                                @foreach(['logo','profile','banner','gallery','kyc','doc_scan','other'] as $opt)
                                <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name"
                                placeholder="e.g. Front-desk photo">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">File Name</label>
                            <input type="text" class="form-control" id="file_name_media" name="file_name"
                                placeholder="Optional custom name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload File <span class="text-danger" id="file_required">*</span></label>
                            <input type="file" class="form-control" id="file_url_media" name="file_url"
                                accept="image/jpeg,image/png,application/pdf">
                            <div class="form-text">Max 5 MB – JPG, PNG, PDF</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Caption</label>
                            <input type="text" class="form-control" id="caption" name="caption"
                                placeholder="Short description">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tags</label>
                            <select id="tags" name="tags[]" class="form-select" multiple></select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // -----------------------------------------------------------------
    // Global IDs – passed from the controller that renders the view
    // -----------------------------------------------------------------
    const customerId = "{{ $id }}";
    const businessId = "{{ $business_id }}";
    const baseUrl = "{{ url('/customers') }}";

    // -----------------------------------------------------------------
    // Select2 helper (re-initialised each time the modal opens)
    // -----------------------------------------------------------------
    const initSelect2 = () => {
        if ($('#tags').data('select2')) $('#tags').select2('destroy');
        $('#tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Add tags, comma separated",
            width: '100%',
            dropdownParent: $('#mediaModal')
        });
    };

    // -----------------------------------------------------------------
    // Load medias (GET)
    // -----------------------------------------------------------------
    const loadMedias = () => {
        const url = `${baseUrl}/${customerId}/${businessId}/media/business/List`;
        $.get(url, res => {
            let html = `<div class="col-12 d-flex justify-content-end mb-3">
                            <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#mediaModal" onclick="openAddModal()">
                                <i class="fas fa-plus"></i> Add Media
                            </button>
                        </div>`;

            if (!res.success || !res.data || res.data.length === 0) {
                html += `<div class="col-12 text-center py-5">
                            <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No media found for this business</h5>
                         </div>`;
            } else {
                res.data.forEach(m => {
                    const isImg = /\.(jpe?g|png|gif|webp)$/i.test(m.file_url);
                    const preview = isImg ?
                        `<img src="${m.file_url}" class="card-img-top media-thumbnail" alt="${m.file_name}">` :
                        `<div class="card-img-top media-thumbnail bg-light d-flex align-items-center justify-content-center">
                               <i class="fas fa-file-pdf fa-3x text-danger"></i>
                           </div>`;

                    html += `
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            ${preview}
                            <div class="card-body position-relative">
                                <h5 class="card-title text-capitalize">${m.media_usage ?? '-'}</h5>
                                <p class="small"><strong>Subject:</strong> ${m.subject_name ?? '-'}</p>
                                <p class="small"><strong>Caption:</strong> ${m.caption ?? '-'}</p>
                                <p class="small"><strong>Tags:</strong> ${(m.tags || []).join(', ')}</p>

                                <div class="btn-group w-100" role="group">
                                    <a href="${m.file_url}" target="_blank"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="${m.file_url}" download
                                       class="btn btn-sm btn-outline-success">Download</a>
                                </div>

                                <div class="dropdown position-absolute top-0 end-0 mt-2 me-2">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="dropdown-item editMedia" data-id="${m.id}">Edit</a></li>
                                        <li><a href="#" class="dropdown-item text-danger deleteMedia" data-id="${m.id}">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            }
            $('#mediaList').html(html);
        }).fail(() => Swal.fire('Error', 'Could not load media.', 'error'));
    };

    // -----------------------------------------------------------------
    // Modal open – add mode
    // -----------------------------------------------------------------
    const openAddModal = () => {
        $('#mediaModalLabel').text('Add Business Media');
        $('#mediaForm')[0].reset();
        $('#media_id').val('');
        $('#file_required').show();
        $('#file_url_media').val(''); // safe clear
        initSelect2();
    };

    // -----------------------------------------------------------------
    // Form submit (store / update)
    // -----------------------------------------------------------------
    $('#mediaForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const fd = new FormData(form);
        const mediaId = $('#media_id').val();

        // always send the business context
        fd.append('business_id', businessId);

        let url = `${baseUrl}/${customerId}/${businessId}/media/business`;
        if (mediaId) {
            url += `/${mediaId}`;
            fd.append('_method', 'PUT');
        } else {
            url += `/store`;
        }

        // file optional on edit
        if (mediaId && !$('#file_url_media')[0].files.length) fd.delete('file_url');

        $.ajax({
            url,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: res => {
                $('#mediaModal').modal('hide');
                loadMedias();
                Swal.fire('Success', res.message || 'Saved', 'success');
            },
            error: xhr => {
                let msg = 'Error saving media.';
                if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // -----------------------------------------------------------------
    // Edit click
    // -----------------------------------------------------------------
    $(document).on('click', '.editMedia', function(e) {
        e.preventDefault();
        const mediaId = $(this).data('id');
        const url = `${baseUrl}/${customerId}/${businessId}/media/business/${mediaId}`;

        $.get(url, res => {
            if (!res.success) return Swal.fire('Error', res.message || 'Not found', 'error');
            const m = res.data;

            $('#media_id').val(m.id);
            $('#media_usage').val(m.media_usage);
            $('#subject_name').val(m.subject_name);
            $('#file_name_media').val(m.file_name);
            $('#caption').val(m.caption);
            $('#tags').val(m.tags || []).trigger('change');
            $('#file_url_media').val(''); // never pre-fill a file input
            $('#file_required').hide(); // optional on edit
            $('#mediaModalLabel').text('Edit Business Media');
            $('#mediaModal').modal('show');
            initSelect2();
        }).fail(() => Swal.fire('Error', 'Could not fetch media.', 'error'));
    });

    // -----------------------------------------------------------------
    // Delete click
    // -----------------------------------------------------------------
    $(document).on('click', '.deleteMedia', function(e) {
        e.preventDefault();
        const mediaId = $(this).data('id');

        Swal.fire({
            title: 'Delete?',
            text: 'This media file will be removed permanently.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(result => {
            if (!result.isConfirmed) return;

            const url = `${baseUrl}/${customerId}/${businessId}/media/business/${mediaId}`;
            $.ajax({
                url,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: res => {
                    loadMedias();
                    Swal.fire('Deleted', res.message || 'Removed', 'success');
                },
                error: xhr => Swal.fire('Error', xhr.responseJSON?.message || 'Delete failed', 'error')
            });
        });
    });

    // -----------------------------------------------------------------
    // Init
    // -----------------------------------------------------------------
    $(document).ready(() => {
        initSelect2();
        loadMedias();

        // clean up when modal closes
        $('#mediaModal').on('hidden.bs.modal', () => {
            $('#mediaForm')[0].reset();
            $('#media_id').val('');
            $('#tags').val(null).trigger('change');
            $('#file_url_media').val('');
            $('#file_required').show();
            $('#mediaModalLabel').text('Add Business Media');
        });
    });
</script>
@endsection

@section('style')
<style>
    .media-thumbnail {
        height: 220px;
        object-fit: cover;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        background: #f8f9fa;
    }

    .card {
        position: relative;
    }

    .card-body p {
        margin-bottom: .4rem;
        font-size: .875rem;
    }

    .dropdown-menu {
        min-width: 120px;
    }
</style>
@endsection