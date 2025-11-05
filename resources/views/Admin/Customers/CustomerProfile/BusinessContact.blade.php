@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Contact"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Customers' => 'Customers.List',
        'Customer Details' => ['customers.viewDetails', ['id' => $id]],
        'Business List' => ['customers.Businesslist', $id],
        'Business Details' => ['customers.BusinessDetails', ['id' => $id, 'business_id' => $business_id]],
        'Business Contact' => '']" />

    <div class="mt-4">
        <form id="customerContactForm">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <x-inputbox id="contact_person_type" label="Contact Person Type"
                        type="text" placeholder="Enter type" name="contact_person_type"
                        value="{{ old('contact_person_type') }}" :required="false"
                        helpertxt="E.g., Office Address, Branch Address" />
                </div>

                <div class="col-md-3">
                    <x-inputbox id="department" label="Department" type="text"
                        placeholder="Ex: Sales, HR, Accounts" name="department"
                        value="{{ old('department') }}" :required="false" helpertxt="Ex: Sales / HR / Accounts" />
                </div>

                <div class="col-md-3">
                    <x-inputbox id="designation" label="Designation" type="text"
                        placeholder="Ex: Manager, Executive" name="designation"
                        value="{{ old('designation') }}" :required="false" helpertxt="Ex: Manager / Executive" />
                </div>

                <div class="col-md-3">
                    <x-inputbox id="contact_person_name" label="Contact Person Name" type="text"
                        placeholder="Enter Name" name="contact_person_name"
                        value="{{ old('contact_person_name') }}" :required="false" helpertxt="Ex: John Doe" />
                </div>

                <div class="col-md-3">
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
                    <small class="helpertxt">Select Contact Type</small>
                </div>

                <div class="col-md-3">
                    <x-inputbox id="value" label="Contact Value" type="text"
                        placeholder="Enter contact value" name="value"
                        value="{{ old('value') }}" :required="true"
                        helpertxt="Phone number, email, etc." />
                </div>

                <div class="col-md-3" id="extension_field" style="display:none;">
                    <x-inputbox id="extension" label="Extension" type="text"
                        placeholder="Enter extension (optional)" name="extension"
                        value="{{ old('extension') }}" :required="false"
                        helpertxt="Ex: 101, 205" />
                </div>

                <div class="col-md-3">
                    <x-inputbox id="country_code" label="Country Code" type="text"
                        placeholder="Enter country code" name="country_code"
                        value="{{ old('country_code') }}" :required="false"
                        helpertxt="Ex: +91, +1" />
                </div>

                <div class="col-md-3">
                    <x-inputbox id="label" label="Label" type="text"
                        placeholder="Enter Label" name="label"
                        value="{{ old('label') }}" :required="false"
                        helpertxt="Ex: Personal, Work" />
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

        <div class="mt-4">
            <table class="table table-bordered" id="contactsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Person Type</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Person Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Extension</th>
                        <th>Code</th>
                        <th>Label</th>
                        <th>Primary</th>
                        <th>Emergency</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>
@endsection


@section('script')
<script>
    let baseUrl = "{{ url('customers') }}";
    let customer_id = "{{ $id }}";
    let business_id = "{{ $business_id }}";
    let editingContactId = null;

    $('#contact_type').on('change', function() {
        $(this).val() === 'phone' ? $('#extension_field').show() : $('#extension_field').hide();
    });

    function loadContacts() {
        $.get(`${baseUrl}/${customer_id}/${business_id}/Business/Contacts/List`, function(r) {
            let rows = "",
                i = 1;
            r.data.forEach(c => {
                rows += `
                <tr data-id="${c.id}">
                    <td>${i++}</td>
                    <td>${c.contact_person_type ?? '-'}</td>
                    <td>${c.department ?? '-'}</td>
                    <td>${c.designation ?? '-'}</td>
                    <td>${c.contact_person_name ?? '-'}</td>
                    <td>${c.contact_type}</td>
                    <td>${c.value}</td>
                    <td>${c.extension ?? '-'}</td>
                    <td>${c.country_code ?? '-'}</td>
                    <td>${c.label ?? '-'}</td>
                    <td>${c.is_primary ? 'Yes' : 'No'}</td>
                    <td>${c.is_emergency ? 'Yes' : 'No'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editBtn">Edit</button>
                        ${c.is_primary ? '' : `<button class="btn btn-sm btn-danger deleteBtn">Delete</button>`}
                    </td>
                </tr>`;
            });
            $("#contactsTable tbody").html(rows);
        });
    }

    $("#customerContactForm").submit(function(e) {
        e.preventDefault();
        let url = editingContactId ?
            `${baseUrl}/${customer_id}/${business_id}/Business/Contacts/${editingContactId}` :
            `${baseUrl}/${customer_id}/${business_id}/Business/Contacts`;

        let formData = $(this).serialize();

        $.ajax({
            url: url,
            type: editingContactId ? "PUT" : "POST",
            data: formData,
            success: function(res) {
                editingContactId = null;
                $("#customerContactForm")[0].reset();
                $("#cancel-btn").hide();
                loadContacts();
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    $(document).on("click", ".editBtn", function() {
        editingContactId = $(this).closest("tr").data("id");
        $("#cancel-btn").show();

        $.get(`${baseUrl}/${customer_id}/${business_id}/Business/Contact/${editingContactId}`, function(r) {
            let c = r.data;
            $("#contact_person_type").val(c.contact_person_type || "");
            $("#department").val(c.department || "");
            $("#designation").val(c.designation || "");
            $("#contact_person_name").val(c.contact_person_name || "");
            $("#contact_type").val(c.contact_type);
            $("#value").val(c.value);
            $("#extension").val(c.extension || "");
            $("#country_code").val(c.country_code || "");
            $("#label").val(c.label || "");
            $("#is_primary").prop("checked", c.is_primary);
            $("#is_emergency").prop("checked", c.is_emergency);
            c.contact_type === 'phone' ? $('#extension_field').show() : $('#extension_field').hide();
        });
    });

    $(document).on("click", ".deleteBtn", function() {
        let id = $(this).closest("tr").data("id");
        $.ajax({
            url: `${baseUrl}/${customer_id}/${business_id}/Business/Contacts/${id}`,
            type: "DELETE",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                loadContacts();
                Swal.fire('Deleted', res.message, 'success');
            }
        });
    });

    $("#cancel-btn").click(function() {
        editingContactId = null;
        $("#customerContactForm")[0].reset();
        $(this).hide();
    });

    $(document).ready(loadContacts);
</script>
@endsection