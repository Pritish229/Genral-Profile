@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Bank Details')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Bank Details"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List', 'Bank Details' => '']" />

    <!-- Vendor details -->
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
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="method">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="method" name="method" required>
                        <option value="">-- Select Method --</option>
                        <option value="bank" selected>Bank</option>
                        <option value="upi">UPI</option>
                    </select>
                    <small class="form-text text-muted">Choose whether you want to add Bank details or UPI details.</small>
                </div>
                <div class="col-md-6">
                    <label for="is_default_payout">Default Payout <span class="text-danger">*</span></label>
                    <select class="form-select" id="is_default_payout" name="is_default_payout" required>
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                    <small class="form-text text-muted">Choose the Default Payout.</small>
                </div>
            </div>
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
            <div id="bank-fields">
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
                        <label for="account_type">Account Type <span class="text-danger">*</span></label>
                        <select name="account_type" id="account_type" class="form-select">
                            <option value="">Select Account Type</option>
                            <option value="savings" {{ old('account_type') == 'savings' ? 'selected' : '' }}>Savings Account</option>
                            <option value="current" {{ old('account_type') == 'current' ? 'selected' : '' }}>Current Account</option>
                            <option value="salary" {{ old('account_type') == 'salary' ? 'selected' : '' }}>Salary Account</option>
                            <option value="fixed_deposit" {{ old('account_type') == 'fixed_deposit' ? 'selected' : '' }}>Fixed Deposit Account</option>
                            <option value="recurring_deposit" {{ old('account_type') == 'recurring_deposit' ? 'selected' : '' }}>Recurring Deposit Account</option>
                            <option value="cash_credit" {{ old('account_type') == 'cash_credit' ? 'selected' : '' }}>Cash Credit Account</option>
                            <option value="overdraft" {{ old('account_type') == 'overdraft' ? 'selected' : '' }}>Overdraft Account</option>
                            <option value="nri" {{ old('account_type') == 'nri' ? 'selected' : '' }}>NRI Account</option>
                            <option value="business_current" {{ old('account_type') == 'business_current' ? 'selected' : '' }}>Business Current Account</option>
                            <option value="joint" {{ old('account_type') == 'joint' ? 'selected' : '' }}>Joint Account</option>
                            <option value="merchant" {{ old('account_type') == 'merchant' ? 'selected' : '' }}>Merchant Account</option>
                            <option value="escrow" {{ old('account_type') == 'escrow' ? 'selected' : '' }}>Escrow Account</option>
                            <option value="demat" {{ old('account_type') == 'demat' ? 'selected' : '' }}>Demat Account</option>
                            <option value="loan" {{ old('account_type') == 'loan' ? 'selected' : '' }}>Loan Account</option>
                            <option value="other" {{ old('account_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <small class="form-text text-muted">Select the type of bank account.</small>
                    </div>
                    <div class="col-md-6">
                        <x-inputbox id="account_number" label="Account Number" type="text"
                            placeholder="Enter Account Number" name="account_number"
                            :required="false" value="{{ old('account_number') }}"
                            helpertxt="Double-check your account number before submitting." />
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
                        <x-inputbox id="ifsc_code" label="IFSC Code" type="text"
                            placeholder="SBIN0001234" name="ifsc_code"
                            :required="false" value="{{ old('ifsc_code') }}"
                            helpertxt="Enter the 11-digit IFSC code (for Indian banks)." />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-inputbox id="swift_code" label="SWIFT Code" type="text"
                            placeholder="SBININBBXXX" name="swift_code"
                            :required="false" value="{{ old('swift_code') }}"
                            helpertxt="Enter SWIFT code (for international transactions)." />
                    </div>
                    <div class="col-md-6"></div>
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
                    let imgSrc = response.data.avatar_url ? `storage/${response.data.avatar_url}` : 'assets/img/default-avatar.png';
                    $("#vendor-details").html(`
                        <div class="d-flex align-items-start gap-3">
                            <div style="flex: 0 0 160px;">
                                <img src="{{ asset('') }}${imgSrc}" class="img-thumbnail w-100" alt="Profile picture" onerror="this.src='{{ asset('assets/img/default-avatar.png') }}'">
                            </div>
                            <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.vendor_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                                <p><strong>Phone:</strong> ${response.primary_details.primary_phone ?? 'N/A'}</p>
                            </div>
                        </div>
                    `);
                } else {
                    $("#vendor-details").html(`<p class="text-danger">${response.errors || 'Failed to load vendor details.'}</p>`);
                }
            },
            error: function() {
                $("#vendor-details").html(`<p class="text-danger">Unable to load details.</p>`);
            }
        });
    }

    function loadContacts() {
        $("#contact-list").html(`<p class="text-muted">Loading contacts...</p>`);
        $.ajax({
            url: `${baseUrl}/${vendor_id}/contacts/list`,
            method: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let rows = response.data.map(contact => `
                        <tr>
                            <td>${contact.contact_type}</td>
                            <td>${contact.value}</td>
                            <td>${contact.label ?? '-'}</td>
                            <td>${contact.is_primary ? '<span class="badge bg-success">Primary</span>' : ''}</td>
                        </tr>
                    `).join("");
                    $("#contact-list").html(`
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Label</th>
                                    <th>Primary</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    `);
                } else {
                    $("#contact-list").html(`<p class="text-muted">No contacts found for this vendor.</p>`);
                }
            },
            error: function() {
                $("#contact-list").html(`<p class="text-danger">Failed to load contacts.</p>`);
            }
        });
    }

    function updateProgress(value) {
        $("#progressContainer").show();
        $("#progressBar").css("width", value + "%").text(value + "%");
    }

    // Toggle fields + dynamically set required
    function toggleFields(method) {
        const bankFields = ['account_holder', 'bank_name', 'account_type', 'account_number', 'ifsc_code'];
        const upiFields = ['upi_id', 'upi_name'];

        if (method === "upi") {
            $("#upi-fields").removeClass("d-none");
            $("#bank-fields").addClass("d-none");

            upiFields.forEach(id => $(`#${id}`).prop('required', true));
            bankFields.forEach(id => $(`#${id}`).prop('required', false));
        } else if (method === "bank") {
            $("#bank-fields").removeClass("d-none");
            $("#upi-fields").addClass("d-none");

            bankFields.forEach(id => $(`#${id}`).prop('required', true));
            upiFields.forEach(id => $(`#${id}`).prop('required', false));
        } else {
            $("#upi-fields, #bank-fields").addClass("d-none");
            [...bankFields, ...upiFields].forEach(id => $(`#${id}`).prop('required', false));
        }
    }

    $(document).ready(function() {
        fetchDetails();
        loadContacts();
        updateProgress(50);
        toggleFields("bank"); // Default to Bank

        $("#method").on("change", function() {
            toggleFields($(this).val());
        });

        $("#refreshContactsBtn").on("click", function() {
            loadContacts();
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
                            text: response.message || 'Bank/UPI details saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${vendor_id}/Document`;
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let html = '<div class="alert alert-danger"><ul class="mb-0">';
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            html += '<li>' + value[0] + '</li>';
                        });
                    } else {
                        html += '<li>Something went wrong. Please try again.</li>';
                    }
                    html += '</ul></div>';
                    $("#alert-box").html(html);
                }
            });
        });
    });
</script>
@endsection