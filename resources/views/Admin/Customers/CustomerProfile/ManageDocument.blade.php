@extends('Admin.layout.app')

@section('title', 'Manage Document')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Documents"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Students' => 'students.Studentlist',
            'Student Detail' => ['students.Studentlist.studentDetailsPage', $id],
            'Manage Documents' => ''
        ]" />

    <!-- Cards container -->
    <div class="row p-3 " id="documentList"></div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="documentForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Add Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id" id="doc_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-inputbox id="document_type" label="Document Type" type="text" name="document_type"
                                placeholder="e.g., Passport, Aadhar Card" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="document_number" label="Document Number" type="text" name="document_number"
                                placeholder="Enter Document Number" />
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
                                placeholder="e.g., Passport Scan" />
                        </div>
                        <div class="col-md-6">
                            <label for="file_url">Upload File</label>
                            <input type="file" class="form-control" id="file_url" name="file_url">
                            <small class="form-text text-muted">Upload scanned copy or PDF.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-textareabox id="remarks" label="Remarks" name="remarks"
                                placeholder="Enter additional remarks about this document" />
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
    $(".flatpickr").flatpickr({
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        allowInput: true
    });

    let studentId = "{{ $id }}";

    // Fetch and render documents
    function loadDocuments() {
        $.get("{{ url('/students') }}/" + studentId + "/documents", function(res) {
            let html = '';

            if (res.data.length === 0) {
                // No docs → show "Create New" only
                html = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No documents are here</h5>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#documentModal">
                        <i class="fas fa-plus"></i> Create New
                    </button>
                </div>`;
            } else {
                // Show "Add Document" button on top
                html = `
                <div class="col-12 d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal" id="addDocumentBtn">
                        <i class="fas fa-plus"></i> Add Document
                    </button>
                </div>`;

                res.data.forEach(doc => {
                    let fileUrl = "{{ url('storage') }}/" + doc.file_url;
                    let preview = '';

                    if (doc.file_url) {
                        let ext = doc.file_url.split('.').pop().toLowerCase();
                        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                            preview = `<img src="${fileUrl}" class="img-fluid rounded mb-2" style="max-height:120px;object-fit:cover;">`;
                        } else if (ext === 'pdf') {
                            preview = `<i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>`;
                        } else {
                            preview = `<i class="fas fa-file-alt fa-3x text-secondary mb-2"></i>`;
                        }
                    }

                    // Friendly download filename
                    let downloadName = (doc.file_name ? doc.file_name.replace(/\s+/g, "_") : "document_" + doc.id);

                    html += `
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                ${preview}
                                <h5 class="card-title">${doc.document_type}</h5>
                                <p class="mb-1"><strong>Number:</strong> ${doc.document_number}</p>
                                <p class="mb-1"><strong>Issue:</strong> ${doc.issue_date ?? '-'}</p>
                                <p class="mb-1"><strong>Expiry:</strong> ${doc.expiry_date ?? '-'}</p>
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

            $("#documentList").html(html);
        });
    }


    // Add/Edit document
    $("#documentForm").on("submit", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let docId = $("#doc_id").val();
        let url, method;

        if (docId) {
            url = "{{ url('/students') }}/" + studentId + "/documents/" + docId;
            method = "POST";
            formData.append("_method", "PUT");
        } else {
            url = "{{ url('/students') }}/" + studentId + "/storeDocument";
            method = "POST";
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $("#documentModal").modal('hide');
                loadDocuments();
                $("#documentForm")[0].reset();
                $("#doc_id").val("");
                $("#documentModalLabel").text("Add Document");

                Swal.fire({
                    icon: "success",
                    title: "Saved!",
                    text: "Document has been saved successfully",
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(err) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Error saving document"
                });
            }
        });
    });

    // Edit document
    $(document).on("click", ".editDoc", function() {
        let id = $(this).data("id");
        $.get("{{ url('/students') }}/" + studentId + "/documents/" + id, function(res) {
            let d = res.data;

            $("#doc_id").val(d.id);
            $("#document_type").val(d.document_type);
            $("#document_number").val(d.document_number);

            if (d.issue_date) {
                document.querySelector("#issue_date")._flatpickr.setDate(d.issue_date, true, "Y-m-d");
            } else {
                document.querySelector("#issue_date")._flatpickr.clear();
            }

            if (d.expiry_date) {
                document.querySelector("#expiry_date")._flatpickr.setDate(d.expiry_date, true, "Y-m-d");
            } else {
                document.querySelector("#expiry_date")._flatpickr.clear();
            }

            $("#file_name").val(d.file_name);
            $("#remarks").val(d.remarks);

            $("#documentModalLabel").text("Edit Document");
            $("#documentModal").modal("show");
        });
    });

    // Delete document
    $(document).on("click", ".deleteDoc", function() {
        let id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This document will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/students') }}/" + studentId + "/documents/" + id,
                    type: "DELETE",
                    success: function() {
                        loadDocuments();
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: "Document has been deleted.",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Delete failed"
                        });
                    }
                });
            }
        });
    });

    // Initial load
    loadDocuments();
</script>
@endsection
