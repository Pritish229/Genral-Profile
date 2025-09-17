@extends('Admin.layout.app')

@section('title', 'Home | employees | Address')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Address"
        :links="['Home' => 'Admin.Dashboard', 'Employees' => 'employees.employeelist', 'Address' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-1" id="employee-details">
                Loading details...
            </div>
        </div>
    </div>

    <h5>Add Permanent Address</h5>
    <hr style="color:#5156be">

    <div id="alert-box" class="mt-2"></div>

    <!-- Progress bar -->
    <div class="progress mb-3" style="height: 25px; display:none;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="progressBar">0%</div>
    </div>

    <form id="employeeAddressForm">
        <div class="row">
            <div class="col-md-3">
                <x-inputbox id="state" label="State" type="text" placeholder="Enter State Name" name="state"
                    value="{{ old('state') }}" :required="false" helpertxt="State Name Max 120 character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="district" label="District" type="text" placeholder="Enter District Name" name="district"
                    value="{{ old('district') }}" :required="false" helpertxt="District Name Max 120 character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="city" label="City" type="text" placeholder="Enter City Name" name="city"
                    value="{{ old('city') }}" :required="false" helpertxt="City Name Max 120 character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="pincode" label="Pincode" type="text" placeholder="Enter Pincode" name="pincode"
                    value="{{ old('pincode') }}" :required="false" helpertxt="Pincode must be 6 digits" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="line_1" label="Line 1" type="text" placeholder="Enter Line 1" name="line_1"
                    value="{{ old('line_1') }}" :required="false" helpertxt="Line 1 Name Max 120 character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="line_2" label="Line 2" type="text" placeholder="Enter Line 2" name="line_2"
                    value="{{ old('line_2') }}" :required="false" helpertxt="Line 2 Name Max 120 character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="landmark" label="Landmark" type="text" placeholder="Enter Landmark" name="landmark"
                    value="{{ old('landmark') }}" :required="false" helpertxt="Landmark Max 150 character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Ex: Parents Address" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="longitude" label="Longitude (Optional)" type="text" placeholder="Enter Longitude Code" name="longitude"
                    value="{{ old('longitude') }}" :required="false" helpertxt="Ex: 19.32642" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="latitude" label="Latitude (Optional)" type="text" placeholder="Enter Latitude Code" name="latitude"
                    value="{{ old('latitude') }}" :required="false" helpertxt="Ex: 19.32642" />
            </div>
            <div class="col-lg-12">
                <button type="submit" class="btn btn-primary">Save & Continue</button>
            </div>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
    let baseUrl = "{{ url('/employees') }}";
    let employee_id = "{{ $id }}";

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${employee_id}/Basicinfo/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    console.log(response);

                    let imgSrc = `/storage/${response.data.avatar_url}`;
                    $("#employee-details").html(`
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex: 0 0 150px;">
                                <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                            </div>
                            <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.employee_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Status:</strong> ${response.primary_details.status}</p>
                            </div>
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

    function updateProgress(value) {
        $("#progressContainer").show();
        $("#progressBar").css("width", value + "%").text(value + "%");
    }

    $(document).ready(function() {
        fetchDetails();

        // Show progress at 30% for Address step
        updateProgress(30);

        $("#employeeAddressForm").on("submit", function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the address.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                type: "POST",
                url: `${baseUrl}/${employee_id}/Manage/Addresses`,
                data: $(this).serialize(),
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        $("#alert-box").html("");
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${employee_id}/Contact`;
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let html = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(key, value) {
                            html += '<li>' + value[0] + '</li>';
                        });
                        html += '</ul></div>';
                        $("#alert-box").html(html);
                    } else {
                        Swal.fire("Error", "Something went wrong.", "error");
                        console.error(xhr.responseText);
                    }
                }
            });
        });

        // Skip button
        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `${baseUrl}/${employee_id}/Contact`;
            });
        });
    });
</script>
@endsection