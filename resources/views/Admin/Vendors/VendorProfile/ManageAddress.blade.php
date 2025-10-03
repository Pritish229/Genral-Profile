@extends('Admin.layout.app')

@section('title', 'Home | Manage Address')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Address"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
        'Manage Address' => ''
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

    <!-- Address Form -->
    <form id="vendorAddressForm">
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
                    value="{{ old('line1') }}" :required="false" helpertxt="" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="line2" label="Line 2" type="text" placeholder="Enter Line 2" name="line2"
                    value="{{ old('line2') }}" :required="false" helpertxt="" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="landmark" label="Landmark" type="text" placeholder="Enter Landmark" name="landmark"
                    value="{{ old('landmark') }}" :required="false" helpertxt="" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Ex: Home Address, Office Address" />
            </div>
            <div class="col-md-4">
                <div class="mb-2">
                    <label for="address_type" class="mb-2 labeltxt">Address Type</label>
                    <select name="address_type" class="form-select" id="address_type">
                        <option value="permanent">Permanent</option>
                        <option value="temporary">Temporary</option>
                        <option value="office">Office</option>
                        <option value="billing">Billing</option>
                        <option value="shipping">Shipping</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Address Type</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                    <label class="form-check-label" for="is_primary">
                        Set as Primary Address
                    </label>
                </div>
            </div>
            <div class="col-md-4">
                <x-inputbox id="longitude" label="Longitude (Optional)" type="text" placeholder="Enter Longitude" name="longitude"
                    value="{{ old('longitude') }}" :required="false" helpertxt="" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="latitude" label="Latitude (Optional)" type="text" placeholder="Enter Latitude" name="latitude"
                    value="{{ old('latitude') }}" :required="false" helpertxt="" />
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
                    <th>Type</th>
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
    let baseUrl = "{{ url('vendors') }}";
    let vendor_id = "{{ $id }}";
    let edit_id = null; // track address being edited
    let currentProfileType = 'individual'; // track current profile type

    // Fetch & render addresses
    function loadAddresses() {
        $.get(`${baseUrl}/${vendor_id}/${currentProfileType}/Get/Addresses`, function(res) {
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
                            <td>${address.address_type ?? '-'}</td>
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
    $("#vendorAddressForm").on("submit", function(e) {
        e.preventDefault();
        let formData = $(this).serialize() + `&profile_type=${currentProfileType}`;
        let url = edit_id ?
            `${baseUrl}/${vendor_id}/${currentProfileType}/addresses/${edit_id}` :
            `${baseUrl}/${vendor_id}/${currentProfileType}/Manage/Addresses`;
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
                    loadAddresses();
                    $("#vendorAddressForm")[0].reset();
                    edit_id = null;
                    $("#save-btn").text("Save");
                    $("#is_primary").prop('checked', false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: res.message || 'Error saving address'
                    });
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

        // Fetch full address details for editing
        $.get(`${baseUrl}/${vendor_id}/${currentProfileType}/addresses/${edit_id}`, function(res) {
            if (res.success) {
                const address = res.data;
                $("#state").val(address.state);
                $("#district").val(address.district);
                $("#city").val(address.city);
                $("#pincode").val(address.pincode);
                $("#line1").val(address.line1);
                $("#line2").val(address.line2);
                $("#landmark").val(address.landmark);
                $("#label").val(address.label);
                $("#address_type").val(address.address_type);
                $("#longitude").val(address.longitude);
                $("#latitude").val(address.latitude);
                $("#is_primary").prop('checked', address.is_primary);
            }
        });

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
                    url: `${baseUrl}/${vendor_id}/${currentProfileType}/addresses/${address_id}`,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
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

    // Profile type change handler
    $('input[name="profile_type"]').on('change', function() {
        currentProfileType = $(this).val();
        loadAddresses();
        // Reset form when switching profile types
        $("#vendorAddressForm")[0].reset();
        edit_id = null;
        $("#save-btn").text("Save");
        $("#is_primary").prop('checked', false);
    });

    $(document).ready(function() {
        loadAddresses();

        // Initialize Select2 for address type
        $('#address_type').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    });
</script>
@endsection