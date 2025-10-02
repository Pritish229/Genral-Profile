@extends('Admin.layout.app')

@section('title', 'Home | Manage Contact')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Contact"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
        'Manage Contact' => ''
    ]" />

    <!-- Profile Type Selection -->
    <div class="mb-3">
        <h5>Profile Type</h5>
        <div class="btn-group" role="group" aria-label="Profile Type">
            <input type="radio" class="btn-check" name="profile_type" id="individual" value="individual" checked>
            <label class="btn btn-outline-primary" for="individual">Individual</label>

            <input type="radio" class="btn-check" name="profile_type" id="business" value="business">
            <label class="btn btn-outline-primary" for="business">Business</label>
        </div>
    </div>

    <!-- Contact Form -->
    <form id="vendorContactForm">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <div class="mb-2">
                    <label for="contact_type" class="mb-2 labeltxt">Contact Type</label>
                    <select name="contact_type" class="form-select" id="contact_type" required>
                        <option value="">Select Contact Type</option>
                        <option value="phone">Phone</option>
                        <option value="email">Email</option>
                        <option value="mobile">Mobile</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="telegram">Telegram</option>
                        <option value="skype">Skype</option>
                        <option value="other">Other</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Contact Type</small>
                </div>
            </div>
            <div class="col-md-3">
                <x-inputbox id="value" label="Contact Value" type="text" placeholder="Enter contact value" name="value"
                    value="{{ old('value') }}" :required="true" helpertxt="Phone number, email, etc." />
            </div>
            <div class="col-md-3">
                <x-inputbox id="country_code" label="Country Code" type="text" placeholder="Enter country code" name="country_code"
                    value="{{ old('country_code') }}" :required="false" helpertxt="Ex: +91, +1" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Ex: Personal, Work" />
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                    <label class="form-check-label" for="is_primary">
                        Set as Primary Contact
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_emergency" name="is_emergency" value="1">
                    <label class="form-check-label" for="is_emergency">
                        Emergency Contact
                    </label>
                </div>
            </div>
            <div class="col-lg-12 mt-2">
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                <button type="button" class="btn btn-secondary" id="cancel-btn" style="display: none;">Cancel</button>
            </div>
        </div>
    </form>

    <!-- Contact Table -->
    <div class="mt-4">
        <table class="table table-bordered" id="contactsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Country Code</th>
                    <th>Label</th>
                    <th>Primary</th>
                    <th>Emergency</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filled dynamically with JS -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('vendors') }}";
    let vendor_id = "{{ $id }}";
    let edit_id = null; // track contact being edited
    let currentProfileType = 'individual'; // track current profile type

    // Fetch & render contacts
    function loadContacts() {
        $.get(`${baseUrl}/${vendor_id}/${currentProfileType}/Get/Contacts`, function(res) {
            if (res.success) {
                let rows = "";
                let index = 1;
                res.data.forEach(contact => {
                    rows += `
                        <tr data-id="${contact.id}">
                            <td>${index++}</td>
                            <td>${contact.contact_type}</td>
                            <td>${contact.value}</td>
                            <td>${contact.country_code ?? '-'}</td>
                            <td>${contact.label ?? '-'}</td>
                            <td>${contact.is_primary ? 'Yes' : 'No'}</td>
                            <td>${contact.is_emergency ? 'Yes' : 'No'}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editBtn">Edit</button>
                                ${contact.is_primary ? '' : `<button class="btn btn-sm btn-danger deleteBtn">Delete</button>`}
                            </td>
                        </tr>`;
                });
                $("#contactsTable tbody").html(rows);
            } else {
                $("#contactsTable tbody").html('<tr><td colspan="8" class="text-center">No contacts found</td></tr>');
            }
        }).fail(function() {
            $("#contactsTable tbody").html('<tr><td colspan="8" class="text-center">Error loading contacts</td></tr>');
        });
    }

    // Create or Update contact
    $("#vendorContactForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize() + `&profile_type=${currentProfileType}`;
        let url = edit_id ?
            `${baseUrl}/${vendor_id}/${currentProfileType}/contacts/${edit_id}` :
            `${baseUrl}/${vendor_id}/${currentProfileType}/Manage/Contacts`;
        let method = edit_id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: method,
            data: formData + `&_token={{ csrf_token() }}`,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadContacts();
                    $("#vendorContactForm")[0].reset();
                    edit_id = null;
                    $("#save-btn").text("Save");
                    $("#cancel-btn").hide();
                    $("#is_primary").prop('checked', false);
                    $("#is_emergency").prop('checked', false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: res.message || 'Error saving contact'
                    });
                }
            },
            error: function(err) {
                console.error(err.responseJSON);
                let errorMsg = 'Validation failed';
                if (err.responseJSON && err.responseJSON.errors) {
                    const errors = err.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join(', ');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg
                });
            }
        });
    });

    // Edit contact
    $(document).on("click", ".editBtn", function() {
        let tr = $(this).closest("tr");
        edit_id = tr.data("id");

        // Fetch full contact details for editing
        $.get(`${baseUrl}/${vendor_id}/${currentProfileType}/contacts/${edit_id}`, function(res) {
            if (res.success) {
                const contact = res.data;
                $("#contact_type").val(contact.contact_type).trigger('change');
                $("#value").val(contact.value);
                $("#country_code").val(contact.country_code);
                $("#label").val(contact.label);
                $("#is_primary").prop('checked', contact.is_primary);
                $("#is_emergency").prop('checked', contact.is_emergency);
            }
        });

        $("#save-btn").text("Update");
        $("#cancel-btn").show();
    });

    // Cancel edit
    $("#cancel-btn").on("click", function() {
        $("#vendorContactForm")[0].reset();
        edit_id = null;
        $("#save-btn").text("Save");
        $("#cancel-btn").hide();
        $("#is_primary").prop('checked', false);
        $("#is_emergency").prop('checked', false);
    });

    // Delete contact
    $(document).on("click", ".deleteBtn", function() {
        let contact_id = $(this).closest("tr").data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "This contact will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${vendor_id}/${currentProfileType}/contacts/${contact_id}`,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire("Deleted!", res.message, "success");
                            loadContacts();
                        } else {
                            Swal.fire("Error!", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error!", "Something went wrong.", "error");
                    }
                });
            }
        });
    });

    // Profile type change handler
    $('input[name="profile_type"]').on('change', function() {
        currentProfileType = $(this).val();
        loadContacts();
        // Reset form when switching profile types
        $("#vendorContactForm")[0].reset();
        edit_id = null;
        $("#save-btn").text("Save");
        $("#cancel-btn").hide();
        $("#is_primary").prop('checked', false);
        $("#is_emergency").prop('checked', false);
    });

    $(document).ready(function() {
        loadContacts();

        // Initialize Select2 for contact type
        $('#contact_type').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    });
</script>
@endsection