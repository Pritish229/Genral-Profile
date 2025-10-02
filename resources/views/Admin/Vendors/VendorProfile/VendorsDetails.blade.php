@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Vendor Detail')



@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Vendor Details"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => '',
    ]" />


    <div id="vendor-details" class="p-2 card mb-3">Loading Profile...</div>

    <div class="mt-2">
        <div class="p-3">
            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="vendorTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                        Individual Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link disabled" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab" disabled>
                        Business Profile
                    </button>
                </li>
            </ul>

            <!-- Tab contents -->
            <div class="tab-content" id="vendorTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="row align-items-stretch" id="primary-info-cards">
                        Loading Profile Information...
                    </div>
                </div>

                <div class="tab-pane fade" id="business" role="tabpanel">
                    <div class="row align-items-stretch" id="business-info-cards">
                        Loading Business Profile Information...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";
    let vendorType = "individual";
    let currentVendorType = null; // Will be set from API response

    function safe(val) {
        return val ? val : 'Not Provided';
    }

    function primaryinfo(data, info) {
        let profileCard = `
    <div class="col-12 mb-3">
        <div class="card p-3 flex-fill">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-user"></i> Personal Details</h5>
                <a href="${baseUrl}/${vendor_id}/manage" class="text-decoration-none"><i class="fas fa-edit"></i></a>
            </div>
            <div class="row">
                <div class="col-md-3"><p><i class="fas fa-id-card"></i> <strong>Full Name:</strong> ${safe(info.full_name)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-birthday-cake"></i> <strong>DOB:</strong> ${safe(info.dob)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-venus-mars"></i> <strong>Gender:</strong> ${safe(info.gender)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-flag"></i> <strong>Nationality:</strong> ${safe(info.nationality)}</p></div>

                <div class="col-md-3"><p><i class="fas fa-briefcase"></i> <strong>Occupation:</strong> ${safe(info.occupation)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-heart"></i> <strong>Marital Status:</strong> ${safe(info.marital_status)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-language"></i> <strong>Language:</strong> ${safe(info.preferred_language)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-coins"></i> <strong>Currency:</strong> ${safe(info.preferred_currency)}</p></div>
            </div>

            <!-- Card Footer Links -->
            <div class="card-footer mt-3">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/${vendorType}/BankDetails" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-university fa-lg"></i><br>
                                Bank Details
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/${vendorType}/OnlineProfile/Manage" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-globe fa-lg"></i><br>
                                Online Profile
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/${vendorType}/Documents" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-file-alt fa-lg"></i><br>
                                Documents
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/${vendorType}/Medias" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-photo-video fa-lg"></i><br>
                                Medias
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;

        let contactCard = `
            <div class="col-md-6 mb-3 d-flex">
                <div class="card p-3 flex-fill equal-height" id="contact-info">
                    
                    <div class="card-body">
                        <p>Loading Contact Information...</p>
                    </div>
                </div>
            </div>
            `;

        let addressCard = `
            <div class="col-md-6 mb-3 d-flex">
                <div class="card p-3 flex-fill equal-height" id="address-info">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-map-marker-alt"></i> Permanent Address</h5>
                        <a href="${baseUrl}/${vendor_id}/${vendorType}/Manage/Address" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <div class="card-body">
                        <p>Loading Address Information...</p>
                    </div>
                </div>
            </div>
            `;

        $("#primary-info-cards").html(profileCard + contactCard + addressCard);

        permanentContact(vendorType);
        permanentAddress(vendorType);
    }

    function businessProfileInfo(data, info) {
    console.log(info);
    
        
        let profileCard = `
    <div class="col-12 mb-3">
        <div class="card p-3 flex-fill">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-building"></i> Business Details</h5>
                <a href="${baseUrl}/${vendor_id}/business/manage" class="text-decoration-none"><i class="fas fa-edit"></i></a>
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
                <div class="col-md-3"><p><i class="fas fa-envelope"></i> <strong>Billing Email:</strong> ${safe(info.billing_email)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-phone"></i> <strong>Primary Contact Phone:</strong> ${safe(info.primary_contact_phone)}</p></div>
                <div class="col-md-3"><p><i class="fas fa-phone"></i> <strong>Billing Phone:</strong> ${safe(info.billing_phone)}</p></div>
            
            </div>

            <!-- Card Footer Links -->
            <div class="card-footer mt-3">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/business/BankDetails" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-university fa-lg"></i><br>
                                Bank Details
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/business/OnlineProfile/Manage" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-globe fa-lg"></i><br>
                                Online Profile
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/business/Documents" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-file-alt fa-lg"></i><br>
                                Documents
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <a href="${baseUrl}/${vendor_id}/business/Medias" class="text-decoration-none">
                            <div class="p-2 border rounded">
                                <i class="fas fa-photo-video fa-lg"></i><br>
                                Medias
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;

        let contactCard = `
            <div class="col-md-6 mb-3 d-flex">
                <div class="card p-3 flex-fill equal-height" id="business-contact-info">
                    
                    <div class="card-body">
                        <p>Loading Business Contact Information...</p>
                    </div>
                </div>
            </div>
            `;

        let addressCard = `
            <div class="col-md-6 mb-3 d-flex">
                <div class="card p-3 flex-fill equal-height" id="business-address-info">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-map-marker-alt"></i> Business Address</h5>
                        <a href="${baseUrl}/${vendor_id}/business/Manage/Address" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <div class="card-body">
                        <p>Loading Business Address Information...</p>
                    </div>
                </div>
            </div>
            `;

        $("#business-info-cards").html(profileCard + contactCard + addressCard);

        permanentContact("business");
        permanentAddress("business");
    }

    function permanentContact(type) {
        const contactInfoId = type === 'business' ? '#business-contact-info' : '#contact-info';

        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/${type}/Contact/Permanent`,
            dataType: "json",
            success: function(response) {
                if (response.success && response.data) {
                    let contactHtml = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                        <a href="${baseUrl}/${vendor_id}/${type}/Manage/Contacts" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <p><i class="fas fa-phone-square"></i> <strong>Contact Type:</strong> ${safe(response.data.contact_type)}</p>
                    <p><i class="fas fa-phone"></i> <strong>Value:</strong> ${safe(response.data.contact_value)}</p>
                    <p><i class="fas fa-globe"></i> <strong>Country Code:</strong> ${safe(response.data.country_code)}</p>
                    <p><i class="fas fa-tag"></i> <strong>Label:</strong> ${safe(response.data.contact_label)}</p>
                    <p><i class="fas fa-exclamation-circle"></i> <strong>Emergency:</strong> ${safe(response.data.emergency)}</p>
                `;
                    $(contactInfoId).html(contactHtml);
                } else {
                    $(contactInfoId).html(`
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                            <a href="${baseUrl}/${vendor_id}/${type}/Manage/Contacts" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                        </div>
                        <p class='text-muted'>No contact information provided.</p>
                    `);
                }
            },
            error: function(xhr) {
                $(contactInfoId).html(`
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                        <a href="${baseUrl}/${vendor_id}/${type}/Manage/Contacts" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <p class='text-muted'>No contact information provided.</p>
                `);
            }
        });
    }

    function permanentAddress(type) {
        const addressInfoId = type === 'business' ? '#business-address-info' : '#address-info';

        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/${type}/Address/Permanent`,
            dataType: "json",
            success: function(response) {
                if (response.success && response.data) {
                    let address = response.data;
                    let addressHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-address-book"></i> Address Info</h5>
                    <a href="${baseUrl}/${vendor_id}/${type}/Manage/Address" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                </div>
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Address Line 1:</strong> ${safe(address.line1)}</p>
                    <p><i class="fas fa-map-marked-alt"></i> <strong>Address Line 2:</strong> ${safe(address.line2)}</p>
                    <p><i class="fas fa-city"></i> <strong>City:</strong> ${safe(address.city)}</p>
                    <p><i class="fas fa-map"></i> <strong>State:</strong> ${safe(address.state)}</p>
                    <p><i class="fas fa-mail-bulk"></i> <strong>Pincode:</strong> ${safe(address.pincode)}</p>
                    <p><i class="fas fa-flag"></i> <strong>Country:</strong> ${safe(address.country)}</p>
                `;
                    $(addressInfoId).html(addressHtml);
                } else {
                    $(addressInfoId).html(`
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5><i class="fas fa-address-book"></i> Address Info</h5>
                            <a href="${baseUrl}/${vendor_id}/${type}/Manage/Address" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                        </div>
                        <p class='text-muted'>No address information provided.</p>
                    `);
                }
            },
            error: function(xhr) {
                $(addressInfoId).html(`
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-address-book"></i> Address Info</h5>
                        <a href="${baseUrl}/${vendor_id}/${type}/Manage/Address" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <p class='text-muted'>No address information provided.</p>
                `);
            }
        });
    }

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    // Store the vendor type
                    currentVendorType = response.primary_details.type;

                    // Enable/disable business tab based on vendor type
                    if (currentVendorType === 'business') {
                        $('#business-tab').removeClass('disabled').removeAttr('disabled');
                    } else {
                        $('#business-tab').addClass('disabled').attr('disabled', true);
                    }

                    let imgSrc = response.data.avatar_url ? `{{ asset('storage') }}/${response.data.avatar_url}` : "{{ asset('images/default.png') }}";
                    $("#vendor-details").html(`
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex: 0 0 160px;">
                                <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                            </div>
                            <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${safe(response.primary_details.vendor_uid)}</p>
                                <p><strong>Name:</strong> ${safe(response.data.full_name)}</p>
                                <p><strong>Type:</strong> ${safe(response.primary_details.type).toUpperCase()}</p>
                                <p><strong>Gender:</strong> ${safe(response.data.gender)}</p>
                                <p><strong>Occupation:</strong> ${safe(response.data.occupation)}</p>
                                <p><strong>Email:</strong> ${safe(response.primary_details.primary_email)}</p>
                            </div>
                        </div>
                        <div>
                            <span class="badge ${response.primary_details.status === 'active' ? 'bg-success' : 'bg-danger'}">
                                ${safe(response.primary_details.status)}
                            </span>
                        </div>
                    </div>
                `);

                    primaryinfo(response.primary_details, response.data);
                } else {
                    $("#vendor-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#vendor-details").html("<p class='text-danger'>Something went wrong.</p>");
            }
        });
    }

    function fetchBusinessDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Business`,
            dataType: "json",
            success: function(response) {
                if (response.success && response.data) {
                    // Create a mock primary_details object for consistency
                    const mockPrimaryDetails = {
                        vendor_uid: currentVendorType === 'business' ? 'Business Vendor' : 'N/A',
                        primary_email: response.data.primary_contact_email || 'N/A',
                        status: 'active'
                    };

                    businessProfileInfo(mockPrimaryDetails, response.data);
                } else {
                    $("#business-info-cards").html("<p class='text-danger'>No business profile found.</p>");
                }
            },
            error: function(xhr) {
                $("#business-info-cards").html("<p class='text-danger'>Error loading business profile.</p>");
            }
        });
    }

    $(document).ready(function() {
        fetchDetails();

        $('#vendorTab button').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr("data-bs-target");
            if (target === "#profile") {
                vendorType = "individual";
                // Refresh individual profile data
                permanentContact(vendorType);
                permanentAddress(vendorType);
            } else if (target === "#business") {
                vendorType = "business";
                // Only fetch business details if vendor type is business
                if (currentVendorType === 'business') {
                    fetchBusinessDetails();
                } else {
                    $("#business-info-cards").html("<p class='text-warning'>This vendor does not have a business profile.</p>");
                }
            }
        });
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
</style>

@endsection