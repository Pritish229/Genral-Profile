@extends('Admin.layout.app')

@section('title', 'Home | Students | Bank Details')

@section('content')
<div class="page-content">
    <x-breadcrumb
    title="Bank Details"
    :links="[
        'Home' => 'Admin.Dashboard',
        'Students' => 'students.Studentlist',
        'Student Detail' => ['students.Studentlist.studentDetailsPage', $id],
        'Bank Details' => ''
    ]"
/>
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
                        <th>slNo</th>
                        <th>Method</th>
                        <th>Account Holder</th>
                        <th>Bank Name</th>
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
                        <td colspan="10" class="text-center">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Modal for Add/Edit Bank/UPI -->
<div class="modal fade" id="bankModal" tabindex="-1" aria-labelledby="bankModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="bankDetailsForm">
        @csrf
        <input type="hidden" name="account_id" id="account_id" value="">

        <div class="modal-header">
          <h5 class="modal-title" id="bankModalLabel">Bank / UPI Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

            <!-- Bank Fields -->
            <div id="bank-fields" class="d-none">
                <x-inputbox id="bank_account_holder" name="account_holder" label="Account Holder" type="text" placeholder="John Doe" />
                <x-inputbox id="bank_name" name="bank_name" label="Bank Name" type="text" placeholder="State Bank of India" />
                <x-inputbox id="branch_name" name="branch_name" label="Branch Name" type="text" placeholder="MG Road Branch" />
                <x-inputbox id="ifsc_code" name="ifsc_code" label="IFSC Code" type="text" placeholder="SBIN0001234" />
                <x-inputbox id="swift_code" name="swift_code" label="SWIFT Code" type="text" placeholder="SBININBBXXX" />
            </div>

            <!-- UPI Fields -->
            <div id="upi-fields" class="d-none">
                <x-inputbox id="upi_vpa" name="upi_vpa" label="UPI ID" type="text" placeholder="example@upi" />
                <x-inputbox id="upi_holder_name" name="upi_holder_name" label="UPI Holder Name" type="text" placeholder="Full Name" />
            </div>

            <!-- Primary Dropdown -->
            <div class="mb-3">
                <label for="is_primary">Make Primary?</label>
                <select id="is_primary" name="is_primary" class="form-select">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
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
let student_id = "{{ $id }}";

// Toggle fields + required attributes
function toggleFields(method) {
    if (method === 'bank') {
        $('#bank-fields').removeClass('d-none');
        $('#upi-fields').addClass('d-none');

        $('#bank-fields input').attr('required', true);
        $('#upi-fields input').removeAttr('required');

    } else if (method === 'upi') {
        $('#upi-fields').removeClass('d-none');
        $('#bank-fields').addClass('d-none');

        $('#upi-fields input').attr('required', true);
        $('#bank-fields input').removeAttr('required');

    } else {
        $('#bank-fields, #upi-fields').addClass('d-none');
        $('#bank-fields input, #upi-fields input').removeAttr('required');
    }
}

// Open Modal: Add
$('#addBankBtn').click(function() {
    $('#bankModalLabel').text('Add Bank / UPI');
    $('#modalSaveText').text('Save');
    $('#bankDetailsForm')[0].reset();
    $('#account_id').val('');
    toggleFields('');
    $('#bankModal').modal('show');
});

// Open Modal: Edit
function editBank(account) {
    $('#bankModalLabel').text('Edit Bank / UPI');
    $('#modalSaveText').text('Update');

    $('#account_id').val(account.id);
    $('#method').val(account.method).trigger('change');

    $('#bank_account_holder').val(account.account_holder || '');
    $('#bank_name').val(account.bank_name || '');
    $('#branch_name').val(account.branch_name || '');
    $('#ifsc_code').val(account.ifsc_code || '');
    $('#swift_code').val(account.swift_code || '');

    $('#upi_vpa').val(account.upi_vpa || '');
    $('#upi_holder_name').val(account.account_holder || '');

    $('#is_primary').val(account.is_primary ? 1 : 0);

    toggleFields(account.method);
    $('#bankModal').modal('show');
}

// Fetch bank list
function studentbanklist() {
    $.ajax({
        url: `{{url('/students/${student_id}/bank-list')}}`,
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
                            <td>${account.upi_vpa || '-'}</td>
                            <td>
                                ${account.is_primary == 1 
                                    ? '<span class="badge bg-success">Yes</span>' 
                                    : '<span class="badge bg-secondary">No</span>'}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary editBankBtn" data-account='${JSON.stringify(account)}'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteBank(${account.id})">
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

// Edit button click
$(document).on('click', '.editBankBtn', function() {
    let account = $(this).data('account');
    editBank(account);
});

// Delete bank
function deleteBank(account_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the account!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{url('/students/${student_id}/deleteBank/${account_id}')}}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success');
                        studentbanklist();
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                }
            });
        }
    });
}

// Submit bank form
$('#bankDetailsForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: `{{url('/students/${student_id}/saveBank')}}`,
        type: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            Swal.fire({ icon: 'success', title: 'Success!', text: res.message, timer: 1500, showConfirmButton: false });
            $('#bankModal').modal('hide');
            studentbanklist();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let html = '';
                $.each(xhr.responseJSON.errors, function(k, v) { html += v[0] + '<br>'; });
                Swal.fire({ icon: 'error', title: 'Validation Error', html: html });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
            }
        }
    });
});

// On page load
$(document).ready(function() {
    studentbanklist();
    $('#method').change(function() {
        toggleFields($(this).val());
    });
});
</script>
@endsection
