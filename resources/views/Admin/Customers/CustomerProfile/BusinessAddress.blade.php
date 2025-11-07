@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Address"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Customers' => 'customers.List',
        'Customers Details' => ['customers.viewDetails', ['id' => $id]],
        'Business List' => ['customers.Businesslist', $id],
        'Business Details' => ['customers.BusinessDetails', ['id' => $id, 'business_id' => $business_id]],
        'Business Address' => ''
    ]" />
    <div class="mt-4">
        <form id="businessAddressForm">
            @csrf
            <input type="hidden" id="address_id" name="address_id" value="">

            <div class="row">
                <div class="col-md-3">
                    <x-inputbox label="State" type="text" placeholder="Enter State Name" name="state" id="state" value="" :disabled="false" helpertxt="Max 120 characters" :required="false" />
                </div>
                <div class="col-md-3">
                    <x-inputbox label="District" type="text" placeholder="Enter District Name" name="district" id="district" value="" :disabled="false" helpertxt="Max 120 characters" :required="false" />
                </div>
                <div class="col-md-3">
                    <x-inputbox label="City" type="text" placeholder="Enter City Name" name="city" id="city" value="" :disabled="false" helpertxt="Max 120 characters" :required="false" />
                </div>
                <div class="col-md-3">
                    <x-inputbox label="Pincode" type="text" placeholder="Enter Pincode" name="pincode" id="pincode" value="" :disabled="false" helpertxt="6 digits only" :required="false" />
                </div>

                <div class="col-md-4">
                    <x-inputbox label="Line 1" type="text" placeholder="Enter Line 1" name="line1" id="line1" value="" :disabled="false" helpertxt="" :required="false" />
                </div>
                <div class="col-md-4">
                    <x-inputbox label="Line 2" type="text" placeholder="Enter Line 2" name="line2" id="line2" value="" :disabled="false" helpertxt="" :required="false" />
                </div>
                <div class="col-md-4">
                    <x-inputbox label="Landmark" type="text" placeholder="Enter Landmark" name="landmark" id="landmark" value="" :disabled="false" helpertxt="" :required="false" />
                </div>

                <div class="col-md-4">
                    <x-inputbox label="Label" type="text" placeholder="Enter Label" name="label" id="label" value="" :disabled="false" helpertxt="Ex: Home, Head Office" :required="false" />
                </div>

                <div class="col-md-4">
                    <label for="address_type" class="mb-2 labeltxt">Address Type</label>
                    <select name="address_type" class="form-select" id="address_type">
                        <option value="permanent">Permanent</option>
                        <option value="office">Office</option>
                        <option value="billing">Billing</option>
                        <option value="shipping">Shipping</option>
                        <option value="other">Other</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Address Type</small>
                </div>

                <div class="col-md-4">
                    <x-inputbox label="Address Type Name" type="text" placeholder="e.g. Head Office, Factory" name="address_type_name" id="address_type_name" value="" :disabled="false" helpertxt="" :required="true" />
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary"> Set as Primary Address </label>
                    </div>
                </div>

                <div class="col-md-4">
                    <x-inputbox label="Longitude" type="text" placeholder="Enter Longitude" name="longitude" id="longitude" value="" :disabled="false" helpertxt="" :required="false" />
                </div>
                <div class="col-md-4">
                    <x-inputbox label="Latitude" type="text" placeholder="Enter Latitude" name="latitude" id="latitude" value="" :disabled="false" helpertxt="" :required="false" />
                </div>

                <div class="col-lg-12 mt-2">
                    <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                    <button type="button" class="btn btn-secondary" id="cancel-edit" style="display:none;">Cancel</button>
                </div>
            </div>
        </form>

        <hr>

        <h4>Business Addresses</h4>
        <table class="table table-bordered" id="addressTable">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Address Type Name</th>
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

    const customerId = "{{ $id }}";
    const businessId = "{{ $business_id }}";

    function loadAddresses() {
        $.get(`/customers/${customerId}/${businessId}/Business/Address/list`, function(data) {
            if (data.success) {
                const tbody = $('#addressTable tbody').empty();
                data.data.forEach(address => {
                    tbody.append(`
                    <tr>
                        <td>${address.label || '-'}</td>
                        <td>${address.address_type_name || '-'}</td>
                        <td>${address.line1} ${address.line2}, ${address.city}, ${address.district}, ${address.state} - ${address.pincode}</td>
                        <td>${address.is_primary ? '<span class="badge bg-success">Primary</span>' : ''}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" data-id="${address.id}">Edit</button>
                            ${!address.is_primary ? `<button class="btn btn-sm btn-danger delete-btn" data-id="${address.id}">Delete</button>` : ''}
                        </td>
                    </tr>`);
                });
            }
        });
    }

    loadAddresses();

    $('#businessAddressForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#address_id').val();
        let url = id
            ? `/customers/${customerId}/${businessId}/Business/Address/${id}/Update`
            : `/customers/${customerId}/${businessId}/Business/Address/Add`;

        $.post(url, $(this).serialize(), function(data) {
            swal.fire(data.message, "", data.success ? "success" : "error");
            if(data.success){
                $('#businessAddressForm')[0].reset();
                $('#address_id').val('');
                $('#cancel-edit').hide();
                loadAddresses();
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        $.get(`/customers/${customerId}/${businessId}/Business/Address/${id}`, function(data) {
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
                $('#address_type_name').val(a.address_type_name);
                $('#longitude').val(a.longitude);
                $('#latitude').val(a.latitude);
                $('#is_primary').prop('checked', a.is_primary);
                $('#cancel-edit').show();
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        if (!confirm("Are you sure?")) return;
        let id = $(this).data('id');
        $.ajax({
            url: `/customers/${customerId}/${businessId}/Business/Address/${id}/Delete`,
            method: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function(data) {
                swal.fire(data.message, "", data.success ? "success" : "error");
                loadAddresses();
            }
        });
    });

    $('#cancel-edit').click(function() {
        $('#businessAddressForm')[0].reset();
        $('#address_id').val('');
        $(this).hide();
    });

});
</script>
@endsection
