@extends('Admin.layout.app')

@section('title', 'Student Fee Schedule')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Student Fee Schedule"
        :links="['Home' => 'Admin.Dashboard', 'Fee Schedule' => '']" />

    <div class="p-1">

        {{-- FORM --}}
        <div class="card p-4 mb-4">

            <h5 class="mb-3">Create Fee Schedule</h5>

            <form id="feeScheduleForm">@csrf
                <div class="row g-3">


                    {{-- STUDENT --}}
                    <div class="col-md-4">
                        <x-inputbox
                            id="student_name"
                            type="text"
                            name="student_name"
                            label="Student"
                            placeholder="Aman Sharma"
                            value="Aman Sharma"
                            required="true"
                            readonly="true" />
                    </div>

                    {{-- INSTALMENT TYPE --}}
                    <div class="col-md-4">
                        <x-inputbox
                            id="instalment_type"
                            type="text"
                            name="instalment_type"
                            label="Instalment Type"
                            placeholder="Ex: First Instalment"
                            value=""
                            required="true" />
                    </div>

                    {{-- DATE RANGE --}}
                    <div class="col-md-4">
                        <label for="date_range" class="mb-2 labeltxt">Instalment Date Range</label>
                        <input id="date_range" name="date_range"
                               class="form-control flatpickr"
                               placeholder="Select date range"
                               value="">
                    </div>

                    {{-- AMOUNT --}}
                    <div class="col-md-4">
                        <x-inputbox
                            id="amount_paid"
                            type="number"
                            name="amount_paid"
                            label="Amount"
                            placeholder="Enter Instalment Amount"
                            value=""
                            required="true" />
                    </div>

                    {{-- REMAINING TOTAL (Dynamic From JS) --}}
                    <div class="col-md-4">
                        <x-inputbox
                            id="remaining_amount"
                            type="number"
                            name="remaining_amount"
                            label="Remaining Amount"
                            placeholder="Remaining amount"
                            value="100000"
                            readonly="true" />
                    </div>

                </div>

                <div class="text-end mt-3">
                    <button type="button" id="addSchedule" class="btn btn-primary">
                        Add Instalment
                    </button>
                </div>

            </form>

        </div>

        <div class="card p-4">

            <h5>Instalment Breakdown</h5>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Instalment Type</th>
                        <th>Date Range</th>
                        <th>Amount</th>
                        <th>Remaining After</th>
                    </tr>
                </thead>
                <tbody id="scheduleTable"></tbody>
            </table>

        </div>

    </div>

</div>

@endsection



{{-- SCRIPTS --}}
@section('script')

<script>
$(function () {

    // Flatpickr Range Picker
    $(".flatpickr").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        allowInput: true,
    });

    // Total Fee Example (Static or dynamic from DB)
    let totalRemaining = parseFloat($('#remaining_amount').val() || 0);  // initial remaining from value

    // Update the remaining input visually (in case value changed by JS)
    function setRemaining(val) {
        totalRemaining = Math.max(0, parseFloat(val || 0));
        // use input's attribute for readonly UI (x-inputbox supports attributes)
        $('#remaining_amount').val(totalRemaining);
    }

    setRemaining(totalRemaining);

    // Add Instalment Row
    $("#addSchedule").on("click", function () {

        let type = $("#instalment_type").val().trim();
        let dateRange = $("#date_range").val().trim();
        let amount = parseFloat($("#amount_paid").val());

        if (!type) {
            alert("Please enter instalment type.");
            return;
        }

        if (!dateRange) {
            alert("Please select a date range.");
            return;
        }

        if (!amount || amount <= 0 || isNaN(amount)) {
            alert("Please enter a valid amount greater than zero.");
            return;
        }

        if (amount > totalRemaining) {
            // allow but warn (you can change behaviour)
            if (!confirm("Amount is greater than remaining total. Do you want to proceed?")) {
                return;
            }
        }

        // Deduct amount
        totalRemaining -= amount;
        if (totalRemaining < 0) totalRemaining = 0;

        setRemaining(totalRemaining);

        // Add Row to table
        $("#scheduleTable").append(`
            <tr>
                <td>${escapeHtml(type)}</td>
                <td>${escapeHtml(dateRange)}</td>
                <td>${formatAmount(amount)} Rs</td>
                <td>${formatAmount(totalRemaining)} Rs</td>
            </tr>
        `);

        // Reset Fields For Next Entry
        $("#instalment_type").val("");
        $("#date_range").flatpickr().clear();
        $("#amount_paid").val("");
    });

    // small helpers
    function formatAmount(n) {
        // format with 2 decimals and thousand separators
        return parseFloat(n).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

});
</script>
@endsection
