@extends('Admin.layout.app')

@section('title', 'Home | Students | Bank Details')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Bank Details"
        :links="['Home' => 'Admin.Dashboard', 'Students' => 'students.Studentlist' ,'Bank Details'=>'' ]" />

    <!-- Student details -->
    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="student-details">
                Loading details...
            </div>
        </div>
    </div>

    <!-- Progress bar -->
    <div class="progress mb-3 mt-3" style="height: 25px; display:none;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="progressBar">0%</div>
    </div>

    <!-- Bank/UPI form -->
    <div class="mt-4">
        <form id="bankDetailsForm">
            <div class="row mb-3">
                <div class="col-md-12">
                    <select class="form-select" id="method" name="method">
                        <option value="">-- Select Method --</option>
                        <option value="bank" selected>Bank</option>
                        <option value="upi">UPI</option>
                    </select>
                    <small class="form-text text-muted">Choose whether you want to add Bank details or UPI details.</small>
                </div>
            </div>

            <!-- UPI fields -->
            <div id="upi-fields" class="d-none">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox
                            id="upi_id"
                            label="UPI ID"
                            type="text"
                            placeholder="example@upi"
                            name="upi_id"
                            :required="false" />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox
                            id="upi_name"
                            label="UPI Holder Name"
                            type="text"
                            placeholder="Full Name"
                            name="upi_name"
                            :required="false" />
                    </div>
                </div>
            </div>

            <!-- Bank fields -->
            <div id="bank-fields" class="d-none">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox
                            id="account_holder"
                            label="Account Holder"
                            type="text"
                            placeholder="John Doe"
                            name="account_holder"
                            :required="false" />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox
                            id="bank_name"
                            label="Bank Name"
                            type="text"
                            placeholder="State Bank of India"
                            name="bank_name"
                            :required="false" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox
                            id="branch_name"
                            label="Branch Name"
                            type="text"
                            placeholder="MG Road Branch"
                            name="branch_name"
                            :required="false" />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox
                            id="ifsc_code"
                            label="IFSC Code"
                            type="text"
                            placeholder="SBIN0001234"
                            name="ifsc_code"
                            :required="false" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox
                            id="swift_code"
                            label="SWIFT Code"
                            type="text"
                            placeholder="SBININBBXXX"
                            name="swift_code"
                            :required="false" />
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save & Continue</button>
                <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
            </div>
        </form>

        <div id="alert-box" class="mt-3"></div>
    </div>

</div>
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/students') }}";
    let student_id = "{{ $id }}";

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

    function updateProgress(value) {
        $("#progressContainer").show();
        $("#progressBar").css("width", value + "%").text(value + "%");
    }

    function toggleFields(method) {
        if (method === "upi") {
            $("#upi-fields").removeClass("d-none");
            $("#bank-fields").addClass("d-none");

            $("#upi_id, #upi_name").attr("required", true);
            $("#account_holder, #bank_name, #branch_name, #ifsc_code, #swift_code").removeAttr("required");

        } else if (method === "bank") {
            $("#bank-fields").removeClass("d-none");
            $("#upi-fields").addClass("d-none");

            $("#account_holder, #bank_name, #branch_name, #ifsc_code").attr("required", true);
            $("#upi_id, #upi_name, #swift_code").removeAttr("required");

        } else {
            $("#upi-fields, #bank-fields").addClass("d-none");
            $("#upi_id, #upi_name, #account_holder, #bank_name, #branch_name, #ifsc_code, #swift_code").removeAttr("required");
        }
    }

    $(document).ready(function() {
        fetchDetails();
        updateProgress(50); // Bank step = 40%

        // Default show bank
        toggleFields("bank");

        $("#method").on("change", function() {
            toggleFields($(this).val());
        });

        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `${baseUrl}/${student_id}/Document`; // change NextStep to your next route
            });
        });

        $("#bankDetailsForm").on("submit", function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save bank details.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                type: "POST",
                url: `${baseUrl}/${student_id}/storeBank`,
                data: $(this).serialize(),
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        updateProgress(60);
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${student_id}/Document`; 
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
    });
</script>
@endsection
