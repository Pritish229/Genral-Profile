@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Address"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List', 'Vendor Detail' => ['vendors.viewDetails', $id], 'Business Address' => '']" />

    <div class="mt-4">
        <!-- FORM -->
        <form id="businessAddressForm">
            @csrf
            <input type="hidden" id="address_id" name="address_id" value="">

            <div class="row">
                <div class="col-md-3">
                    <x-inputbox id="state" label="State" type="text" placeholder="Enter State Name" name="state" value="" :required="false" helpertxt="Max 120 characters" />
                </div>
                <div class="col-md-3">
                    <x-inputbox id="district" label="District" type="text" placeholder="Enter District Name" name="district" value="" :required="false" helpertxt="Max 120 characters" />
                </div>
                <div class="col-md-3">
                    <x-inputbox id="city" label="City" type="text" placeholder="Enter City Name" name="city" value="" :required="false" helpertxt="Max 120 characters" />
                </div>
                <div class="col-md-3">
                    <x-inputbox id="pincode" label="Pincode" type="text" placeholder="Enter Pincode" name="pincode" value="" :required="false" helpertxt="6 digits only" />
                </div>
                <div class="col-md-4">
                    <x-inputbox id="line1" label="Line 1" type="text" placeholder="Enter Line 1" name="line1" value="" :required="false" helpertxt="" />
                </div>
                <div class="col-md-4">
                    <x-inputbox id="line2" label="Line 2" type="text" placeholder="Enter Line 2" name="line2" value="" :required="false" helpertxt="" />
                </div>
                <div class="col-md-4">
                    <x-inputbox id="landmark" label="Landmark" type="text" placeholder="Enter Landmark" name="landmark" value="" :required="false" helpertxt="" />
                </div>
                <div class="col-md-4">
                    <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label" value="" :required="false" helpertxt="Ex: Home Address, Office Address" />
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label for="address_type" class="mb-2 labeltxt">Address Type</label>
                        <select name="address_type" class="form-select" id="address_type">
                            <option value="permanent">Permanent</option>
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
                        <label class="form-check-label" for="is_primary"> Set as Primary Address </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <x-inputbox id="longitude" label="Longitude (Optional)" type="text" placeholder="Enter Longitude" name="longitude" value="" :required="false" helpertxt="" />
                </div>
                <div class="col-md-4">
                    <x-inputbox id="latitude" label="Latitude (Optional)" type="text" placeholder="Enter Latitude" name="latitude" value="" :required="false" helpertxt="" />
                </div>
                <div class="col-lg-12 mt-2">
                    <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                    <button type="button" class="btn btn-secondary" id="cancel-edit" style="display:none;">Cancel</button>
                </div>
            </div>
        </form>

        <hr>

        <!-- ADDRESS LIST -->
        <h4>Business Addresses</h4>
        <table class="table table-bordered" id="addressTable">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Type</th>
                    <th>Address</th>
                    <th>Primary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    const vendorId = "{{ $id }}";
    const businessId = "{{ $business_id }}";

    function loadAddresses() {
        $.post(`/vendors/${vendorId}/${businessId}/Business/Address/list`, {_token: '{{ csrf_token() }}'}, function(data) {
            if (data.success) {
                const tbody = $('#addressTable tbody').empty();
                data.data.forEach(address => {
                    tbody.append(`
                        <tr data-id="${address.id}">
                            <td>${address.label}</td>
                            <td>${address.address_type}</td>
                            <td>${address.line1} ${address.line2}, ${address.city}, ${address.district}, ${address.state} - ${address.pincode}</td>
                            <td>${address.is_primary ? '<span class="badge bg-success">Primary</span>' : ''}</td>
                            <td>
                                <button class="btn btn-sm btn-info edit-btn" data-id="${address.id}">Edit</button>
                                ${!address.is_primary ? `<button class="btn btn-sm btn-danger delete-btn" data-id="${address.id}">Delete</button>` : ''}
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    loadAddresses();

    // SAVE FORM (Insert/Update)
    $('#businessAddressForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let addressId = $('#address_id').val();
        let url = `/vendors/${vendorId}/${businessId}/Business/Address/Add`;
        if (addressId) url = `/vendors/${vendorId}/${businessId}/Business/Address/${addressId}/Update`;

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    alert(data.message);
                    $('#businessAddressForm')[0].reset();
                    $('#address_id').val('');
                    $('#cancel-edit').hide();
                    loadAddresses();
                } else {
                    alert(data.message || "Something went wrong");
                }
            }
        });
    });

    // EDIT ADDRESS
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        $.get(`/vendors/${vendorId}/${businessId}/Business/Address/${id}`, function(data) {
            if (data.success) {
                let a = data.data;
                $('#address_id').val(a.id);
                $('#state').val(a.state);
                $('#district').val(a.district);
                $('#city').val(a.city);
                $('#pincode').val(a.pincode);
                $('#line1').val(a.line1);
                $('#line2').val(a.line2);
                $('#landmark').val(a.landmark);
                $('#label').val(a.label);
                $('#address_type').val(a.address_type);
                $('#longitude').val(a.longitude);
                $('#latitude').val(a.latitude);
                $('#is_primary').prop('checked', a.is_primary);
                $('#cancel-edit').show();
            }
        });
    });

    // CANCEL EDIT
    $('#cancel-edit').click(function() {
        $('#businessAddressForm')[0].reset();
        $('#address_id').val('');
        $(this).hide();
    });

    // DELETE ADDRESS
    $(document).on('click', '.delete-btn', function() {
        if (!confirm("Are you sure?")) return;
        let id = $(this).data('id');
        $.ajax({
            url: `/vendors/${vendorId}/${businessId}/Business/Address/${id}/Delete`,
            method: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function(data) {
                if (data.success) loadAddresses();
                else alert(data.message);
            }
        });
    });
});
</script>
@endsection
