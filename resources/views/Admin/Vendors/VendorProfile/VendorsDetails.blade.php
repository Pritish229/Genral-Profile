@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Vendor Detail')

@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
@endsection

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Vendor Detail"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.vendorlist','Vendor Detail' => '']" />

    <div id="vendor-details" class="p-2 card mb-3">Loading Profile...</div>

    <div class="mt-2">
        <div class="p-3">
            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="vendorTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                        Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab">
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
                    <div id="vendor-business-details">Loading Business Profile...</div>
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

function safe(val) {
    return val ? val : 'Not Provided';
}

function primaryinfo(data, info) {
    let profileCard = `
        <div class="col-md-6 mb-3 d-flex">
            <div class="card p-3 flex-fill">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-user"></i> Personal Details</h5>
                    <a href="#" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                </div>
                <p><i class="fas fa-id-card"></i> <strong>Full Name:</strong> ${safe(info.full_name)}</p>
                <p><i class="fas fa-birthday-cake"></i> <strong>Date of Birth:</strong> ${safe(info.dob)}</p>
                <p><i class="fas fa-venus-mars"></i> <strong>Gender:</strong> ${safe(info.gender)}</p>
                <p><i class="fas fa-flag"></i> <strong>Nationality:</strong> ${safe(info.nationality)}</p>
                <p><i class="fas fa-briefcase"></i> <strong>Occupation:</strong> ${safe(info.occupation)}</p>
                <p><i class="fas fa-heart"></i> <strong>Marital Status:</strong> ${safe(info.marital_status)}</p>
                <p><i class="fas fa-language"></i> <strong>Preferred Language:</strong> ${safe(info.preferred_language)}</p>
                <p><i class="fas fa-coins"></i> <strong>Preferred Currency:</strong> ${safe(info.preferred_currency)}</p>
            </div>
        </div>
    `;

    let contactCard = `
        <div class="col-md-6 mb-3 d-flex">
            <div class="card p-3 flex-fill" id="contact-info">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                    <a href="#" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                </div>
                <p>Loading Contact Information...</p>
            </div>
        </div>
    `;

    let addressCard = `
        <div class="col-md-6 mb-3 d-flex">
            <div class="card p-3 flex-fill" id="address-info">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-map-marker-alt"></i> Permanent Address</h5>
                    <a href="#" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                </div>
                <p>Loading Address Information...</p>
            </div>
        </div>
    `;

    $("#primary-info-cards").html(profileCard + contactCard + addressCard);

    permanentContact(vendorType);
    permanentAddress(vendorType);
}

function permanentContact(type) {
    $.ajax({
        type: "GET",
        url: `${baseUrl}/${vendor_id}/${type}/Contact/Permanent`,
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                let contactHtml = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                        <a href="#" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <p><i class="fas fa-phone-square"></i> <strong>Contact Type:</strong> ${safe(response.data.contact_type)}</p>
                    <p><i class="fas fa-phone"></i> <strong>Value:</strong> ${safe(response.data.contact_value)}</p>
                    <p><i class="fas fa-globe"></i> <strong>Country Code:</strong> ${safe(response.data.country_code)}</p>
                    <p><i class="fas fa-tag"></i> <strong>Label:</strong> ${safe(response.data.contact_label)}</p>
                    <p><i class="fas fa-exclamation-circle"></i> <strong>Emergency:</strong> ${safe(response.data.emergency)}</p>
                `;
                $("#contact-info").html(contactHtml);
            } else {
                $("#contact-info").html("<p class='text-danger'>No contact information found.</p>");
            }
        },
        error: function(xhr) {
            $("#contact-info").html("<p class='text-danger'>Error loading contact information.</p>");
        }
    });
}

function permanentAddress(type) {
    $.ajax({
        type: "GET",
        url: `${baseUrl}/${vendor_id}/${type}/Address/Permanent`,
        dataType: "json",
        success: function(response) {
            console.log(response);
            
            if (response.success && response.data) {
                let address = response.data;
                let addressHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-address-book"></i> Address Info</h5>
                        <a href="#" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                    <p><strong>Address Line 1:</strong> ${safe(address.line1)}</p>
                    <p><strong>Address Line 2:</strong> ${safe(address.line2)}</p>
                    <p><strong>City:</strong> ${safe(address.city)}</p>
                    <p><strong>State:</strong> ${safe(address.state)}</p>
                    <p><strong>Pincode:</strong> ${safe(address.pincode)}</p>
                    <p><strong>Country:</strong> ${safe(address.country)}</p>
                `;
                $("#address-info").html(addressHtml);
            } else {
                $("#address-info").html("<p class='text-danger'>No address information found.</p>");
            }
        },
        error: function(xhr) {
            $("#address-info").html("<p class='text-danger'>Error loading address information.</p>");
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
            if (response.success) {
                $("#vendor-business-details").html(`<pre>${JSON.stringify(response.data, null, 2)}</pre>`);
            } else {
                $("#vendor-business-details").html("<p class='text-danger'>No business profile found.</p>");
            }
        },
        error: function(xhr) {
            $("#vendor-business-details").html("<p class='text-danger'>Error loading business profile.</p>");
        }
    });
}

$(document).ready(function() {
    fetchDetails();

    $('#vendorTab button').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr("data-bs-target");
        if (target === "#profile") {
            vendorType = "individual";
        } else if (target === "#business") {
            vendorType = "business";
            fetchBusinessDetails();
        }
        permanentContact(vendorType);
    });
});
</script>
@endsection
