@extends('Admin.layout.app')

@section('title', 'Home | vendors | Details')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="vendor Detail"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.vendorlist','Vendor Detail' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="vendor-details">
                Loading details...
            </div>
        </div>
    </div>

    <section id="primary_info">

        <div class="card">
            <h5 class="card-title d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                Profile information
                <a href="{{ url('vendors/' . $id . '/Basicinfo/Manage') }}" class="text-primary" data-toggle="tooltip" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
            </h5>
            <hr style="color:#5156be">
            <div class="row mx-2 mb-0">
                <div class="col-md-6 mb-3 info-row"><strong>Full Name</strong>
                    <div id="f_name">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Date of Birth</strong>
                    <div id="dob">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Gender</strong>
                    <div id="gender">...</div>
                </div>

                <div class="col-md-6 mb-3 info-row"><strong>Nationality</strong>
                    <div id="nationality">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Occupation</strong>
                    <div id="occupation">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Marital Status</strong>
                    <div id="marital_status">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Prefrred Language</strong>
                    <div id="preferred_language">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Prefrred Currency</strong>
                    <div id="preferred_currency">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Primary Email </strong>
                    <div id="primary_email">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Primary Phone </strong>
                    <div id="primary_phone">...</div>
                </div>


            </div>
            <div class="col-lg-6"></div>
        </div>
    </section>

    <section id="admin_andother_info">
        <div class="card-grid">
            <!-- Card 1 -->
            <div class="card p-2">
                <h6 class=" d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                    Business information
                    <!-- <a href="#" class="text-primary" data-toggle="tooltip" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a> -->
                </h6>
                <hr style="color:#5156be">
                <div class="mx-2 mb-0">
                    <div class="mb-3 d-flex"><strong class="me-2">Vendor Name:</strong>
                        <div id="vendor_f_name">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Leagel Name:</strong>
                        <div id="leagel_name">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Tread Name:</strong>
                        <div id="tread_name">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">industry:</strong>
                        <div id="industry">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Incorporation Date:</strong>
                        <div id="incorporation_date">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Business Size:</strong>
                        <div id="business_size">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Website:</strong>
                        <div id="website">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">GST No:</strong>
                        <div id="gst_number">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">CIN No:</strong>
                        <div id="cin_number">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">PAM No:</strong>
                        <div id="pan_number">...</div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card p-2">

                <h6 class=" d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                    Address information
                    <a href="{{ url('/vendors/' . $id . '/Manage/Addresses') }}"
                        class="text-primary"
                        data-toggle="tooltip"
                        title="Manage Addresses">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </h6>


                <hr style="color:#5156be">
                <div class="mx-2 mb-0">
                    <div class="mb-3 d-flex"><strong class="me-2">Country:</strong>
                        <div id="address_country">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">State:</strong>
                        <div id="state">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">District:</strong>
                        <div id="district">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">City:</strong>
                        <div id="city">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Pincode:</strong>
                        <div id="pincode">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Address 1:</strong>
                        <div id="addr_1">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Address 2:</strong>
                        <div id="addr_2">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Land Mark:</strong>
                        <div id="land_mark">...</div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="card p-2">

                <h6 class=" d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                    Contact information
                    <a href="{{ url('/vendors/' . $id . '/Manage/Contacts') }}"
                        class="text-primary"
                        data-toggle="tooltip"
                        title="Manage Contact">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </h6>
                <hr style="color:#5156be">
                <div class="mx-2 mb-0">
                    <div class="mb-3 d-flex"><strong class="me-2">Contact Type:</strong>
                        <div id="contact_type">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Value:</strong>
                        <div id="contact_value">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Country Code:</strong>
                        <div id="country_code">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Label:</strong>
                        <div id="label">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Emergency:</strong>
                        <div id="is_emergency">...</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


</div>
@endsection


