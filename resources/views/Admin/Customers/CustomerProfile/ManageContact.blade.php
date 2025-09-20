@extends('Admin.layout.app')

@section('title', 'Home | Students | Manage Contact')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Contacts"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Students' => 'students.Studentlist',
        'Student Detail' => ['students.Studentlist.studentDetailsPage', $id],
        'Manage Contacts' => ''
    ]" />
    <!-- Contact Form -->
    <form id="studentContactForm">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label for="contact_type" class="form-label">Contact Type</label>
                <select id="contact_type" name="contact_type" class="form-select">
                    <option value="">-- Select Type --</option>
                    <option value="phone">Phone</option>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telegram">Telegram</option>
                    <option value="fax">FAX</option>
                </select>
            </div>
            <div class="col-md-4">
                <x-inputbox id="value" label="Value" type="text" placeholder="Enter Value" name="value"
                    value="{{ old('value') }}" :required="false" helpertxt="Enter phone, email, etc." />
            </div>
            <div class="col-md-4">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Optional label (e.g. Father's Phone)" />
            </div>
            <div class="col-lg-12 mt-2">
                <button type="submit" class="btn btn-primary" id="sav-btn">Save</button>
            </div>
        </div>
    </form>

    <!-- Contacts Table -->
    <div class="mt-4">
        <table class="table table-bordered" id="contactsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Label</th>
                    <th>Primary</th>
                    <th>Emergency</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filled dynamically via AJAX -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('students') }}";
    let student_id = "{{ $id }}";

    // Fetch and render contacts
    function loadContacts() {
        $.get(`${baseUrl}/${student_id}/Get/Contacts`, function(response) {
            if (response.success) {
                let rows = "";
                let index = 1;

                response.data.forEach(contact => {
                    rows += `
                <tr data-id="${contact.id}">
                    <td>${index++}</td>
                    <td>${contact.contact_type}</td>
                    <td>${contact.value}</td>
                    <td>${contact.label ?? '-'}</td>
                    <td>${contact.is_primary ? 'Yes' : 'No'}</td>
                    <td>${contact.is_emergency ? 'Yes' : 'No'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editBtn">Edit</button>
                        ${contact.is_primary 
                            ? '' 
                            : '<button class="btn btn-sm btn-danger deleteBtn">Delete</button>'}
                    </td>
                </tr>`;
                });

                $("#contactsTable tbody").html(rows);
            }
        });
    }


    // Create contact
    $("#studentContactForm").on("submit", function(e) {
        e.preventDefault();
        $('#sav-btn').prop('diabled', true).text('Saving...');
        let formData = $(this).serialize();
        let url = `${baseUrl}/${student_id}/Manage/Contacts`; // ✅ FIXED

        $.post(url, formData + `&student_id=${student_id}`, function(response) {
            $('#sav-btn').prop('diabled', false).text('Save');
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Contact Added Success',
                    text: 'New Contact Added .',
                });
                loadContacts();
                $("#studentContactForm")[0].reset();
            } else {
                $('#sav-btn').prop('diabled', false).text('Save');
                alert("Error saving contact");
                 $('#sav-btn').prop('diabled', false).text('Save');
                }
            }).fail(err => {
                alert("Validation failed");
                console.error(err.responseJSON);
                $('#sav-btn').prop('diabled', false).text('Save');
        });
    });

    // Delete contact
    $(document).on("click", ".deleteBtn", function() {
        let contact_id = $(this).closest("tr").data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This contact will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${student_id}/contacts/${contact_id}`,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire("Deleted!", response.message, "success");
                            loadContacts();
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function(err) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                    }
                });
            }
        });
    });

    // Edit contact
    $(document).on("click", ".editBtn", function() {
        let tr = $(this).closest("tr");
        let contact_id = tr.data("id");

        $("#contact_type").val(tr.find("td:eq(1)").text());
        $("#value").val(tr.find("td:eq(2)").text());
        $("#label").val(tr.find("td:eq(3)").text());

        // Change form submit to update mode
        $("#studentContactForm").off("submit").on("submit", function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: `${baseUrl}/${student_id}/contacts/${contact_id}`,
                type: "PUT",
                data: formData + `&_token={{ csrf_token() }}`, // add token for Laravel
                success: function(response) {
                    if (response.success) {
                        loadContacts();
                        $("#studentContactForm")[0].reset();

                        // Restore original submit (create mode)
                        $("#studentContactForm").off("submit").on("submit", function(e) {
                            e.preventDefault();
                            let formData = $(this).serialize();
                            $.post(`${baseUrl}/${student_id}/Manage/Contacts`, formData, function(res) {
                                if (res.success) {
                                    loadContacts();
                                    $("#studentContactForm")[0].reset();
                                }
                            });
                        });
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        loadContacts();
    });
</script>
@endsection