@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Manage Bank')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Bank"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
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
                        <th>Account Type</th> {{-- ✅ Added --}}
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
                        <td colspan="13" class="text-center">Loading...</td>
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

                    <label for="method">Method</label>
                    <select id="method" name="method" class="form-select mb-3" required>
                        <option value="">-- Select Method --</option>
                        <option value="bank">Bank</option>
                        <option value="upi">UPI</option>
                    </select>

                    <div id="bank-fields" class="d-none">
                        <x-inputbox id="bank_account_holder" name="account_holder" label="Account Holder" type="text" placeholder="John Doe" :required="true" value="" helpertxt="" />
                        <x-inputbox id="bank_name" name="bank_name" label="Bank Name" type="text" placeholder="State Bank of India" :required="true"  value="" helpertxt=""/>

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

                        <x-inputbox id="account_number" name="account_number" label="Account Number" type="text" placeholder="XXXXXXXXXXXX1234" value="" helpertxt="" :required="false"/>
                        <x-inputbox id="branch_name" name="branch_name" label="Branch Name" type="text" placeholder="MG Road Branch" value="" helpertxt="" :required="false"/>
                        <x-inputbox id="ifsc_code" name="ifsc_code" label="IFSC Code" type="text" placeholder="SBIN0001234" :required="true" value="" helpertxt="" :required="false" />
                        <x-inputbox id="swift_code" name="swift_code" label="SWIFT Code" type="text" placeholder="SBININBBXXX" value="" helpertxt="" :required="false"/>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_primary" name="is_primary">
                            <label class="form-check-label" for="is_primary">Set as Primary</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_default_payout" name="is_default_payout">
                            <label class="form-check-label" for="is_default_payout">Set as Default Payout</label>
                        </div>
                    </div>

                    <div id="upi-fields" class="d-none">
                        <x-inputbox id="upi_id" name="upi_id" label="UPI ID" type="text" placeholder="example@upi" value="" helpertxt="" :required="false"/>
                        <x-inputbox id="upi_name" name="upi_name" label="UPI Holder Name" type="text" placeholder="Full Name" :required="true" value="" helpertxt="" />
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
let vendor_id = "{{ $id }}";
let baseUrl = "{{ url('/vendors') }}";

function toggleFields(method) {
    method === 'bank' ? ($('#bank-fields').removeClass('d-none'), $('#upi-fields').addClass('d-none'))
                      : ($('#upi-fields').removeClass('d-none'), $('#bank-fields').addClass('d-none'));
}

$('#addBankBtn').click(()=>{ $('#bankDetailsForm')[0].reset(); $('#account_id').val(''); $('#modalSaveText').text('Save'); toggleFields(''); $('#bankModal').modal('show'); });

$(document).on('click','.editBankBtn',function(){
    let a = $(this).data('account');
    $.get(`${baseUrl}/${vendor_id}/bank/${a.id}`, res => {
        let acc = res.data;
        $('#account_id').val(acc.id);
        $('#method').val(acc.method).trigger('change');
        $('#bank_account_holder').val(acc.account_holder);
        $('#bank_name').val(acc.bank_name);
        $('#account_type').val(acc.account_type).trigger('change'); // ✅ Prefill account type
        $('#branch_name').val(acc.branch_name);
        $('#ifsc_code').val(acc.ifsc_code);
        $('#swift_code').val(acc.swift_code);
        $('#upi_id').val(acc.upi_vpa);
        $('#upi_name').val(acc.account_holder);
        $('#account_number').val('').attr('placeholder', acc.account_number_mask);
        $('#is_primary').prop('checked', acc.is_primary == 1);
        $('#is_default_payout').prop('checked', acc.is_default_payout == 1);
        toggleFields(acc.method);
        $('#modalSaveText').text('Update');
        $('#bankModal').modal('show');
    });
});

function vendorbanklist(){
    $.get(`${baseUrl}/${vendor_id}/BankList`, res=>{
        let data = Array.isArray(res)?res:res.data;
        if(!data?.length){ $('#bank-list').html(`<tr><td colspan="13" class="text-center">No bank details found.</td></tr>`); return;}
        let rows='';
        data.forEach((a,i)=> rows+=`
        <tr>
            <td>${i+1}</td>
            <td>${a.method}</td>
            <td>${a.account_holder??'-'}</td>
            <td>${a.bank_name??'-'}</td>
            <td>${a.account_type??'-'}</td> 
            <td>${a.account_number_mask??'-'}</td>
            <td>${a.branch_name??'-'}</td>
            <td>${a.ifsc_code??'-'}</td>
            <td>${a.swift_code??'-'}</td>
            <td>${a.upi_vpa??'-'}</td>
            <td>${a.is_primary?'<span class="badge bg-success">Primary</span>':'-'}</td>
            <td>
                <button class="btn btn-sm btn-primary editBankBtn" data-account='${JSON.stringify(a)}'><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteBank(${a.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`);
        $('#bank-list').html(rows);
    });
}

function deleteBank(id){
    Swal.fire({icon:'warning',title:'Delete?',showCancelButton:true,confirmButtonText:'Yes'}).then(r=>{
        if(r.isConfirmed){
            $.ajax({url:`${baseUrl}/${vendor_id}/deleteBank/${id}`,type:'DELETE',headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},success:vendorbanklist});
        }
    });
}

$('#bankDetailsForm').submit(function(e) {
    e.preventDefault();

    // Prevent double submission
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    const id = $('#account_id').val();
    const url = id 
        ? `${baseUrl}/${vendor_id}/updateBank/${id}` 
        : `${baseUrl}/${vendor_id}/saveBank`;

    $.post(url, $(this).serialize())
        .done(function(response) {
            $('#bankModal').modal('hide');
            vendorbanklist();

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: id ? 'Bank/UPI details updated successfully.' : 'Bank/UPI details saved successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .fail(function(xhr) {
            let errorMsg = 'Something went wrong. Please try again.';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    // Laravel validation errors
                    const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    errorMsg = firstError || errorMsg;
                }
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg
            });
        })
        .always(function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> <span id="modalSaveText">Save</span>');
        });
});

$(document).ready(()=> vendorbanklist());
$('#method').change(()=> toggleFields($('#method').val()));
</script>
@endsection
