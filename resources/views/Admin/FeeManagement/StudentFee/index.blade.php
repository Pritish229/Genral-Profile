@extends('Admin.layout.app')

@section('title', 'Assign Student Fee')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Assign Student Fee"
        :links="['Home' => 'Admin.Dashboard', 'Assign Fee' => '']" />

    <div class="card shadow-sm">
        
        <div class="card-body">

            <div class="row mb-4 rounded bg-light">

                <div class="col-md-4">
                    <div class="border p-3">
                        <div class="fw-bold">Student Name</div>
                        <div>{{ $student->full_name }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border p-3">
                        <div class="fw-bold">Course</div>
                        <div class="text-muted fw-bold">Parent: {{ $parent->course_name ?? 'N/A' }}</div>
                        <div>{{ $course->course_name }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border p-3">
                        <div class="fw-bold">Session</div>
                        <div>{{ $session }}</div>
                    </div>
                </div>

            </div>

            <h5 class="fw-bold mb-3">Course Fees</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="feeTable">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Fee Head</th>
                            <th>Type</th>
                            <th>Times</th>
                            <th>Amount</th>
                            <th>Select</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($fees as $i => $f)
                        <tr data-fee-id="{{ $f->fee_id }}"
                            data-collection="{{ $f->collection_type }}">

                            <td>{{ $i+1 }}</td>
                            <td>{{ $f->fee_head }}</td>

                            <td>
                                @if($f->fee_type == 1)
                                <span class="text-success">Add (+)</span>
                                @else
                                <span class="text-danger">Deduct (-)</span>
                                @endif
                            </td>

                            <td>{{ $f->times_in_year }}</td>

                            <td>
                                <input type="number"
                                    class="form-control amount-input"
                                    value="{{ $f->amount }}"
                                    readonly>
                            </td>

                            <td class="text-center">
                                <input type="checkbox" class="fee-check"
                                    @if($f->collection_type === 'mandatory') checked disabled @endif>
                            </td>

                            <td class="row-total fw-bold">
                                {{ number_format($f->total_amount, 2) }}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <hr>

            <h5 class="fw-bold text-danger mb-3">Deduct Fees</h5>

            <button class="btn btn-outline-danger btn-sm mb-3" id="addDeductFee">+ Add Deduct Fee</button>

            <div class="table-responsive">
                <table class="table table-bordered" id="deductFeeTable">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Fee Head</th>
                            <th>Amount</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <hr>

            <h5 class="fw-bold mb-3">Add Custom Fee</h5>

            <button class="btn btn-success btn-sm mb-3" id="addCustomFee">+ Add Custom Fee</button>

            <div class="table-responsive">
                <table class="table table-bordered" id="customFeeTable">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Fee Name</th>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <hr>

            <h3 class="text-end">
                Grand Total:
                <span id="grandTotal" class="text-primary fw-bold">0.00</span> ₹
            </h3>

            <button id="submitFees" class="btn btn-primary float-end mt-3" type="button">
                Submit Fees
            </button>

        </div>
    </div>
</div>

@endsection


@section('script')

<script>
let deductFeeOptions = [];

$(document).ready(function () {
    loadDeductMasters();
    calculateTotal();
});

function loadDeductMasters() {
    $.ajax({
        url: "{{ route('fee.feemaster.list') }}",
        type: "GET",
        data: { fee_type: 0 },
        success: function(res) {
            deductFeeOptions = res.data || [];
        }
    });
}

function calculateTotal() {
    let grand = 0;

    $('#feeTable tbody tr').each(function() {
        let chk = $(this).find('.fee-check');
        let isChecked = chk.is(':checked') || chk.is(':disabled');
        let amount = parseFloat($(this).find('.amount-input').val()) || 0;
        let times = parseInt($(this).find('td:nth-child(4)').text());
        let total = isChecked ? amount * times : 0;

        $(this).find('.row-total').text(total.toFixed(2));
        grand += total;
    });

    $('#deductFeeTable tbody tr').each(function() {
        let amount = parseFloat($(this).find('.deduct-amount').val()) || 0;
        let total = -amount;
        $(this).find('.deduct-total').text(total.toFixed(2));
        grand += total;
    });

    $('#customFeeTable tbody tr').each(function() {
        let amount = parseFloat($(this).find('.custom-amount').val()) || 0;
        let type = $(this).find('.custom-type').val();
        let total = type === 'add' ? amount : -amount;
        $(this).find('.custom-total').text(total.toFixed(2));
        grand += total;
    });

    $('#grandTotal').text(grand.toFixed(2));
}

$(document).on('input change', '.deduct-amount, .custom-amount, .custom-type', calculateTotal);
$(document).on('change', '.fee-check', calculateTotal);

$('#addDeductFee').click(function() {

    if (deductFeeOptions.length === 0) {
        Swal.fire("No deduct master fees found", "", "warning");
        return;
    }

    let options = deductFeeOptions.map(f => `<option value="${f.id}">${f.fee_name}</option>`).join('');
    let index = $('#deductFeeTable tbody tr').length + 1;

    $('#deductFeeTable tbody').append(`
        <tr>
            <td>${index}</td>
            <td><select class="form-select deduct-type">${options}</select></td>
            <td><input type="number" class="form-control deduct-amount" value="0"></td>
            <td class="deduct-total fw-bold text-danger">0.00</td>
            <td><button class="btn btn-danger btn-sm remove-row">X</button></td>
        </tr>
    `);
});

$('#addCustomFee').click(function() {
    let index = $('#customFeeTable tbody tr').length + 1;

    $('#customFeeTable tbody').append(`
        <tr>
            <td>${index}</td>
            <td><input type="text" class="form-control custom-name"></td>
            <td>
                <select class="form-select custom-type">
                    <option value="add">Add (+)</option>
                    <option value="deduct">Deduct (-)</option>
                </select>
            </td>
            <td><input type="number" class="form-control custom-amount" value="0"></td>
            <td class="custom-total fw-bold">0.00</td>
            <td><button class="btn btn-danger btn-sm remove-row">X</button></td>
        </tr>
    `);
});

$(document).on('click', '.remove-row', function() {
    $(this).closest('tr').remove();
    calculateTotal();
});

$('#submitFees').click(function () {

    $('#submitFees').prop('disabled', true).text('Processing...');
    $('input, select, button').prop('disabled', true);

    let fees = [];

    $('#feeTable tbody tr').each(function() {
        let chk = $(this).find('.fee-check');
        if (chk.is(':checked') || chk.is(':disabled')) {

            let text = $(this).find('td:nth-child(3)').text();
            let type = text.includes('+') ? 1 : 0;
            let collection = $(this).data('collection') === 'mandatory' ? 'mandatory' : 'optional';

            fees.push({
                fee_id: $(this).data('fee-id'),
                fee_head: $(this).find('td:nth-child(2)').text(),
                fee_type: type,
                collection_type: collection,
                amount: parseFloat($(this).find('.amount-input').val()),
                times: parseInt($(this).find('td:nth-child(4)').text())
            });
        }
    });

    $('#deductFeeTable tbody tr').each(function() {
        let amount = parseFloat($(this).find('.deduct-amount').val());
        if (amount > 0) {
            let id = $(this).find('.deduct-type').val();
            let name = $(this).find('.deduct-type option:selected').text();

            fees.push({
                fee_id: id,
                fee_head: name,
                fee_type: 0,
                collection_type: 'optional',
                amount: amount,
                times: 1
            });
        }
    });

    $('#customFeeTable tbody tr').each(function() {
        let name = $(this).find('.custom-name').val();
        let amount = parseFloat($(this).find('.custom-amount').val());
        if (name && amount > 0) {
            let type = $(this).find('.custom-type').val() === 'add' ? 1 : 0;
            fees.push({
                fee_id: null,
                fee_head: name,
                fee_type: type,
                collection_type: 'optional',
                amount: amount,
                times: 1
            });
        }
    });

    $.ajax({
        url: "{{ route('fee.studentfee.store') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            student_id: "{{ $student->student_id ?? $student->id }}",
            student_name: "{{ $student->full_name }}",
            college_id: "{{ $course->college_id }}",
            course_id: "{{ $course->course_id }}",
            session_year_name: "{{ $session }}",
            fees: fees
        },
        success: function(res) {

            if (res.status) {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Fees assigned successfully!"
                }).then(() => {
                    resetForm();
                });
            } else {
                Swal.fire("Error", res.message || "Something went wrong", "error");
                restoreUI();
            }
        },
        error: function() {
            Swal.fire("Error", "Server error", "error");
            restoreUI();
        }
    });
});

function resetForm() {
    $('#deductFeeTable tbody').empty();
    $('#customFeeTable tbody').empty();
    $('#feeTable .fee-check').prop('checked', false);
    $('#grandTotal').text("0.00");

    $('input, select, button').prop('disabled', false);
    $('#submitFees').text("Submit Fees");

    calculateTotal();
}

function restoreUI() {
    $('input, select, button').prop('disabled', false);
    $('#submitFees').text("Submit Fees");
}
</script>


@endsection