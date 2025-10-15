@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Details"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
        'Business List' => ['vendors.Businesslist', $id],
            'Vendor Details' => ['vendors.viewDetails', $id],
            'Business Details' => ''
        ]" />

    <div class="mt-4">
        <div id="business-info-cards" class="row">
            Loading Business Profile Information...
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";
    let business_id = "{{ $business_id }}";

    function safe(val) {
        return val ? val : 'Not Provided';
    }

    function businessProfileInfo(data, info) {
        let profileCard = `
    <div class="col-12 mb-3">
        <div class="card p-3 flex-fill">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-building"></i> Basic Details</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/ManageBusinessinfo" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-3"><p><i class="fas fa-building"></i> <strong>Legal Name:</strong> ${safe(info.legal_name)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-store"></i> <strong>Trade Name:</strong> ${safe(info.trade_name)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-industry"></i> <strong>Industry:</strong> ${safe(info.industry)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-chart-bar"></i> <strong>Business Size:</strong> ${safe(info.business_size)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-calendar"></i> <strong>Incorporation Date:</strong> ${safe(info.incorporation_date)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-globe"></i> <strong>Website:</strong> ${safe(info.website)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-file-alt"></i> <strong>GST Number:</strong> ${safe(info.gst_number)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-id-card"></i> <strong>PAN Number:</strong> ${safe(info.pan_number)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-certificate"></i> <strong>CIN Number:</strong> ${safe(info.cin_number)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-credit-card"></i> <strong>Credit Limit:</strong> ${safe(info.credit_limit)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-calendar-alt"></i> <strong>Payment Terms:</strong> ${safe(info.payment_terms_days)} days</p></div>
                <div class="col-md-3"><p><i class="fas fa-user-tie"></i> <strong>Account Manager:</strong> ${safe(info.account_manager)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-envelope"></i> <strong>Primary Contact Email:</strong> ${safe(info.primary_contact_email)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-phone"></i> <strong>Primary Contact Phone:</strong> ${safe(info.primary_contact_phone)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-envelope"></i> <strong>Billing Email:</strong> ${safe(info.billing_email)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-phone"></i> <strong>Billing Phone:</strong> ${safe(info.billing_phone)}</p></div>
            </div>
        </div>
    </div>`;

        let contactCard = `
    <div class="col-md-6 mb-3 d-flex">
        <div class="card p-3 flex-fill equal-height" id="business-contact-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                    <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Contact" class="text-decoration-none">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <p>Loading Business Contact Information...</p>
            </div>
        </div>
    </div>`;

        let addressCard = `
    <div class="col-md-6 mb-3 d-flex">
        <div class="card p-3 flex-fill equal-height" id="business-address-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-map-marker-alt"></i> Address Info</h5>
                    <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Address" class="text-decoration-none">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <p>Loading Business Address Information...</p>
            </div>
        </div>
    </div>`;

        let bankCard = `
    <div class="col-md-6 mb-3 d-flex">
        <div class="card p-3 flex-fill equal-height" id="business-bank-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-university"></i> Bank Info</h5>
                    <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Bank" class="text-decoration-none">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <p>Loading Business Bank Information...</p>
            </div>
        </div>
    </div>`;

        let others = `
    <div class="col-md-6  "> 
        <div class=" flex-fill equal-height ">
                <div class="row text-center">
                   
                    <div class="col-md-6 col-6 mb-3">
                        <a href="${baseUrl}/${vendor_id}/business/OnlineProfile/${business_id}" class="text-decoration-none ">
                            <div class="p-2 border rounded">
                                <i class="fas fa-globe fa-lg"></i><br>
                                Online Profile
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-6 ">
                        <a href="${baseUrl}/${vendor_id}/documents/business/${business_id}/manage" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-file-alt fa-lg"></i><br>
                                Documents
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-6">
                        <a href="${baseUrl}/${vendor_id}/${business_id}/media/business" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-photo-video fa-lg"></i><br>
                                Medias
                            </div>
                        </a>
                    </div>
                </div>
            </div>
    </div>
    
    `;

        $("#business-info-cards").html(profileCard + contactCard + addressCard + bankCard + others);

        permanentContact();
        permanentAddress();
        permanentBank();
    }

    function permanentContact() {
        let business_id = "{{ $business_id }}";
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/${business_id}/BusinessContact/Permanent`,
            dataType: "json",
            success: function(response) {
                let html = `
            <div class="d-flex justify-content-between align-items-center my-2">
                <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Contact" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>`;

                if (response.success && response.data) {
                    html += `
                <p><i class="fas fa-phone-square"></i> <strong>Contact Type:</strong> ${safe(response.data.contact_type)}</p>
                <p><i class="fas fa-phone"></i> <strong>Value:</strong> ${safe(response.data.value)}</p>
                <p><i class="fas fa-globe"></i> <strong>Country Code:</strong> ${safe(response.data.country_code)}</p>
                <p><i class="fas fa-tag"></i> <strong>Label:</strong> ${safe(response.data.label)}</p>
                <p><i class="fas fa-exclamation-circle"></i> <strong>Emergency:</strong> ${safe(response.data.emergency)}</p>`;
                } else {
                    html += `<p class='text-muted'>No data found.</p>`;
                }
                $("#business-contact-info .card-body").html(html);
            },
            error: function() {
                $("#business-contact-info .card-body").html(`
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Contact" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <p class='text-muted'>No data found.</p>`);
            }
        });
    }

    function permanentAddress() {
        let business_id = "{{ $business_id }}";
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/${business_id}/Permanat/Business/Address`,
            dataType: "json",
            success: function(response) {
                console.log(response);

                let html = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><i class="fas fa-map-marker-alt"></i> Address Info</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Address" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>`;

                if (response.success && response.data) {
                    html += `
                <p><i class="fas fa-map-marker-alt"></i> <strong>Address Line 1:</strong> ${safe(response.data.line1)}</p>
                <p><i class="fas fa-map-marked-alt"></i> <strong>Address Line 2:</strong> ${safe(response.data.line2)}</p>
                <p><i class="fas fa-city"></i> <strong>City:</strong> ${safe(response.data.city)}</p>
                <p><i class="fas fa-map"></i> <strong>State:</strong> ${safe(response.data.state)}</p>
                <p><i class="fas fa-mail-bulk"></i> <strong>Pincode:</strong> ${safe(response.data.pincode)}</p>
                <p><i class="fas fa-flag"></i> <strong>Country:</strong> ${safe(response.data.country)}</p>`;
                } else {
                    html += "<p class='text-muted'>No data found.</p>";
                }
                $("#business-address-info .card-body").html(html);
            },
            error: function() {
                $("#business-address-info .card-body").html(`
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><i class="fas fa-map-marker-alt"></i> Address Info</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Address" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <p class='text-muted'>No data found.</p>`);
            }
        });
    }

    function permanentBank() {
        let business_id = "{{ $business_id }}";
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/${business_id}/BusinessBank`,
            dataType: "json",
            success: function(response) {


                let html = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><i class="fas fa-university"></i> Bank Info</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Bank" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>`;
                if (response.success && response.data) {
                    html += `
                     <p><i class="fas fa-address-card"></i> <strong>Account Holder : </strong> ${safe(response.data.account_holder)}</p>
                     <p><i class="fas fa-th-large"></i> <strong>Account Method : </strong> ${safe(response.data.method)}</p>
                     <p><i class="fas fa-id-card"></i> <strong>Account Number : </strong> ${safe(response.data.account_number_mask)}</p>
                     <p><i class="fas fa-university"></i> <strong>Bank : </strong> ${safe(response.data.bank_name)}</p>
                     <p><i class="fas fa-landmark"></i> <strong>Branch Name : </strong> ${safe(response.data.branch_name)}</p>
                     <p><i class="far fa-file-alt"></i> <strong>IFSC Code : </strong> ${safe(response.data.ifsc_code)}</p>
                     <p><i class="far fa-file-alt"></i> <strong>SWIFT Code : </strong> ${safe(response.data.swift_code)}</p>
                    `;

                } else {
                    html += "<p class='text-muted'>No data found.</p>";
                }
                $("#business-bank-info .card-body").html(html);
            },
            error: function() {
                $("#business-bank-info .card-body").html(`
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><i class="fas fa-university"></i> Bank Info</h5>
                <a href="${baseUrl}/${vendor_id}/${business_id}/Business/Bank" class="text-decoration-none">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <p class='text-muted'>No data found.</p>`);
            }
        });
    }

    function fetchBusinessDetails() {
        let business_id = "{{ $business_id }}";
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/${business_id}/Business/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success && response.data) {
                    businessProfileInfo({}, response.data);
                } else {
                    $("#business-info-cards").html("<p class='text-danger'>No business profile found.</p>");
                }
            },
            error: function() {
                $("#business-info-cards").html("<p class='text-danger'>Error loading business profile.</p>");
            }
        });
    }



    $(document).ready(function() {
        fetchBusinessDetails();
    });
</script>
@endsection

@section('style')
<style>
    .equal-height {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .equal-height .card-body {
        flex-grow: 1;
    }

    .card-footer {
        background: transparent;
        border-top: none;
    }

    .text-muted {
        font-style: italic;
    }
</style>
@endsection