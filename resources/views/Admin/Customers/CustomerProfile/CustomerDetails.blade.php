@extends('Admin.layout.app')

@section('title', 'Home | Customers | Customer Detail')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Student Detail"
        :links="['Home' => 'Admin.Dashboard', 'Customers' => 'customers.Studentlist','Customer Detail' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="student-details">
                Loading details...
            </div>
        </div>
    </div>

    <section id="primary_info">
        <div class="card">
            <h5 class="card-title d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                Basic information
                <a href="{{ url('students/' . $id . '/Basicinfo/Manage') }}" class="text-primary" data-toggle="tooltip" title="Edit">
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
                <div class="col-md-6 mb-3 info-row"><strong>Caste</strong>
                    <div id="caste">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Current Class</strong>
                    <div id="current_class">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Religion</strong>
                    <div id="religion">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Section</strong>
                    <div id="section">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Mother Tongue</strong>
                    <div id="mother_tongue">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Guardian Name</strong>
                    <div id="guardian_name">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Roll no </strong>
                    <div id="roll_no">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Guardian Relation </strong>
                    <div id="guardian_relation">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Enrollment Status</strong>
                    <div id="enrollment_status">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Guardian Occupation </strong>
                    <div id="guardian_occupation">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Parent Income</strong>
                    <div id="parent_income">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Nationality</strong>
                    <div id="nationality">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Extracurriculars</strong>
                    <div id="extracurriculars">...</div>
                </div>
            </div>

        </div>
    </section>

    <section id="admin_andother_info">
        <div class="card-grid">
            <!-- Card 1 -->
            <div class="card p-2">
                <h6 class=" d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                    Administration information
                    <!-- <a href="#" class="text-primary" data-toggle="tooltip" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a> -->
                </h6>
                <hr style="color:#5156be">
                <div class="mx-2 mb-0">
                    <div class="mb-3 d-flex"><strong class="me-2">Student Name:</strong>
                        <div id="student_f_name">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Student UID:</strong>
                        <div id="student_uid">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Admission Number:</strong>
                        <div id="admission_no">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Admission Date:</strong>
                        <div id="admission_date">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">University Admission No:</strong>
                        <div id="univ_admission_no">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Status:</strong>
                        <div id="admin_status">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Note:</strong>
                        <div id="note">...</div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card p-2">
                
                    <h6 class=" d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                        Address information
                        <a href="{{ url('/students/' . $id . '/Manage/Addresses') }}"
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
                        Address information
                        <a href="{{ url('/students/' . $id . '/Manage/Contacts') }}"
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
    let baseUrl = "{{ url('/students') }}";
    let student_id = "{{ $id }}";

    function primaryinfo(data, info) {
        // Fallback helper
        const safe = (val) => val ? val : 'Not Provided';

        $('#f_name').text(': ' + safe(info.full_name));
        $('#dob').text(': ' + safe(info.dob));
        $('#gender').text(': ' + safe(info.gender));
        $('#caste').text(': ' + safe(info.caste));
        $('#current_class').text(': ' + safe(info.current_class));
        $('#religion').text(': ' + safe(info.religion));
        $('#section').text(': ' + safe(info.section));
        $('#guardian_name').text(': ' + safe(info.guardian_name));
        $('#roll_no').text(': ' + safe(info.roll_no));
        $('#guardian_relation').text(': ' + safe(info.guardian_relation));
        $('#enrollment_status').text(': ' + safe(info.enrollment_status));
        $('#guardian_occupation').text(': ' + safe(info.guardian_occupation));
        $('#parent_income').text(': ' + safe(info.parent_income));
        $('#nationality').text(': ' + safe(info.nationality));
        $('#extracurriculars').text(': ' + safe(info.extracurriculars));
        $('#student_uid').text(': ' + safe(data.student_uid));
        $('#admission_date').text(': ' + safe(data.admission_date));
        $('#admission_no').text(': ' + safe(data.admission_no));
        $('#univ_admission_no').text(': ' + safe(data.univ_admission_no));
        $('#admin_status').text(': ' + safe(data.status));
        $('#note').text(': ' + safe(data.note));
        $('#student_f_name').text(': ' + safe(info.full_name));
        $('#mother_tongue').text(': ' + safe(info.mother_tongue));

    }
    // Fetch student basic info
    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${student_id}/Basicinfo/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    primaryinfo(response.primary_details, response.data);

                    let imgSrc = `/storage/${response.data.avatar_url}`;
                    let manageBankUrl = `/students/${response.data.id}/manageBank`;
                    let manageDocUrl = `/students/${response.data.id}/manageDocument`;
                    let managemediaUrl = `/students/${response.data.id}/Media/manage`;

                    $("#student-details").html(`
                <div class="d-flex align-items-start justify-content-between">
                    <!-- Profile + Info -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 160px;">
                            <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                        </div>
                        <div class="flex-grow-1">
                            <p><strong>UID:</strong> ${response.primary_details.student_uid}</p>
                            <p><strong>Name:</strong> ${response.data.full_name}</p>
                            <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                            <p><strong>Phone:</strong> ${response.primary_details.primary_phone}</p>
                            <p><strong>Admission No:</strong> ${response.primary_details.admission_no}</p>
                            <p><strong>Admission Date:</strong> ${response.primary_details.admission_date}</p>
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
                    $("#student-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#student-details").html(`<p class="text-danger">Something went wrong.</p>`);
                console.error(xhr.responseText);
            }
        });
    }


    function permanentAddress() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${student_id}/Address/Permanent`,
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
            url: `${baseUrl}/${student_id}/Contact/Permanent`,
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

    function studentbanklist() {
        $.ajax({
            url: `${baseUrl}/${student_id}/bank-list`,
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
                                <a href="${baseUrl}/${student_id}/manageBank/${account.id}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteBank(${student_id}, ${account.id})">
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
    function deleteBank(student_id, account_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${student_id}/deleteBank/${account_id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Deleted!', res.message, 'success');
                            studentbanklist(); // Refresh table
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
        studentbanklist();
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