@section('script')
<script>
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";

    function primaryinfo(data, info) {
        console.log(info);

        // Fallback helper
        const safe = (val) => val ? val : 'Not Provided';

        $('#f_name').text(': ' + safe(info.full_name));
        $('#dob').text(': ' + safe(info.dob));
        $('#gender').text(': ' + safe(info.gender));
        $('#nationality').text(': ' + safe(info.nationality));
        $('#marital_status').text(': ' + safe(info.marital_status));
        $('#occupation').text(': ' + safe(info.occupation));
        $('#primary_email').text(': ' + safe(data.primary_email));
        $('#primary_phone').text(': ' + safe(data.primary_phone));
        $('#roll_no').text(': ' + safe(info.roll_no));
        $('#guardian_relation').text(': ' + safe(info.guardian_relation));
        $('#preferred_language').text(': ' + safe(info.preferred_language));
        $('#guardian_occupation').text(': ' + safe(info.guardian_occupation));
        $('#preferred_currency').text(': ' + safe(info.preferred_currency));
        $('#nationality').text(': ' + safe(info.nationality));
        $('#extracurriculars').text(': ' + safe(info.extracurriculars));
        $('#vendor_uid').text(': ' + safe(data.vendor_uid));
        $('#admission_date').text(': ' + safe(data.admission_date));
        $('#admission_no').text(': ' + safe(data.admission_no));
        $('#univ_admission_no').text(': ' + safe(data.univ_admission_no));
        $('#admin_status').text(': ' + safe(data.status));
        $('#note').text(': ' + safe(data.note));
        $('#vendor_f_name').text(': ' + safe(info.full_name));
        $('#mother_tongue').text(': ' + safe(info.mother_tongue));

    }
    // Fetch vendor basic info
    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    primaryinfo(response.primary_details, response.data);

                    let imgSrc = `storage/${response.data.avatar_url}`;
                    let manageBankUrl = `/vendors/${response.data.id}/manageBank`;
                    let manageDocUrl = `/vendors/${response.data.id}/manageDocument`;
                    let managemediaUrl = `/vendors/${response.data.id}/Media/manage`;

                    $("#vendor-details").html(`
                <div class="d-flex align-items-start justify-content-between">
                    <!-- Profile + Info -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 160px;">
                            <img src="{{asset('${imgSrc}')}}" class="img-thumbnail w-100" alt="Profile picture">
                        </div>
                        <div class="flex-grow-1">
                                <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.vendor_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Gender:</strong> ${response.data.gender}</p>
                                <p><strong>Occupation:</strong> ${response.data.occupation}</p>
                                <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status badge on top-right -->
                    <div>
                        <span class="badge ${response.primary_details.status === 'active' ? 'bg-success' : 'bg-danger'}">
                            ${response.primary_details.status}
                        </span>
                    </div>
                </div>

                <!-- Footer with Documents & Media -->
                <div class="d-flex justify-content-end gap-3 mt-3 border-top pt-2">
                    <a href="${manageBankUrl}" class="text-decoration-none">
                        <i class="fas fa-university me-1"></i> Bank Details
                    </a>
                    <a href="${manageDocUrl}" class="text-decoration-none">
                        <i class="fas fa-file-alt me-1"></i> Documents
                    </a>
                    <a href="${managemediaUrl}" class="text-decoration-none">
                        <i class="fas fa-photo-video me-1"></i> Medias
                    </a>
                </div>
            `);

                } else {
                    $("#vendor-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#vendor-details").html(`<p class="text-danger">Something went wrong.</p>`);
                console.error(xhr.responseText);
            }
        });
    }


    function permanentAddress() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Address/Permanent`,
            dataType: "json",
            success: function(response) {
                console.log(response);
                $('#address_country').text(response.data.country)
                $('#state').text(response.data.state)
                $('#district').text(response.data.district)
                $('#city').text(response.data.city)
                $('#pincode').text(response.data.pincode)
                $('#addr_1').text(response.data.line1)
                $('#addr_2').text(response.data.line2)
                $('#land_mark').text(response.data.landmark)
            }
        });
    }

    function permanentContact() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Contact/Permanent`,
            dataType: "json",
            success: function(response) {
                console.log(response);
                $('#contact_type').text(response.data.contact_type)
                $('#contact_value').text(response.data.value)
                $('#country_code').text(response.data.country_code)
                $('#label').text(response.data.label)
                $('#is_emergency').text(response.data.is_emergency == '1' ? 'Yes' : 'No')
                $('#addr_1').text(response.data.line1)
                $('#addr_2').text(response.data.line2)
                $('#land_mark').text(response.data.landmark)
            }
        });
    }

    function vendorbanklist() {
        $.ajax({
            url: `${baseUrl}/${vendor_id}/bank-list`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    let rows = '';
                    $.each(res.data, function(index, account) {
                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${account.method}</td>
                            <td>${account.account_holder || '-'}</td>
                            <td>${account.bank_name || '-'}</td>
                            <td>${account.branch_name || '-'}</td>
                            <td>${account.ifsc_code || '-'}</td>
                            <td>${account.swift_code || '-'}</td>
                            <td>${account.upi_id || '-'}</td>
                            <td>
                                <a href="${baseUrl}/${vendor_id}/manageBank/${account.id}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteBank(${vendor_id}, ${account.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    });
                    $('#bank-list').html(rows);
                }
            }
        });
    }

    // Delete account
    function deleteBank(vendor_id, account_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${vendor_id}/deleteBank/${account_id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Deleted!', res.message, 'success');
                            vendorbanklist(); // Refresh table
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    }
                });
            }
        });
    }

    // Initial loa

    $(document).ready(function() {
        fetchDetails()
        permanentAddress()
        permanentContact()
        vendorbanklist();
    });
</script>
@endsection

@section('style')

<style>
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1rem;
        /* space between cards */
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 10rem;
        /* space between label and value */
    }

    .info-row strong {
        min-width: 160px;
        /* fix width so all values start at same line */
        font-weight: 600;
    }
</style>

@endsection