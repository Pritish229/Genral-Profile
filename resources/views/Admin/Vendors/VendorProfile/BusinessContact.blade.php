@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Contact"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List', 'Vendor Detail' => ['vendors.viewDetails', $id], 'Business Contact' => '']" />

    <div class="mt-4">
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
                        <label class="form-check-label" for="is_primary">Set as Primary Contact</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_emergency" name="is_emergency" value="1">
                        <label class="form-check-label" for="is_emergency">Emergency Contact</label>
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
</div>
@endsection


@section('script')
<script>
    let baseUrl = "{{ url('vendors') }}";
    let vendor_id = "{{ $id }}";
    let business_id = "{{ $business_id }}";
    let editingContactId = null;

    // Load contacts list
    function loadContacts() {
        $.get(`${baseUrl}/${vendor_id}/${business_id}/Business/Contacts/List`, function(response) {
            if (response.success) {
                let rows = "";
                let index = 1;
                response.data.forEach(contact => {
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

    // Save contact (add/update)
    $("#vendorContactForm").submit(function(e) {
        e.preventDefault();
        let formData = $(this).serializeArray();
        formData.push({
            name: "_token",
            value: "{{ csrf_token() }}"
        });

        let url = editingContactId ?
            `${baseUrl}/${vendor_id}/${business_id}/Business/Contacts/${editingContactId}` :
            `${baseUrl}/${vendor_id}/${business_id}/Business/Contacts`;

        let type = editingContactId ? "PUT" : "POST";

        $("#save-btn").attr("disabled", true);

        Swal.fire({
            title: editingContactId ? "Updating..." : "Adding...",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: url,
            type: type,
            data: formData,
            success: function(response) {
                Swal.close();
                $("#save-btn").attr("disabled", false);
                Swal.fire('Success', response.message || "Operation successful", 'success');
                editingContactId = null;
                $("#vendorContactForm")[0].reset();
                $("#cancel-btn").hide();
                loadContacts();
            },
            error: function(xhr) {
                Swal.close();
                $("#save-btn").attr("disabled", false);
                Swal.fire('Error', xhr.responseJSON?.message || "Something went wrong", 'error');
            }
        });
    });

    // Edit contact
    $(document).on("click", ".editBtn", function() {
        editingContactId = $(this).closest("tr").data("id");
        $("#cancel-btn").show();

        $.get(`${baseUrl}/${vendor_id}/${business_id}/Business/Contact/${editingContactId}`, function(response) {
            if (response.success) {
                let contact = response.data;
                $("#contact_type").val(contact.contact_type);
                $("#value").val(contact.value);
                $("#country_code").val(contact.country_code || "");
                $("#label").val(contact.label || "");
                $("#is_primary").prop("checked", contact.is_primary);
                $("#is_emergency").prop("checked", contact.is_emergency);
            } else {
                Swal.fire('Error', response.message || "Could not fetch contact", 'error');
            }
        }).fail(function() {
            Swal.fire('Error', "Failed to load contact details", 'error');
        });
    });

    // Cancel editing
    $("#cancel-btn").click(function() {
        editingContactId = null;
        $("#vendorContactForm")[0].reset();
        $(this).hide();
    });

    // Delete contact
    $(document).on("click", ".deleteBtn", function() {
        let contactId = $(this).closest("tr").data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the contact permanently!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Deleting...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: `${baseUrl}/${vendor_id}/${business_id}/Business/Contacts/${contactId}`,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.close();
                        Swal.fire('Deleted!', response.message || "Contact deleted.", 'success');
                        loadContacts();
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Error', "Could not delete contact", 'error');
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        loadContacts();
    });
</script>
@endsection