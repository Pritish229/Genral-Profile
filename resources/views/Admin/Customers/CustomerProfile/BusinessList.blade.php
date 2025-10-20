@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business List"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
            'Business List' => ''
        ]" />

    <div class="mt-4 d-flex justify-content-between align-items-center">

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
            <i class="fas fa-plus"></i> Add New Business
        </button>
    </div>

    <div class="mt-3">
        <table class="table table-bordered table-hover" id="businessTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Legal Name</th>
                    <th>Trade Name</th>
                    <th>Industry</th>
                    <th>Business Size</th>
                    <th>GST No</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be populated dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addBusinessModal" tabindex="-1" aria-labelledby="addBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="addBusinessForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBusinessModalLabel">Add New Business Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="vendor_id" value="{{ $id }}">

                    <div class="row">
                        <div class="col-md-4">
                            <x-inputbox id="legal_name" label="Legal Name" type="text" placeholder="Enter Legal Name" name="legal_name"
                                value="{{ old('legal_name') }}" :required="false" helpertxt="Legal Name max 180 Character" />
                        </div>
                        <div class="col-md-4">
                            <x-inputbox id="trade_name" label="Trade Name" type="text" placeholder="Enter Trade Name" name="trade_name"
                                value="{{ old('trade_name') }}" :required="false" helpertxt="Trade Name max 180 Character" />
                        </div>
                        <div class="col-md-4">
                            <x-inputbox id="industry" label="Industry" type="text" placeholder="Enter industry" name="industry"
                                value="{{ old('industry') }}" :required="false" helpertxt="Industry max 120 Character" />
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="business_size" class="mb-2 labeltxt">Business Size</label>
                                <select name="business_size" class="form-select" id="business_size">
                                    <option value="micro">Micro</option>
                                    <option value="sme">Sme</option>
                                    <option value="enterprise">Enrterprise</option>
                                </select>
                                <small class="mb-3 pt-1 helpertxt">Select Business Size</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="dob" class="mb-2 labeltxt">Incorporation Date</label>
                                <input type="text" id="incorporation_date" name="incorporation_date" class="form-control flatpickr"
                                    placeholder="Select Incorporation Date Date" value="{{ old('incorporation_date') }}">
                                <small class="mb-3 pt-1 helpertxt">Must be a Date</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <x-inputbox id="website" label="Website URL" type="text" placeholder="Enter Website URL" name="website"
                                value="{{ old('website') }}" :required="false" helpertxt="Max 200 Characters" />
                        </div>

                        <div class="col-md-3">
                            <x-inputbox id="billing_email" label="Billing Email" type="email" placeholder="Enter Billing Email" name="billing_email"
                                value="{{ old('billing_email') }}" :required="false" helpertxt="Billing Email Max 120 Character" />
                        </div>
                        <div class="col-md-3">
                            <x-inputbox id="billing_phone" label="Billing Phone" type="text" placeholder="Enter Billing Phone" name="billing_phone"
                                value="{{ old('billing_phone') }}" :required="false" helpertxt="Billing Phone Max 30 Digits" />

                        </div>
                        <div class="col-md-3">
                            <x-inputbox id="gst_number" label="GST No" type="text" placeholder="Enter GST No" name="gst_number"
                                value="{{ old('gst_number') }}" :required="false" helpertxt="GST No Max 20 Characters" />
                        </div>
                        <div class="col-md-3">
                            <x-inputbox id="pan_number" label="PAN No" type="text" placeholder="Enter PAN No" name="pan_number"
                                value="{{ old('pan_number') }}" :required="false" helpertxt="PAN No Max 15 Characters" />
                        </div>
                        <div class="col-md-3">
                            <x-inputbox id="cin_number" label="CIN No" type="text" placeholder="Enter CIN No" name="cin_number"
                                value="{{ old('cin_number') }}" :required="false" helpertxt="CIN No Max 25 Characters" />
                        </div>
                        <div class="col-md-3">
                            <x-inputbox id="credit_limit" label="Credit Limit" type="number" placeholder="Enter Credit Limit" name="credit_limit"
                                value="0.0" :required="false" helpertxt="Must Be Number" />
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label for="dob" class="mb-2 labeltxt">Payment Terms Days</label>
                                <input type="number" id="payment_terms_days" name="payment_terms_days" class="form-control"
                                    placeholder="Select Payment Terms Date" value="{{ old('payment_terms_days') }}">
                                <small class="mb-3 pt-1 helpertxt">Must be a Number</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-inputbox id="account_manager" label="Account Manager" type="text" placeholder="Enter Account Manager " name="account_manager"
                                value="{{ old('account_manager') }}" :required="false" helpertxt="Max 120 Charcter" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        const vendorId = "{{ $id }}";
        let baseUrl = "{{ url('/vendors') }}";

        // Load business list
        function loadBusinesses() {
            $.ajax({
                url: `${baseUrl}/${vendorId}/all/Business`,
                method: "GET",
                success: function(res) {
                    let rows = '';
                    if (res.success && res.data.length > 0) {
                        res.data.forEach((business, index) => {
                            rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${business.legal_name || ''}</td>
                                <td>${business.trade_name || ''}</td>
                                <td>${business.industry || ''}</td>
                                <td>${business.business_size || ''}</td>
                                <td>${business.gst_number || ''}</td>
                                <td>
                                    <a href="${baseUrl}/${vendorId}/${business.id}/BusinessDetails" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-2"></i> Details
                                    </a>
                                </td>
                            </tr>
                        `;
                        });
                    } else {
                        rows = `<tr><td colspan="7" class="text-center">No business profiles found.</td></tr>`;
                    }
                    $("#businessTable tbody").html(rows);
                },
                error: function() {
                    console.error("Error fetching businesses.");
                }
            });
        }

        loadBusinesses();

        // Add new business
        $("#addBusinessForm").on("submit", function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            const $saveBtn = $(this).find("button[type='submit']");
            $saveBtn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: `/vendors/${vendorId}/business/create`,
                method: "POST",
                data: formData,
                success: function(res) {
                    if (res.success) {
                        $("#addBusinessModal").modal("hide");
                        $("#addBusinessForm")[0].reset();
                        loadBusinesses();

                        Swal.fire({
                            icon: 'success',
                            title: 'Business Added!',
                            text: res.message || 'New business details added successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    console.error("Error saving business.");
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong!',
                    });
                },
                complete: function() {
                    $saveBtn.prop("disabled", false).html('<i class="fas fa-save"></i> Save');
                }
            });
        });
    });
</script>
@endsection