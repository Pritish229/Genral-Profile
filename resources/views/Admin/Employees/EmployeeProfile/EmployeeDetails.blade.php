@extends('Admin.layout.app')

@section('title', 'Home | Employees | Details')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Employee Details"
        :links="['Home' => 'Admin.Dashboard', 'Employees' => 'employees.Employeelist.all','Employee Details' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="employee-details">
                Loading details...
            </div>
        </div>
    </div>

    <section id="primary_info">
        <div class="card">
            <h5 class="card-title d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                Basic information
                <a href="{{ url('employees/' . $id . '/Basicinfo/Manage') }}" class="text-primary" data-toggle="tooltip" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
            </h5>
            <hr style="color:#5156be">
            <div class="row mx-2 mb-0">
                <div class="col-md-6 mb-3 info-row"><strong>Full Name</strong>
                    <div id="f_name">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Designation</strong>
                    <div id="designation">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Date of Birth</strong>
                    <div id="dob">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Department</strong>
                    <div id="department">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Gender</strong>
                    <div id="gender">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Employee Type</strong>
                    <div id="employment_type">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Location</strong>
                    <div id="location">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Base Salary</strong>
                    <div id="base_salary">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Blood Group</strong>
                    <div id="blood_group">...</div>
                </div>

                <div class="col-md-6 mb-3 info-row"><strong>Experience Years</strong>
                    <div id="experience_years">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Emergency Contact Name</strong>
                    <div id="emergency_contact_name">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Emergency Contact</strong>
                    <div id="emergency_contact_phone">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Emergency Relation</strong>
                    <div id="emergency_relation">...</div>
                </div>
                <div class="col-md-6 mb-3 info-row"><strong>Skills</strong>
                    <div id="skills">...</div>
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
                    <div class="mb-3 d-flex"><strong class="me-2">Employee Name:</strong>
                        <div id="employee_f_name">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Employee UID:</strong>
                        <div id="employee_uid">...</div>
                    </div>
                    <div class="mb-3 d-flex"><strong class="me-2">Hire Date:</strong>
                        <div id="hire_date">...</div>
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
                    <a href="{{ url('/employees/' . $id . '/Manage/Addresses') }}"
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
                    <a href="{{ url('/employees/' . $id . '/Manage/Contacts') }}"
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
    let baseUrl = "{{ url('/employees') }}";
    let employee_id = "{{ $id }}";

    function primaryinfo(data, info) {
        console.log(data);
        const safe = (val) => val ? val : 'Not Provided';

        $('#f_name').text(': ' + safe(info.full_name));
        $('#dob').text(': ' + safe(info.dob));
        $('#gender').text(': ' + safe(info.gender));
        $('#blood_group').text(': ' + safe(info.blood_group));
        $('#designation').text(': ' + safe(info.designation));
        $('#department').text(': ' + safe(info.department));
        $('#employment_type').text(': ' + safe(info.employment_type));
        $('#experience_years').text(': ' + safe(info.experience_years));
        $('#emergency_contact_name').text(': ' + safe(info.emergency_contact_name));
        $('#emergency_contact_phone').text(': ' + safe(info.emergency_contact_phone));
        $('#emergency_relation').text(': ' + safe(info.emergency_relation));
        $('#base_salary').text(': ' + safe(info.base_salary + '' + info.salary_currency));

        $('#employee_uid').text(': ' + safe(data.employee_uid));
        $('#hire_date').text(': ' + safe(data.hire_date));
        $('#admin_status').text(': ' + safe(data.status));
        $('#employee_f_name').text(': ' + safe(info.full_name));
        $('#note').text(': ' + safe(data.note));
        let skills = Array.isArray(info.skills) ? info.skills : JSON.parse(info.skills || '[]');
$('#skills').text(': ' + skills.join(', '));


    }
    // Fetch employee basic info
    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${employee_id}/Basicinfo/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    primaryinfo(response.primary_details, response.data);

                    let imgSrc = `/storage/${response.data.avatar_url}`;
                    let manageBankUrl = `/employees/${response.data.id}/manageBank`;
                    let manageDocUrl = `/employees/${response.data.id}/manageDocument`;
                    let managemediaUrl = `/employees/${response.data.id}/Media/manage`;

                    $("#employee-details").html(`
                <div class="d-flex align-items-start justify-content-between">
                    <!-- Profile + Info -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 160px;">
                            <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                        </div>
                        <div class="flex-grow-1">
                            <p><strong>UID:</strong> ${response.primary_details.employee_uid}</p>
                            <p><strong>Name:</strong> ${response.data.full_name}</p>
                            <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                            <p><strong>Phone:</strong> ${response.primary_details.primary_phone}</p>
                            <p><strong>Hire Date:</strong> ${response.primary_details.hire_date}</p>
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
                    $("#employee-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#employee-details").html(`<p class="text-danger">Something went wrong.</p>`);
                console.error(xhr.responseText);
            }
        });
    }


    function permanentAddress() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${employee_id}/Address/Permanent`,
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
            url: `${baseUrl}/${employee_id}/Contact/Permanent`,
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


    // Initial loa

    $(document).ready(function() {
        fetchDetails()
        permanentAddress()
        permanentContact()
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