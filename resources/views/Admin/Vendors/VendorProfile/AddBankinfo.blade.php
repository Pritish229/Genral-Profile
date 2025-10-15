@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Bank Details')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Bank Details"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List' ,'Bank Details'=>'' ]" />

    <!-- Student details -->
    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="vendor-details">
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
                <div class="col-md-6">
                    <label for="is_default_payout">Payment Method</label>
                    <select class="form-select" id="method" name="method">
                        <option value="">-- Select Method --</option>
                        <option value="bank" selected>Bank</option>
                        <option value="upi">UPI</option>
                    </select>
                    <small class="form-text text-muted">Choose whether you want to add Bank details or UPI details.</small>
                </div>
                <div class="col-md-6">
                    <label for="is_default_payout">Default Payout</label>
                    <select class="form-select" id="is_default_payout" name="is_default_payout">
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                    <small class="form-text text-muted">Choose the Default Payout.</small>
                </div>
            </div>

            <!-- UPI fields -->
            <div id="upi-fields" class="d-none">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox id="upi_id" label="UPI ID" type="text"
                            placeholder="example@upi" name="upi_id"
                            :required="false" value="{{ old('upi_id') }}"
                            helpertxt="Enter your valid UPI ID (e.g., mobile@upi)." />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox id="upi_name" label="UPI Holder Name" type="text"
                            placeholder="Full Name" name="upi_name"
                            :required="false" value="{{ old('upi_name') }}"
                            helpertxt="Enter the name as registered with UPI." />
                    </div>
                </div>
            </div>

            <!-- Bank fields -->
            <div id="bank-fields" class="d-none">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox id="account_holder" label="Account Holder" type="text"
                            placeholder="John Doe" name="account_holder"
                            :required="false" value="{{ old('account_holder') }}"
                            helpertxt="Enter the account holder’s full name as per bank records." />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox id="bank_name" label="Bank Name" type="text"
                            placeholder="State Bank of India" name="bank_name"
                            :required="false" value="{{ old('bank_name') }}"
                            helpertxt="Mention the official name of the bank." />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox id="branch_name" label="Branch Name" type="text"
                            placeholder="MG Road Branch" name="branch_name"
                            :required="false" value="{{ old('branch_name') }}"
                            helpertxt="Provide the branch name where the account is opened." />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox id="account_number" label="Account Number" type="text"
                            placeholder="Enter Account Number" name="account_number"
                            :required="true" value="{{ old('account_number') }}"
                            helpertxt="Double-check your account number before submitting." />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox id="ifsc_code" label="IFSC Code" type="text"
                            placeholder="SBIN0001234" name="ifsc_code"
                            :required="false" value="{{ old('ifsc_code') }}"
                            helpertxt="Enter the 11-digit IFSC code (for Indian banks)." />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox id="swift_code" label="SWIFT Code" type="text"
                            placeholder="SBININBBXXX" name="swift_code"
                            :required="false" value="{{ old('swift_code') }}"
                            helpertxt="Enter SWIFT code (for international transactions)." />
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
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
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

    function updateProgress(value) {
        $("#progressContainer").show();
        $("#progressBar").css("width", value + "%").text(value + "%");
    }

    function toggleFields(method) {
        if (method === "upi") {
            $("#upi-fields").removeClass("d-none");
            $("#bank-fields").addClass("d-none");
            $("#upi-fields :input").prop("disabled", false);
            $("#bank-fields :input").prop("disabled", true);
        } else if (method === "bank") {
            $("#bank-fields").removeClass("d-none");
            $("#upi-fields").addClass("d-none");
            $("#bank-fields :input").prop("disabled", false);
            $("#upi-fields :input").prop("disabled", true);
        } else {
            $("#upi-fields, #bank-fields").addClass("d-none");
            $("#upi-fields :input, #bank-fields :input").prop("disabled", true);
        }
    }

    $(document).ready(function() {
        fetchDetails();
        updateProgress(50); // Bank step = 50%

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
                window.location.href = `${baseUrl}/${vendor_id}/Document`;
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
                url: `${baseUrl}/${vendor_id}/saveBank`,
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
                            window.location.href = `${baseUrl}/${vendor_id}/Document`;
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