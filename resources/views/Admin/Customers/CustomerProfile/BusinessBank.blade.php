@extends('Admin.layout.app')

@section('title', 'Home | Customers | Bank Details')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Business Bank" :links="[
        'Home' => 'Admin.Dashboard',
        'Customers' => 'customers.List',
        'Customer Details' => ['customers.viewDetails', ['id' => $id]],
        'Business List' => ['customers.Businesslist', $id],
        'Business Details' => ['customers.BusinessDetails', ['id' => $id, 'business_id' => $business_id]],
        'Business Bank' => ''
    ]" />

    <section id="bank_details">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mx-2 mt-2 mb-0">
                <h6>Bank / UPI List</h6>
                <div>
                    <a href="javascript:void(0)" class="text-primary me-3" id="addBankBtn" title="Add Bank/UPI">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </div>
            <hr style="color:#5156be">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sl No</th>
                        <th>Method</th>
                        <th>Account Holder</th>
                        <th>Bank Name</th>
                        <th>Account Type</th>
                        <th>Account Number</th>
                        <th>Branch</th>
                        <th>IFSC Code</th>
                        <th>SWIFT Code</th> <!-- Added -->
                        <th>UPI VPA</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bank-list">
                    <tr>
                        <td colspan="12" class="text-center">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- Bank / UPI Modal --}}
<div class="modal fade" id="bankModal" tabindex="-1" aria-labelledby="bankModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="bankDetailsForm">
                @csrf
                <input type="hidden" name="account_id" id="account_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="bankModalLabel">Bank / UPI Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="method">Method</label>
                        <select id="method" name="method" class="form-select" required>
                            <option value="">-- Select Method --</option>
                            <option value="bank">Bank</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>

                    {{-- Bank Fields --}}
                    <div id="bank-fields" class="d-none">
                        <x-inputbox id="bank_account_holder" name="account_holder" label="Account Holder" type="text" placeholder="John Doe" :required="true" helpertxt="" value=""  />
                        <x-inputbox id="bank_name" name="bank_name" label="Bank Name" type="text" placeholder="State Bank of India" :required="true" helpertxt="" value="" />

                        <div class="mb-3">
                            <label for="account_type">Account Type</label>
                            <select name="account_type" id="account_type" class="form-select" required>
                                <option value="">Select Account Type</option>
                                <option value="savings">Savings Account</option>
                                <option value="current">Current Account</option>
                                <option value="salary">Salary Account</option>
                                <option value="fixed_deposit">Fixed Deposit Account</option>
                                <option value="recurring_deposit">Recurring Deposit Account</option>
                                <option value="cash_credit">Cash Credit Account</option>
                                <option value="overdraft">Overdraft Account</option>
                                <option value="nri">NRI Account</option>
                                <option value="business_current">Business Current Account</option>
                                <option value="joint">Joint Account</option>
                                <option value="merchant">Merchant Account</option>
                                <option value="escrow">Escrow Account</option>
                                <option value="demat">Demat Account</option>
                                <option value="loan">Loan Account</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <x-inputbox id="account_number" name="account_number" label="Account Number" type="text" placeholder="XXXXXXXXXXXX1234" :required="true" helpertxt="" value="" />
                        <x-inputbox id="branch_name" name="branch_name" label="Branch Name" type="text" placeholder="MG Road Branch" helpertxt="" value="" :required="false" />
                        <x-inputbox id="ifsc_code" name="ifsc_code" label="IFSC Code" type="text" placeholder="SBIN0001234" :required="true" helpertxt="" value=""  />
                        <x-inputbox id="swift_code" name="swift_code" label="SWIFT Code" type="text" placeholder="SBININBBXXX" helpertxt="" value="" :required="false"/>
                        
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_primary" name="is_primary">
                            <label class="form-check-label" for="is_primary">Set as Primary</label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_default_payout" name="is_default_payout">
                            <label class="form-check-label" for="is_default_payout">Set as Default Payout</label>
                        </div>
                    </div>

                    {{-- UPI Fields --}}
                    <div id="upi-fields" class="d-none">
                        <x-inputbox id="upi_id" name="upi_id" label="UPI ID" type="text" placeholder="example@upi" :required="true" helpertxt="" value="" />
                        <x-inputbox id="upi_name" name="upi_name" label="UPI Holder Name" type="text" placeholder="Full Name" :required="true" helpertxt="" value="" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <span id="modalSaveText">Save</span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let customer_id = "{{ $id }}";
let business_id = "{{ $business_id }}";
let baseUrl = "{{ url('/customers') }}";

