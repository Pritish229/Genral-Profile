@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Business info')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Basic info"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.Vendorslist', 'Business info' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-1" id="student-details">
                Loading details...
            </div>
        </div>
    </div>

    <h5>Add More</h5>
    <hr style="color:#5156be">

    <!-- Progress bar -->
    <div class="progress mb-3" style="height: 25px;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="progress-bar">0%</div>
    </div>

    <form id="studentForm">
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
                    <label for="onboarding_channel" class="mb-2 labeltxt">Business Size</label>
                    <select name="onboarding_channel" class="form-select" id="onboarding_channel">
                        <option value="micro">Micro</option>
                        <option value="sme">Sme</option>
                        <option value="enrterprice">Enrterprice</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Business Size</small>
                </div>
            </div>
            <div class="col-md-6">
                <x-inputbox id="website" label="Website URL" type="text" placeholder="Enter Website URL" name="website"
                    value="{{ old('website') }}" :required="false" helpertxt="Max 200 Characters" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="primary_contact_name" label="Primary Contact name" type="text" placeholder="Enter Contact name" name="primary_contact_name"
                    value="{{ old('primary_contact_name') }}" :required="false" helpertxt="Primary Contact max 100 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="primary_contact_email" label="Primary Contact Email" type="email" placeholder="Enter Primary Contact Email" name="primary_contact_email"
                    value="{{ old('primary_contact_email') }}" :required="false" helpertxt="Primary Contact Email Max 120 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="primary_contact_phone" label="Primary Contact Email" type="text" placeholder="Enter Primary Contact Email" name="primary_contact_phone"
                    value="{{ old('primary_contact_phone') }}" :required="false" helpertxt="Primary Contact Number Max 30 Digits" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="billing_email" label="Billing Email" type="email" placeholder="Enter Billing Email" name="billing_email"
                    value="{{ old('billing_email') }}" :required="false" helpertxt="Billing Email Max 120 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="billing_phone" label="Billing Phone" type="email" placeholder="Enter Billing Phone" name="billing_phone"
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
                    value="{{ old('credit_limit') }}" :required="false" helpertxt="Must Be Number" />
            </div>
            <div class="col-md-3">
            <div class="mb-2">
              <label for="dob" class="mb-2 labeltxt">Payment Terms Date</label>
              <input type="text" id="payment_terms_days" name="payment_terms_days" class="form-control flatpickr"
                placeholder="Select Payment Terms Date" value="{{ old('payment_terms_days') }}">
              <small class="mb-3 pt-1 helpertxt">Must be a Date</small>
            </div>
          </div>
            <div class="col-md-3">
                <x-inputbox id="account_manager" label="Account Manager" type="text" placeholder="Enter Account Manager " name="account_manager"
                    value="{{ old('account_manager') }}" :required="false" helpertxt="Max 120 Charcter" />
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save & Continue</button>
            <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/students') }}";
    let student_id = "{{ $id }}";

    // --- Progress Helpers ---
    function setProgress(value) {
        localStorage.setItem("studentProgress", value);
        $('#progress-bar').css('width', value + '%').text(value + '%');
    }

    function getProgress() {
        return parseInt(localStorage.getItem("studentProgress") || 0, 10);
    }

    // Restore progress bar on page load
    $(document).ready(function() {
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true
        });

        $('#progressContainer').show();
        setProgress(getProgress());

        fetchDetails();

        $("#studentForm").on("submit", function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we update the student profile.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                type: "POST",
                url: `${baseUrl}/${student_id}/Basicinfo/Update`,
                data: $(this).serialize(),
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Student profile has been updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // ✅ Step 2 milestone = 20%
                            setProgress(20);
                            window.location.href = `${baseUrl}/${student_id}/Address`;
                        });
                    } else {
                        Swal.fire("Failed", JSON.stringify(response.errors), "error");
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire("Error", "Something went wrong.", "error");
                    console.error(xhr.responseText);
                }
            });
        });

        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                // ✅ Still count this step as completed
                setProgress(20);
                window.location.href = `${baseUrl}/${student_id}/Address`;
            });
        });
    });

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${student_id}/Basicinfo/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let imgSrc = `/storage/${response.data.avatar_url}`;
                    $("#student-details").html(`
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex: 0 0 150px;">
                                <img src="${imgSrc}" class="img-thumbnail w-100" alt="Profile picture">
                            </div>
                            <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.student_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Gender:</strong> ${response.data.gender}</p>
                                <p><strong>Caste:</strong> ${response.data.caste}</p>
                                <p><strong>Religion:</strong> ${response.data.religion}</p>
                            </div>
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
</script>
@endsection