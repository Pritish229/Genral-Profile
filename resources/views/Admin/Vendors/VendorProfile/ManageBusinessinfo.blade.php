@extends('Admin.layout.app')

@section('title', 'Home | Manage Business Info')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Business Information"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
        'Manage Business Info' => ''
    ]" />

    <form id="businessForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="vendor_id" name="vendor_id" value="{{ $id }}">

        <div class="row g-3">
            <h5>Business Information</h5>
            <hr style="color:#5156be">

            <div class="col-md-4">
                <x-inputbox id="legal_name" label="Legal Name" type="text" placeholder="Enter Legal Name" name="legal_name"
                    value="" :required="true" helpertxt="Legal Name Maximum 180 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="trade_name" label="Trade Name" type="text" placeholder="Enter Trade Name" name="trade_name"
                    value="" :required="false" helpertxt="Trade Name Maximum 180 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="industry" label="Industry" type="text" placeholder="Enter Industry" name="industry"
                    value="" :required="false" helpertxt="Industry Maximum 120 Characters" />
            </div>

            <div class="col-md-6">
                <div class="mb-2">
                    <label for="business_size" class="mb-2 labeltxt">Business Size</label>
                    <select name="business_size" class="form-select" id="business_size">
                        <option value="micro">Micro</option>
                        <option value="sme">SME</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Business Size</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <label for="incorporation_date" class="mb-2 labeltxt">Incorporation Date</label>
                    <input type="text" id="incorporation_date" name="incorporation_date" class="form-control flatpickr"
                        placeholder="Select Incorporation Date" value="">
                    <small class="mb-3 pt-1 helpertxt">Date of Incorporation</small>
                </div>
            </div>

            <div class="col-md-12">
                <x-inputbox id="website" label="Website URL" type="url" placeholder="Enter Website URL" name="website"
                    value="" :required="false" helpertxt="Website URL Maximum 200 Characters" />
            </div>

            <h5>Contact Information</h5>
            <hr style="color:#5156be">

            <div class="col-md-4">
                <x-inputbox id="primary_contact_name" label="Primary Contact Name" type="text" placeholder="Enter Contact Name" name="primary_contact_name"
                    value="" :required="false" helpertxt="Primary Contact Maximum 150 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="primary_contact_email" label="Primary Contact Email" type="email" placeholder="Enter Contact Email" name="primary_contact_email"
                    value="" :required="false" helpertxt="Primary Contact Email Maximum 150 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="primary_contact_phone" label="Primary Contact Phone" type="text" placeholder="Enter Contact Phone" name="primary_contact_phone"
                    value="" :required="false" helpertxt="Primary Contact Phone Maximum 30 Characters" />
            </div>

            <div class="col-md-6">
                <x-inputbox id="billing_email" label="Billing Email" type="email" placeholder="Enter Billing Email" name="billing_email"
                    value="" :required="false" helpertxt="Billing Email Maximum 150 Characters" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="billing_phone" label="Billing Phone" type="text" placeholder="Enter Billing Phone" name="billing_phone"
                    value="" :required="false" helpertxt="Billing Phone Maximum 30 Characters" />
            </div>

            <h5>Legal & Financial Information</h5>
            <hr style="color:#5156be">

            <div class="col-md-4">
                <x-inputbox id="gst_number" label="GST Number" type="text" placeholder="Enter GST Number" name="gst_number"
                    value="" :required="false" helpertxt="GST Number Maximum 15 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="pan_number" label="PAN Number" type="text" placeholder="Enter PAN Number" name="pan_number"
                    value="" :required="false" helpertxt="PAN Number Maximum 15 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="cin_number" label="CIN Number" type="text" placeholder="Enter CIN Number" name="cin_number"
                    value="" :required="false" helpertxt="CIN Number Maximum 25 Characters" />
            </div>

            <div class="col-md-4">
                <x-inputbox id="credit_limit" label="Credit Limit" type="number" placeholder="Enter Credit Limit" name="credit_limit"
                    value="" :required="false" helpertxt="Credit Limit (Numeric)" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="payment_terms_days" label="Payment Terms (Days)" type="number" placeholder="Enter Payment Terms" name="payment_terms_days"
                    value="" :required="false" helpertxt="Payment Terms in Days (Numeric)" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="account_manager" label="Account Manager" type="text" placeholder="Enter Account Manager" name="account_manager"
                    value="" :required="false" helpertxt="Account Manager Maximum 120 Characters" />
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
    jQuery(function($) {
        let baseUrl = "{{ url('/vendors') }}";
        let vendorId = "{{ $id }}";

        // Initialize date picker
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true
        });

        // Initialize Select2 for business size
        $('#business_size').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        // Load business data
        function loadBusinessData() {
            $.ajax({
                url: `${baseUrl}/${vendorId}/Business`,
                type: 'GET',
                success: function(response) {
                    console.log('Business data loaded:', response);

                    if (response.success && response.data) {
                        const business = response.data;

                        // Fill business data
                        $('#legal_name').val(business.legal_name || '');
                        $('#trade_name').val(business.trade_name || '');
                        $('#industry').val(business.industry || '');
                        $('#business_size').val(business.business_size || 'micro').trigger('change');
                        $('#website').val(business.website || '');

                        // Handle incorporation date
                        if (business.incorporation_date) {
                            document.querySelector("#incorporation_date")._flatpickr.setDate(business.incorporation_date, true, "Y-m-d");
                        }

                        // Contact information
                        $('#primary_contact_name').val(business.primary_contact_name || '');
                        $('#primary_contact_email').val(business.primary_contact_email || '');
                        $('#primary_contact_phone').val(business.primary_contact_phone || '');
                        $('#billing_email').val(business.billing_email || '');
                        $('#billing_phone').val(business.billing_phone || '');

                        // Legal & Financial information
                        $('#gst_number').val(business.gst_number || '');
                        $('#pan_number').val(business.pan_number || '');
                        $('#cin_number').val(business.cin_number || '');
                        $('#credit_limit').val(business.credit_limit || '');
                        $('#payment_terms_days').val(business.payment_terms_days || '');
                        $('#account_manager').val(business.account_manager || '');
                    } else {
                        console.log('No business profile found or failed to load');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading business data:', xhr);
                    if (xhr.status !== 404) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load business data: ' + (xhr.responseJSON?.message || xhr.statusText)
                        });
                    }
                }
            });
        }

        // Form Submit
        $('#businessForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#save_btn').prop('disabled', true);
            const formData = new FormData(this);

            Swal.fire({
                title: 'Updating business information...',
                html: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                    url: `${baseUrl}/${vendorId}/Business/Update`,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false
                })
                .done(function(response) {
                    Swal.close();

                    if (response && response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Business information updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${vendorId}/view/Details`;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: (response && response.message) ? response.message : 'Update failed.'
                        });
                    }
                })
                .fail(function(xhr) {
                    Swal.close();
                    $btn.prop('disabled', false);

                    if (xhr.status === 422) {
                        const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};
                        let html = '<div class="alert alert-danger"><ul>';
                        Object.keys(errors).forEach(k => {
                            const v = errors[k];
                            html += '<li><strong>' + k + ':</strong> ' + (Array.isArray(v) ? v[0] : v) + '</li>';
                        });
                        html += '</ul></div>';

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: html
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong, please try again. Status: ' + xhr.status
                        });
                    }
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });

        // Load data on page load
        loadBusinessData();
    });
</script>
@endsection