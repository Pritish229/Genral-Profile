@extends('Admin.layout.app')

@section('title', 'Home | Manage Address')

@section('content')
<div class="page-content">
    <x-breadcrumb
    title="Manage Address"
    :links="[
        'Home' => 'Admin.Dashboard',
        'Students' => 'students.Studentlist',
        'Student Detail' => ['students.Studentlist.studentDetailsPage', $id],
        'Manage Address' => ''
    ]"
/>
    <!-- Address Form -->
    <form id="studentAddressForm">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <x-inputbox id="state" label="State" type="text" placeholder="Enter State Name" name="state"
                    value="{{ old('state') }}" :required="false" helpertxt="Max 120 characters" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="district" label="District" type="text" placeholder="Enter District Name" name="district"
                    value="{{ old('district') }}" :required="false" helpertxt="Max 120 characters" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="city" label="City" type="text" placeholder="Enter City Name" name="city"
                    value="{{ old('city') }}" :required="false" helpertxt="Max 120 characters" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="pincode" label="Pincode" type="text" placeholder="Enter Pincode" name="pincode"
                    value="{{ old('pincode') }}" :required="false" helpertxt="6 digits only" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="line1" label="Line 1" type="text" placeholder="Enter Line 1" name="line1"
                    value="{{ old('line1') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="line2" label="Line 2" type="text" placeholder="Enter Line 2" name="line2"
                    value="{{ old('line2') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="landmark" label="Landmark" type="text" placeholder="Enter Landmark" name="landmark"
                    value="{{ old('landmark') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Ex: Parents Address" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="longitude" label="Longitude (Optional)" type="text" placeholder="Enter Longitude" name="longitude"
                    value="{{ old('longitude') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="latitude" label="Latitude (Optional)" type="text" placeholder="Enter Latitude" name="latitude"
                    value="{{ old('latitude') }}" :required="false" />
            </div>
            <div class="col-lg-12 mt-2">
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
            </div>
        </div>
    </form>

    <!-- Address Table -->
    <div class="mt-4">
        <table class="table table-bordered" id="addressesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>State</th>
                    <th>District</th>
                    <th>City</th>
                    <th>Pincode</th>
                    <th>Label</th>
                    <th>Primary</th>
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
    let baseUrl = "{{ url('students') }}";
    let student_id = "{{ $id }}";
    let edit_id = null; // track address being edited

    // Fetch & render addresses
    function loadAddresses() {
        $.get(`${baseUrl}/${student_id}/Get/Addresses`, function(res) {
            if (res.success) {
                let rows = "";
                let index = 1;
                res.data.forEach(address => {
                    rows += `
                        <tr data-id="${address.id}">
                            <td>${index++}</td>
                            <td>${address.state}</td>
                            <td>${address.district}</td>
                            <td>${address.city}</td>
                            <td>${address.pincode}</td>
                            <td>${address.label ?? '-'}</td>
                            <td>${address.is_primary ? 'Yes' : 'No'}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editBtn">Edit</button>
                                ${address.is_primary ? '' : `<button class="btn btn-sm btn-danger deleteBtn">Delete</button>`}
                            </td>
                        </tr>`;
                });
                $("#addressesTable tbody").html(rows);
            }
        });
    }

    // Create or Update address
    $("#studentAddressForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        let url = edit_id 
            ? `${baseUrl}/${student_id}/addresses/${edit_id}`
            : `${baseUrl}/${student_id}/Manage/Addresses`;
        let method = edit_id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: method,
            data: formData + `&_token={{ csrf_token() }}`,
            success: function(res) {
                if (res.success) {
                    loadAddresses();
                    $("#studentAddressForm")[0].reset();
                    edit_id = null;
                    $("#save-btn").text("Save");
                } else {
                    alert("Error saving address");
                }
            },
            error: function(err) {
                console.error(err.responseJSON);
                alert("Validation failed");
            }
        });
    });

    // Edit address
    $(document).on("click", ".editBtn", function() {
        let tr = $(this).closest("tr");
        edit_id = tr.data("id");

        $("#state").val(tr.find("td:eq(1)").text());
        $("#district").val(tr.find("td:eq(2)").text());
        $("#city").val(tr.find("td:eq(3)").text());
        $("#pincode").val(tr.find("td:eq(4)").text());
        $("#label").val(tr.find("td:eq(5)").text() === '-' ? '' : tr.find("td:eq(5)").text());

        $("#save-btn").text("Update");
    });

    // Delete address
    $(document).on("click", ".deleteBtn", function() {
        let address_id = $(this).closest("tr").data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "This address will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${student_id}/addresses/${address_id}`,
                    type: "DELETE",
                    data: {_token: "{{ csrf_token() }}"},
                    success: function(res) {
                        if (res.success) {
                            Swal.fire("Deleted!", res.message, "success");
                            loadAddresses();
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

    $(document).ready(function() {
        loadAddresses();
    });
</script>
@endsection