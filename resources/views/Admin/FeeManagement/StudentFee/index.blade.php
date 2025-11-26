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

                <div class="col-md-4">
                    <label class="form-label">College *</label>
                    <select id="filter_college" class="form-select">
                        <option value="">-- Select College --</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Course *</label>
                    <select id="filter_course" class="form-select">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Student *</label>
                    <select id="filter_student" class="form-select">
                        <option value="">-- Select Student --</option>
                    </select>
                </div>

            </div>
        </div>


        {{-- FEE TABLES --}}
        <div class="card p-4">

            {{-- Annual --}}
            <h5 class="mb-3">Annual Fees</h5>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th width="50">Select</th>
                        <th>Fee Name</th>
                        <th>Mode</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="annual_fees"></tbody>
            </table>

            {{-- Monthly --}}
            <h5 class="mb-3">Monthly Fees</h5>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th width="50">Select</th>
                        <th>Fee Name</th>
                        <th>Mode</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="monthly_fees"></tbody>
            </table>

            {{-- Other --}}
            <h5 class="mb-3">Other Fees</h5>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th width="50">Select</th>
                        <th>Fee Name</th>
                        <th>Mode</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="other_fees"></tbody>
            </table>

        </div>


        {{-- SUMMARY --}}
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

            <div class="text-end mt-3">
                <h4>Total Selected: <span id="grandTotal">0</span> Rs</h4>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-primary">Assign Fees</button>
            </div>
        </div>

    </div>

</div>

@endsection



@section('script')
<style>
.badge-addon {
    background: #e1f0ff;
    color: #0d6efd;
    padding: 4px 10px;
    border-radius: 6px;
}

.badge-deduct {
    background: #ffe2e2;
    color: #d63030;
    padding: 4px 10px;
    border-radius: 6px;
}
</style>


<script>
$(function () {

    $('select').select2({ width: '100%' });

    $('#filter_college').html(`
        <option value="">-- Select --</option>
        <option value="1">College of Engineering</option>
        <option value="2">College of Science</option>
    `);

    $('#filter_course').html(`
        <option value="">-- Select --</option>
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


    // FEES DATA
    let fees = [
        { id: 1, name: "Course Fee", total_fee: 50000, fee_type: 1, mode: "addon" },
        { id: 2, name: "Uniform Fee", total_fee: 5000, fee_type: 1, mode: "addon" },

        { id: 3, name: "Tuition Fee", total_fee: 5000, fee_type: 2, mode: "addon" },
        { id: 4, name: "Sports Fee", total_fee: 1000, fee_type: 2, mode: "addon" },
        { id: 5, name: "Hostel Fee", total_fee: 7000, fee_type: 2, mode: "addon" },

        { id: 6, name: "Transport Fee", total_fee: 5000, fee_type: 3, mode: "addon" },
        { id: 7, name: "Scholarship Deduction", total_fee: 2000, fee_type: 3, mode: "deduct" }
    ];


    function typeLabel(t) {
        return t == 1 ? "Annual" : t == 2 ? "Monthly" : "Other";
    }

    function modeBadge(mode) {
        return mode == "addon"
            ? `<span class='badge-addon'>Addon</span>`
            : `<span class='badge-deduct'>Deduct</span>`;
    }


    // Render to tables
    fees.forEach(f => {

        let target =
            f.fee_type == 1 ? "#annual_fees" :
            f.fee_type == 2 ? "#monthly_fees" :
            "#other_fees";

        $(target).append(`
            <tr>
                <td>
                    <input type="checkbox" class="fee-check"
                        data-id="${f.id}"
                        data-name="${f.name}"
                        data-type="${f.fee_type}"
                        data-total="${f.total_fee}"
                        data-mode="${f.mode}">
                </td>
                <td>${f.name}</td>
                <td>${modeBadge(f.mode)}</td>
                <td>${typeLabel(f.fee_type)}</td>
                <td style="color:${f.mode == 'deduct' ? 'red' : '#333'};">
                    ${f.total_fee} Rs
                </td>
            </tr>
        `);
    });



    // Update Summary Table
    $(document).on("change", ".fee-check", function () {

        updateSummary();

    });


    function updateSummary() {

        let sum = 0;
        $('#summaryTable').empty();

        $(".fee-check:checked").each(function () {

            let id = $(this).data("id");
            let name = $(this).data("name");
            let total = parseFloat($(this).data("total"));
            let type = $(this).data("type");
            let mode = $(this).data("mode");

            let lbl = typeLabel(type);
            let amountDisplay = mode === "deduct"
                ? `- ${total} Rs`
                : `${total} Rs`;

            $('#summaryTable').append(`
                <tr>
                    <td>${name}</td>
                    <td>${lbl}</td>
                    <td>${amountDisplay}</td>
                    <td>${amountDisplay}</td>
                </tr>
            `);

            sum += mode === "deduct" ? -total : total;
        });

        $('#grandTotal').text(sum);
    }

});
</script>
@endsection
