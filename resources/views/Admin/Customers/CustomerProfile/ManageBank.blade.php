@extends('Admin.layout.app')

@section('title', 'Home | Customers | Manage Bank')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Bank"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Customers' => 'customers.List',
        'Customer Details' => ['customers.viewDetails', ['id' => $id]],
        'Manage Bank' => ''
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
                        <th>SWIFT Code</th>
                        <th>UPI VPA</th>
                        <th>Primary</th>
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

<!-- Modal -->
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
                        <label for="method">Method <span class="text-danger">*</span></label>
                        <select id="method" name="method" class="form-select" required>
                            <option value="">-- Select Method --</option>
                            <option value="bank">Bank</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>

                    <!-- Bank Fields -->
                    <div id="bank-fields" class="d-none">
                        <x-inputbox id="bank_account_holder" name="account_holder" label="Account Holder" type="text" placeholder="John Doe" :required="false" />
                        <x-inputbox id="bank_name" name="bank_name" label="Bank Name" type="text" placeholder="State Bank of India" :required="false" />

                        <div class="mb-3">
                            <label for="account_type">Account Type <span class="text-danger">*</span></label>
                            <select name="account_type" id="account_type" class="form-select">
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

                        <x-inputbox id="account_number" name="account_number" label="Account Number" type="text" placeholder="XXXXXXXXXXXX1234" :required="false" />
                        <x-inputbox id="branch_name" name="branch_name" label="Branch Name" type="text" placeholder="MG Road Branch" :required="false" />
                        <x-inputbox id="ifsc_code" name="ifsc_code" label="IFSC Code" type="text" placeholder="SBIN0001234" :required="false" />
                        <x-inputbox id="swift_code" name="swift_code" label="SWIFT Code" type="text" placeholder="SBININBBXXX" :required="false" />

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_primary" name="is_primary">
                            <label class="form-check-label" for="is_primary">Set as Primary</label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_default_payout" name="is_default_payout">
                            <label class="form-check-label" for="is_default_payout">Set as Default Payout</label>
                        </div>
                    </div>

                    <!-- UPI Fields -->
                    <div id="upi-fields" class="d-none">
                        <x-inputbox id="upi_id" name="upi_id" label="UPI ID" type="text" placeholder="example@upi" :required="false" />
                        <x-inputbox id="upi_name" name="upi_name" label="UPI Holder Name" type="text" placeholder="Full Name" :required="false" />
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
    let baseUrl = "{{ url('/customers') }}";

    // Toggle visibility + required fields
    function toggleFields(method) {
        const isEdit = $('#account_id').val() !== '';

        if (method === 'bank') {
            $('#bank-fields').removeClass('d-none');
            $('#upi-fields').addClass('d-none');

            $('#bank_account_holder, #bank_name, #account_type, #ifsc_code').prop('required', true);
            $('#account_number').prop('required', !isEdit); // Required only when adding
            $('#branch_name, #swift_code').prop('required', false);
            $('#upi_id, #upi_name').prop('required', false);
        } else if (method === 'upi') {
            $('#upi-fields').removeClass('d-none');
            $('#bank-fields').addClass('d-none');

            $('#upi_id, #upi_name').prop('required', true);
            $('#bank_account_holder, #bank_name, #account_type, #ifsc_code, #account_number, #branch_name, #swift_code')
                .prop('required', false);
        } else {
            $('#bank-fields, #upi-fields').addClass('d-none');
            $('#bank_account_holder, #bank_name, #account_type, #ifsc_code, #account_number, #branch_name, #swift_code, #upi_id, #upi_name')
                .prop('required', false);
        }
    }

    // Add New
    $('#addBankBtn').click(function() {
        $('#bankModalLabel').text('Add Bank / UPI');
        $('#modalSaveText').text('Save');
        $('#bankDetailsForm')[0].reset();
        $('#account_id').val('');
        $('#account_number').attr('placeholder', 'XXXXXXXXXXXX1234');
        $('#method').val('').trigger('change');
        toggleFields('');
        $('#bankModal').modal('show');
    });

    // Edit
    $(document).on('click', '.editBankBtn', function() {
        const account = $(this).data('account');
        $('#bankModalLabel').text('Edit Bank / UPI');
        $('#modalSaveText').text('Update');

        $.get(`${baseUrl}/${customer_id}/bank/${account.id}`, function(res) {
            const acc = res.data || account;

            $('#account_id').val(acc.id);
            $('#method').val(acc.method).trigger('change');
            $('#bank_account_holder').val(acc.account_holder || '');
            $('#bank_name').val(acc.bank_name || '');
            $('#account_type').val(acc.account_type || '');
            $('#branch_name').val(acc.branch_name || '');
            $('#ifsc_code').val(acc.ifsc_code || '');
            $('#swift_code').val(acc.swift_code || '');
            $('#upi_id').val(acc.upi_vpa || '');
            $('#upi_name').val(acc.account_holder || '');
            $('#account_number').val('').attr('placeholder', acc.account_number_mask || 'XXXXXXXXXXXX1234');
            $('#is_primary').prop('checked', !!acc.is_primary);
            $('#is_default_payout').prop('checked', !!acc.is_default_payout);

            toggleFields(acc.method);
            $('#bankModal').modal('show');
        }).fail(() => {
            Swal.fire('Error', 'Failed to load bank details.', 'error');
        });
    });

    // Load List
    function customerbanklist() {
        $.get(`${baseUrl}/${customer_id}/BankList`, function(res) {
            const data = Array.isArray(res) ? res : (res.data || []);
            if (data.length === 0) {
                $('#bank-list').html('<tr><td colspan="12" class="text-center">No bank details found.</td></tr>');
                return;
            }

            let rows = '';
            data.forEach((acc, i) => {
                const primary = acc.is_primary ? '<span class="badge bg-success">Primary</span>' : '-';
                const accType = acc.account_type 
                    ? acc.account_type.charAt(0).toUpperCase() + acc.account_type.slice(1).replace('_', ' ')
                    : '-';

                rows += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${acc.method || '-'}</td>
                    <td>${acc.account_holder || '-'}</td>
                    <td>${acc.bank_name || '-'}</td>
                    <td>${accType}</td>
                    <td>${acc.account_number_mask || '-'}</td>
                    <td>${acc.branch_name || '-'}</td>
                    <td>${acc.ifsc_code || '-'}</td>
                    <td>${acc.swift_code || '-'}</td>
                    <td>${acc.upi_vpa || '-'}</td>
                    <td>${primary}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editBankBtn" data-account='${JSON.stringify(acc)}'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBank(${acc.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#bank-list').html(rows);
        }).fail(() => {
            $('#bank-list').html('<tr><td colspan="12" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    // Delete
    function deleteBank(id) {
        Swal.fire({
            title: 'Delete?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${customer_id}/deleteBank/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        Swal.fire('Deleted!', res.message || 'Bank detail removed.', 'success');
                        customerbanklist();
                    },
                    error: function() {
                        Swal.fire('Error', 'Could not delete.', 'error');
                    }
                });
            }
        });
    }

    // Submit Form
    $('#bankDetailsForm').submit(function(e) {
        e.preventDefault();

        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const originalHtml = $btn.html();
        const accountId = $('#account_id').val();
        const isEdit = !!accountId;
        const url = isEdit
            ? `${baseUrl}/${customer_id}/updateBank/${accountId}`
            : `${baseUrl}/${customer_id}/saveBank`;

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.post(url, $form.serialize())
            .done(function(res) {
                $('#bankModal').modal('hide');
                customerbanklist();

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: isEdit ? 'Bank/UPI updated successfully.' : 'Bank/UPI added successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .fail(function(xhr) {
                let msg = 'Validation failed.';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors)[0][0];
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire('Error', msg, 'error');
            })
            .always(function() {
                $btn.prop('disabled', false).html(originalHtml);
            });
    });

    // Reset on modal close
    $('#bankModal').on('hidden.bs.modal', function() {
        $('#bankDetailsForm')[0].reset();
        $('#account_id').val('');
        $('#method').val('').trigger('change');
        $('#modalSaveText').text('Save');
        toggleFields('');
    });

    // Initialize
    $(document).ready(function() {
        customerbanklist();
        $('#method').change(function() {
            toggleFields($(this).val());
        });
    });
</script>
@endsection