function toggleFields(method) {
    $('#bank-fields input, #bank-fields select, #upi-fields input')
        .prop('required', false)
        .prop('disabled', true);

    const isEdit = $('#account_id').val() !== '';

    if (method === 'bank') {
        $('#bank-fields').removeClass('d-none');
        $('#upi-fields').addClass('d-none');
        $('#bank-fields input, #bank-fields select').prop('disabled', false);
        $('#bank_account_holder, #bank_name, #ifsc_code, #account_type').prop('required', true);
        $('#account_number').prop('required', !isEdit);
    } else if (method === 'upi') {
        $('#upi-fields').removeClass('d-none');
        $('#bank-fields').addClass('d-none');
        $('#upi-fields input').prop('disabled', false);
        $('#upi_id, #upi_name').prop('required', true);
    } else {
        $('#bank-fields, #upi-fields').addClass('d-none');
    }
}

$('#addBankBtn').click(() => {
    $('#bankModalLabel').text('Add Bank / UPI');
    $('#bankDetailsForm')[0].reset();
    $('#account_id').val('');
    $('#account_number').attr('placeholder', 'XXXXXXXXXXXX1234');
    $('#modalSaveText').text('Save');
    toggleFields('');
    $('#bankModal').modal('show');
});

// ✅ FIX: correct URL + id var
function editBank(account) {
    $('#bankModalLabel').text('Edit Bank / UPI');
    $.ajax({
        url: `${baseUrl}/${customer_id}/${business_id}/business/bank/${account.id}`,
        type: 'GET',
        success: function(res) {
            const acc = res.data || account;

            $('#account_id').val(acc.id);
            $('#method').val(acc.method).trigger('change');

            $('#bank_account_holder').val(acc.account_holder || '');
            $('#bank_name').val(acc.bank_name || '');
            $('#account_type').val(acc.account_type || '').trigger('change');
            $('#branch_name').val(acc.branch_name || '');
            $('#ifsc_code').val(acc.ifsc_code || '');
            $('#swift_code').val(acc.swift_code || '');
            $('#upi_id').val(acc.upi_vpa || '');
            $('#upi_name').val(acc.account_holder || '');
            $('#is_primary').prop('checked', +acc.is_primary === 1);
            $('#is_default_payout').prop('checked', +acc.is_default_payout === 1);
            $('#account_number').val('').attr('placeholder', acc.account_number_mask || 'XXXXXXXXXXXX1234');

            $('#modalSaveText').text('Update');
            toggleFields(acc.method);
            $('#bankModal').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'Failed to load bank details.', 'error');
        }
    });
}

function loadBusinessBankList() {
    $.ajax({
        url: `${baseUrl}/${customer_id}/${business_id}/business/BankList`,
        type: 'GET',
        success: function(res) {
            let data = Array.isArray(res) ? res : (res.data || []);
            if (!data.length) {
                $('#bank-list').html('<tr><td colspan="12" class="text-center">No bank details found.</td></tr>');
                return;
            }
            let rows = '';
            data.forEach((account, index) => {
                const primaryBadge = account.is_primary ? '<span class="badge bg-success ms-2">Primary</span>' : '';
                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${account.method || '-'}</td>
                        <td>${account.account_holder || '-'}</td>
                        <td>${account.bank_name || '-'}</td>
                        <td>${account.account_type || '-'}</td>
                        <td>${account.account_number_mask || account.account_number || '-'}</td>
                        <td>${account.branch_name || '-'}</td>
                        <td>${account.ifsc_code || '-'}</td>
                        <td>${account.swift_code || '-'}</td>
                        <td>${account.upi_vpa || '-'}</td>
                        <td>${primaryBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editBankBtn" data-account='${JSON.stringify(account)}'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBank(${account.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
            });
            $('#bank-list').html(rows);
        },
        error: function() {
            $('#bank-list').html('<tr><td colspan="12" class="text-center">Failed to load bank details.</td></tr>');
        }
    });
}

function deleteBank(account_id) {
    Swal.fire({
        title: 'Delete?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${baseUrl}/${customer_id}/${business_id}/business/deleteBank/${account_id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire('Deleted!', res.message || 'Bank detail deleted.', 'success');
                    loadBusinessBankList();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete.', 'error');
                }
            });
        }
    });
}

$('#bankDetailsForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const accountId = $('#account_id').val();
    const isEdit = accountId !== '';

    const url = isEdit
        ? `${baseUrl}/${customer_id}/${business_id}/${accountId}/business/updateBank`
        : `${baseUrl}/${customer_id}/${business_id}/business/saveBank`;

    Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: res.message || 'Saved successfully.',
                timer: 1500,
                showConfirmButton: false
            });
            $('#bankModal').modal('hide');
            loadBusinessBankList();
        },
        error: function(xhr) {
            Swal.close();
            let msg = 'Validation failed';
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            Swal.fire({ icon: 'error', title: 'Error', html: msg });
        }
    });
});

$(document).on('click', '.editBankBtn', function() {
    const account = $(this).data('account');
    editBank(account);
});

$(document).ready(function() {
    loadBusinessBankList();
    $('#method').on('change', function() { toggleFields($(this).val()); });
});
</script>
@endsection

