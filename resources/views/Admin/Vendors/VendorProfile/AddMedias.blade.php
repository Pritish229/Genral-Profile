@extends('Admin.layout.app')

@section('title', 'Manage Business Documents')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Documents"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Vendors' => 'vendors.List',
            'Vendor Detail' => ['vendors.viewDetails', ['id' => $id]],
            'Business Detail' => ['vendors.BusinessDetails', ['id' => $id, 'business_id' => $business_id]],
            'Business Documents' => ''
        ]" />

    <!-- Page Header -->
    <div class="mt-3">
        <h4 class="mb-3">
            <i class="fas fa-file-alt"></i>
            Business Documents
        </h4>
    </div>

    <!-- Cards container -->
    <div class="row p-3" id="documentList"></div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="documentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Add Business Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id" id="doc_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-inputbox id="document_type" label="Document Type" type="text" name="document_type"
                                placeholder="e.g., Passport, Aadhar Card" value="" helpertxt="" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="document_number" label="Document Number" type="text" name="document_number"
                                placeholder="Enter Document Number" value="" helpertxt="" :required="true" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="issue_date">Issue Date</label>
                            <input type="text" id="issue_date" name="issue_date" class="form-control flatpickr"
                                placeholder="Select issue date">
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="text" id="expiry_date" name="expiry_date" class="form-control flatpickr"
                                placeholder="Select expiry date">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-inputbox id="file_name" label="File Name" type="text" name="file_name"
                                placeholder="e.g., Passport Scan" value="" helpertxt="" :required="false" />
                        </div>
                        <div class="col-md-6">
                            <label for="file_url">Upload File</label>
                            <input type="file" class="form-control" id="file_url" name="file_url">
                            <small class="form-text text-muted">Upload scanned copy or PDF ( accepted formats: jpg, jpeg, png, pdf; max 5MB).</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-textareabox id="remarks" label="Remarks" name="remarks"
                                placeholder="Enter additional remarks about this document" value="" helpertxt="" :required="false" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-inputbox id="issuing_authority" label="Issuing Authority" type="text" name="issuing_authority"
                                placeholder="e.g., Government of India" value="" helpertxt="" :required="false" />
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
    // Initialize flatpickr
    $(".flatpickr").flatpickr({
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F vendorId F Y",
        allowInput: true
    });

    let vendorId = "{{ $id }}";
    let businessId = "{{ $business_id }}";
    let apiSubpath = 'business';
    let baseUrl = "{{ url('/vendors') }}";

    // Fetch and render documents
    function loadDocuments() {
        let url = `${baseUrl}/${vendorId}/documents/${apiSubpath}/${businessId}`;

        $.get(url, function(res) {
            let html = '';

            if (res.data.length === 0) {
                html = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No business documents found</h5>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#documentModal" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Document
                    </button>
                </div>`;
            } else {
                html = `
                <div class="col-12 d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Document
                    </button>
                </div>`;

                res.data.forEach(doc => {
                    let fileUrl = doc.file_url; // Full URL from backend
                    let preview = '';
                    if (doc.file_url) {
                        let ext = doc.file_url.split('.').pop().toLowerCase().split('?')[0];
                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                            preview = `<img src="${fileUrl}" class="img-fluid rounded mb-2" style="max-height:120px;object-fit:cover;">`;
                        } else if (ext === 'pdf') {
                            preview = `<i class="fas fa-file-pdf fa-3x text-danger mb-2"></i><p class="small">PDF Document</p>`;
                        } else {
                            preview = `<i class="fas fa-file-alt fa-3x text-secondary mb-2"></i>`;
                        }
                    }

                    let downloadName = (doc.file_name ? doc.file_name.replace(/\s+/g, '_') : `document_${doc.id}`);
                    let issueDate = doc.issue_date || '-';
                    let expiryDate = doc.expiry_date || '-';

                    html += `
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                ${preview}
                                <h5 class="card-title">${doc.document_type}</h5>
                                <p class="mb-1"><strong>Number:</strong> ${doc.document_number}</p>
                                <p class="mb-1"><strong>Issue:</strong> ${issueDate}</p>
                                <p class="mb-1"><strong>Expiry:</strong> ${expiryDate}</p>
                                <p class="mb-1"><strong>Authority:</strong> ${doc.issuing_authority || '-'}</p>
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="${fileUrl}" download="${downloadName}" class="btn btn-sm btn-outline-success">Download</a>
                                <div class="dropdown float-end">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="dropdown-item editDoc" data-id="${doc.id}">Edit</a></li>
                                        <li><a href="#" class="dropdown-item deleteDoc" data-id="${doc.id}">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            }

            $('#documentList').html(html);
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load documents.'
            });
        });
    }

    // Open add modal
    function openAddModal() {
        $('#documentForm')[0].reset();
        $('.flatpickr').each(function() {
            this._flatpickr.clear();
        });
        $('#doc_id').val('');
        $('#documentModalLabel').text('Add Business Document');
        $('#save-btn').text('Save');
    }

    // Submit form (add/edit)
    $('#documentForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let docId = $('#doc_id').val();
        let url = `${baseUrl}/${vendorId}/${businessId}/documents/${apiSubpath}/store`;
        let method = 'POST';

        if (docId) {
            url = `${baseUrl}/${vendorId}/documents/${apiSubpath}/${businessId}/${docId}`;
            formData.append('_method', 'PUT');
            method = 'POST'; // Laravel uses POST with _method for PUT
        }

        $.ajax({
            url,
            method,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#documentModal').modal('hide');
                loadDocuments();
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message || 'Document saved successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(err) {
                let errorMsg = 'Error saving document';
                if (err.responseJSON && err.responseJSON.message) {
                    errorMsg = err.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    });

    // Edit document
    $(document).on('click', '.editDoc', function() {
        let id = $(this).data('id');
        let url = `${baseUrl}/${vendorId}/documents/${apiSubpath}/${businessId}/${id}`;
        $.get(url, function(res) {
            let d = res.data;

            $('#doc_id').val(d.id);
            $('#document_type').val(d.document_type);
            $('#document_number').val(d.document_number);
            $('#file_name').val(d.file_name);
            $('#remarks').val(d.remarks);
            $('#issuing_authority').val(d.issuing_authority);

            if (d.issue_date_raw) {
                document.querySelector('#issue_date')._flatpickr.setDate(d.issue_date_raw, true, 'Y-m-d');
            } else {
                document.querySelector('#issue_date')._flatpickr.clear();
            }

            if (d.expiry_date_raw) {
                document.querySelector('#expiry_date')._flatpickr.setDate(d.expiry_date_raw, true, 'Y-m-d');
            } else {
                document.querySelector('#expiry_date')._flatpickr.clear();
            }

            $('#documentModalLabel').text('Edit Business Document');
            $('#save-btn').text('Update');
            $('#documentModal').modal('show');
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load document details.'
            });
        });
    });

    // Delete document
    $(document).on('click', '.deleteDoc', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This document will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = `${baseUrl}/${vendorId}/documents/${apiSubpath}/${businessId}/${id}`;
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(res) {
                        loadDocuments();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message || 'Document deleted.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Delete failed'
                        });
                    }
                });
            }
        });
    });

    // Initial load
    $(document).ready(function() {
        loadDocuments();
    });
</script>
@endsection