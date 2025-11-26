@extends('Admin.layout.app')

@section('title', 'Assign Student Fees')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Assign Student Fees"
        :links="['Home' => 'Admin.Dashboard', 'Assign Fees' => '']" />

    <div class="p-1">

        {{-- FILTERS --}}
        <div class="card p-3 mb-4">
            <div class="row g-3">

                {{-- COLLEGE --}}
                <div class="col-md-4">
                    <label class="form-label">College *</label>
                    <select id="filter_college" class="form-select">
                        <option value="">-- Select College --</option>
                    </select>
                </div>

                {{-- COURSE --}}
                <div class="col-md-4">
                    <label class="form-label">Course *</label>
                    <select id="filter_course" class="form-select">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>

                {{-- STUDENT --}}
                <div class="col-md-4">
                    <label class="form-label">Student *</label>
                    <select id="filter_student" class="form-select">
                        <option value="">-- Select Student --</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- FEES GROUP DISPLAY --}}
        <div class="card p-4">

            <h5 class="mb-3">Annual Fees</h5>
            <div class="row g-3 mb-4" id="annual_fees"></div>

            <h5 class="mb-3">Monthly Fees</h5>
            <div class="row g-3 mb-4" id="monthly_fees"></div>

            <h5 class="mb-3">Other Fees</h5>
            <div class="row g-3 mb-4" id="other_fees"></div>

        </div>

        {{-- SUMMARY SECTION --}}
        <div class="card p-4 mt-4">
            <h5 class="mb-3">Selected Fees Summary</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fee Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Total Fee</th>
                    </tr>
                </thead>
                <tbody id="summaryTable"></tbody>
            </table>

            <div class="row mt-4">
                <div class="col-md-4 ms-auto">
                    <x-inputbox
                        id="grandTotal"
                        name="grandTotal"
                        type="number"
                        label="Grand Total"
                        readonly="true"
                        disabled="true"
                        value="0"
                    />
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-primary">Assign Fees</button>

            </div>

        </div>

    </div>

</div>

@endsection

@section('script')
<script>
$(function() {

    // Init Select2
    $('select').select2({ width: '100%' });

    // Dummy Filters
    $('#filter_college').html(`
        <option value="">-- Select --</option>
        <option value="1">College of Engineering</option>
        <option value="2">College of Science</option>
    `);

    $('#filter_course').html(`
        <option value="">-- Select --</option>

        <optgroup label="Kids / Pre-Primary">
            <option value="16">Pre-Nursery</option>
            <option value="17">Nursery</option>
        </optgroup>

        <optgroup label="Degree">
            <option value="37">BCA</option>
            <option value="91">BCA Cyber Security</option>
        </optgroup>
    `);

    $('#filter_student').html(`
        <option value="">-- Select --</option>
        <option value="101">Aman Sharma</option>
        <option value="102">Riya Verma</option>
    `);

    // Dummy Fees
    let fees = [
        // Annual
        { id: 1, name: "Course Fee", total_fee: 50000, fee_type: 1 },
        { id: 2, name: "Uniform Fee", total_fee: 5000, fee_type: 1 },

        // Monthly
        { id: 3, name: "Tuition Fee", total_fee: 5000, fee_type: 2 },
        { id: 4, name: "Sports Fee", total_fee: 1000, fee_type: 2 },
        { id: 5, name: "Hostel Fee", total_fee: 7000, fee_type: 2 },

        // Other
        { id: 6, name: "Transport Fee", total_fee: 5000, fee_type: 3 },
        { id: 7, name: "Exam Fee", total_fee: 800, fee_type: 3 }
    ];

    // Render Fees Grouped
    fees.forEach(fee => {
        let target =
            fee.fee_type === 1 ? '#annual_fees' :
            fee.fee_type === 2 ? '#monthly_fees' :
                                '#other_fees';

        $(target).append(`
            <div class="col-md-4">
                <label class="check-option">
                    <input type="checkbox" class="fee-check"
                        data-id="${fee.id}"
                        data-name="${fee.name}"
                        data-type="${fee.fee_type}"
                        data-total="${fee.total_fee}">
                    <div class="icon">&#xf00c;</div>
                    <span>${fee.name} — ${fee.total_fee} Rs</span>
                </label>
            </div>
        `);
    });

    // Summary Logic
    $('.fee-check').on('change', function() {

        let id = $(this).data('id');
        let name = $(this).data('name');
        let type = $(this).data('type');
        let total = $(this).data('total');

        if ($(this).is(':checked')) {
            $('#summaryTable').append(`
                <tr id="row_${id}">
                    <td>${name}</td>
                    <td>${ type == 1 ? 'Annual' : type == 2 ? 'Monthly' : 'Other' }</td>
                    <td>${total} Rs</td>
                    <td>${total} Rs</td>
                </tr>
            `);
        } else {
            $(`#row_${id}`).remove();
        }

        calculateGrandTotal();
    });

    function calculateGrandTotal() {
        let total = 0;

        $('#summaryTable tr').each(function() {
            let val = parseFloat($(this).find('td:last').text()) || 0;
            total += val;
        });

        $('#grandTotal').val(total);
    }

});
</script>
@endsection
