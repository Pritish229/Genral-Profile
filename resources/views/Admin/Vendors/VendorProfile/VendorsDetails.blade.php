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
                    <a href="{{ url('vendors/' . $id . '/Businesslist') }}" class="nav-link text-primary">
                        Business Link
                    </a>
                </li>
            </ul>

            <!-- Tab contents -->
            <div class="tab-content" id="vendorTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="row align-items-stretch" id="primary-info-cards">
                        Loading Profile Information...
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
                </div>

                <div class="row">
                    <div class="col-md-3"><p><i class="fas fa-briefcase"></i> <strong>Occupation:</strong> ${safe(info.occupation)}</p></div>
                    <div class="col-md-3"><p><i class="fas fa-heart"></i> <strong>Marital Status:</strong> ${safe(info.marital_status)}</p></div>
                    <div class="col-md-3"><p><i class="fas fa-language"></i> <strong>Language:</strong> ${safe(info.preferred_language)}</p></div>
                    <div class="col-md-3"><p><i class="fas fa-coins"></i> <strong>Currency:</strong> ${safe(info.preferred_currency)}</p></div>
                </div>

                <div class="card-footer mt-3">
                    
                </div>
                <div class="row text-center">
                       
                        <div class="col-md-4 col-6 mb-2">
                            <a href="${baseUrl}/${vendor_id}/individual/OnlineProfile/Manage" class="text-decoration-none">
                                <div class="p-2 border rounded">
                                    <i class="fas fa-globe fa-lg"></i><br>Online Profile
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                            <a href="${baseUrl}/${vendor_id}/documents/individual/manage" class="text-decoration-none">
                                <div class="p-2 border rounded">
                                    <i class="fas fa-file-alt fa-lg"></i><br>Documents
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                            <a href="${baseUrl}/${vendor_id}/media/manage" class="text-decoration-none">
                                <div class="p-2 border rounded">
                                    <i class="fas fa-photo-video fa-lg"></i><br>Medias
                                </div>
                            </a>
                        </div>
                    </div>
            </div>
        </div>`;

        let contactCard = `
        <div class="col-md-6 mb-3 d-flex">
            <div class="card p-3 flex-fill equal-height" id="contact-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                        <a href="${baseUrl}/${vendor_id}/Manage/Contacts" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                    </div>
                <div class="card-body"><p>Loading Contact Information...</p></div>
            </div>
        </div>`;

        let addressCard = `
        <div class="col-md-6 mb-3 d-flex">
            <div class="card p-3 flex-fill equal-height" id="address-info">
                <div class="card-body"><p>Loading Address Information...</p></div>
            </div>
        </div>`;

        let bankCard = `
        <div class="col-md-6 mb-3 d-flex">
            <div class="card p-3 flex-fill equal-height" id="bank-info">
                <div class="card-body"><p>Loading Bank Information...</p></div>
            </div>
        </div>`;

        $("#primary-info-cards").html(profileCard + contactCard + addressCard + bankCard);

        permanentContact();
        permanentAddress(vendorType);
        permanentBank();
    }

    function permanentContact() {
        const contactInfoId = '#contact-info';
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Contact/Permanent`,
            dataType: "json",
            success: function(res) {
                console.log(res);
                
                let contactHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                    <a href="${baseUrl}/${vendor_id}/Manage/Contacts" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                </div>
            `;

                if (res.success && res.data) {
                    let d = res.data;
                    contactHtml += `
                    <p><i class="fas fa-phone-square"></i> <strong>Contact Type:</strong> ${safe(d.contact_type)}</p>
                    <p><i class="fas fa-phone"></i> <strong>Value:</strong> ${safe(d.value)}</p>
                    <p><i class="fas fa-globe"></i> <strong>Country Code:</strong> ${safe(d.country_code)}</p>
                    <p><i class="fas fa-tag"></i> <strong>Label:</strong> ${safe(d.label)}</p>
                    <p><i class="fas fa-exclamation-circle"></i> <strong>Emergency:</strong> ${safe(d.emergency)}</p>`;
                } else {
                    contactHtml += `<p class='text-muted mb-0'>No contact information provided.</p>`;
                }

                $(contactInfoId).html(contactHtml);
            },
            error: function() {
                let contactHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-address-book"></i> Contact Info</h5>
                    <a href="${baseUrl}/${vendor_id}/Manage/Contacts" class="text-decoration-none"><i class="fas fa-edit"></i></a>
                </div>
                <p class='text-muted mb-0'>No contact information provided.</p>`;
                $(contactInfoId).html(contactHtml);
            }
        });
    }

    function permanentAddress(type) {
        const addressInfoId = '#address-info';
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Address/Permanent`,
            dataType: "json",
            success: function(res) {
                let addressHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-map-marker-alt"></i> Address Info</h5>
                    <a href="${baseUrl}/${vendor_id}/Manage/Address" class="text-decoration-none">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            `;

                if (res.success && res.data) {
                    let d = res.data;
                    addressHtml += `
                    <p><strong>Address Line 1:</strong> ${safe(d.line1)}</p>
                    <p><strong>City:</strong> ${safe(d.city)}</p>
                    <p><strong>State:</strong> ${safe(d.state)}</p>
                    <p><strong>Pincode:</strong> ${safe(d.pincode)}</p>
                    <p><strong>Country:</strong> ${safe(d.country)}</p>`;
                } else {
                    addressHtml += `<p class='text-muted mb-0'>No address information provided.</p>`;
                }

                $(addressInfoId).html(addressHtml);
            },
            error: function() {
                let fallbackHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-map-marker-alt"></i> Address Info</h5>
                    <a href="${baseUrl}/${vendor_id}/Manage/Address" class="text-decoration-none">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <p class='text-muted mb-0'>No address information provided.</p>`;
                $(addressInfoId).html(fallbackHtml);
            }
        });
    }

    function permanentBank() {
        const bankInfoId = '#bank-info';
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Permanent/BankDetails`,
            dataType: "json",
            success: function(res) {
                console.log(res);
                
                let bankHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                <h5><i class="fas fa-university"></i> Bank Details</h5>
                <a href="${baseUrl}/${vendor_id}/Manage/Bank" class="text-decoration-none">
                <i class="fas fa-edit"></i>
                </a>
                </div>
                `;
                

                if (res.success && res.data) {
                    
                    let b = res.data;
                    bankHtml += `
                    <p><strong>Account Holder:</strong> ${safe(b.account_holder)}</p>
                    <p><strong>Account Method:</strong> ${safe(b.method)}</p>
                    <p><strong>Account Number:</strong> ${safe(b.account_number)}</p>
                    <p><strong>Bank:</strong> ${safe(b.bank_name)}</p>
                    <p><strong>Branch Name:</strong> ${safe(b.branch_name)}</p>
                    <p><strong>IFSC Code:</strong> ${safe(b.ifsc_code)}</p>
                    <p><strong>SWIFT Code:</strong> ${safe(b.swift_code)}</p>`;
                } else {
                    bankHtml += `<p class='text-muted mb-0'>No bank details available.</p>`;
                }

                $(bankInfoId).html(bankHtml);
            },
            error: function() {
                let fallbackHtml = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5><i class="fas fa-university"></i> Bank Details</h5>
                    <a href="${baseUrl}/${vendor_id}/Manage/Bank" class="text-decoration-none">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <p class='text-muted mb-0'>No bank details available.</p>`;
                $(bankInfoId).html(fallbackHtml);
            }
        });
    }

    function fetchDetails() {
        $.get(`${baseUrl}/${vendor_id}/Details`, function(res) {
            if (res.success) {
                let img = res.data.avatar_url ? `{{ asset('storage') }}/${res.data.avatar_url}` : "{{ asset('images/default.png') }}";
                $("#vendor-details").html(`
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start gap-3">
                        <img src="${img}" class="img-thumbnail" style="width:160px;">
                        <div>
                            <p><strong>UID:</strong> ${safe(res.primary_details.vendor_uid)}</p>
                            <p><strong>Name:</strong> ${safe(res.data.full_name)}</p>
                            <p><strong>Type:</strong> ${safe(res.primary_details.type).toUpperCase()}</p>
                            <p><strong>Email:</strong> ${safe(res.primary_details.primary_email)}</p>
                        </div>
                    </div>
                    <span class="badge ${res.primary_details.status === 'active' ? 'bg-success' : 'bg-danger'}">
                        ${safe(res.primary_details.status)}
                    </span>
                </div>`);
                primaryinfo(res.primary_details, res.data);
            } else {
                $("#vendor-details").html("<p class='text-danger'>Unable to load details.</p>");
            }
        });
    }

    $(document).ready(fetchDetails);
</script>
@endsection