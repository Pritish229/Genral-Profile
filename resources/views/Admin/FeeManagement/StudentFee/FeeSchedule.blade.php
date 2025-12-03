@extends('Admin.layout.app')

@section('title', 'Fee Installment Schedule')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Fee Installment Schedule"
        :links="['Home' => 'Admin.Dashboard', 'Fee Schedule' => '']" />

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="row mb-4 bg-light p-3 rounded">
                <div class="col-md-4">
                    <div class="border p-3">
                        <div class="fw-bold">Student Name</div>
                        <div>{{ $student->fullName }}</div>
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

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3 text-center bg-primary text-white">
                        <div>Total Due</div>
                        <h4 id="summary_total_due">0</h4>
                    </div>
                </div>



                <div class="col-md-4">
                    <div class="card shadow-sm p-3 text-center bg-warning">
                        <div>Balance Due</div>
                        <h4 id="summary_balance">0</h4>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm p-3 text-center bg-info text-white">
                        <div>Installments</div>
                        <h4 id="summary_count">0</h4>
                    </div>
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <h4 class="fw-bold mb-3">Create Installment</h4>

                <form id="installmentForm">
                    @csrf

                    <input type="hidden" name="student_id" value="{{ $student->student_id }}">
                    <input type="hidden" name="course_id" value="{{ $course->course_id }}">
                    <input type="hidden" name="session_year_name" value="{{ $session }}">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Installment Title</label>
                            <input type="text" name="installment_title" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Amount</label>
                            <input type="number" name="installment_amount" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Due Date</label>
                            <input type="text" name="due_date" id="due_date" class="form-control" required>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="button" id="addInstallmentBtn">
                        Add Installment
                    </button>
                </form>
            </div>

            <h4 class="fw-bold">Installment Ledger</h4>

            <table class="table table-striped table-bordered mt-3">
                <thead class="bg-light">
                    <tr>
                        <th>Schedule No</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Balance Due</th>    
                        <th>Due Date</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody id="installmentTableBody">
                    <tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection

@section('script')

<script>
$(document).ready(function() {
    $('#due_date').flatpickr({
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    refreshAll();

    $('#addInstallmentBtn').click(function() {
        createInstallment();
    });
});

function refreshAll() {
    loadSummary();
    loadTable();
}

function loadSummary() {
    $.get("{{ route('fee.installment.summary') }}", {
        student_id: "{{ $student->student_id }}",
        course_id: "{{ $course->course_id }}",
        session_year_name: "{{ $session }}"
    }, function(res) {
        $('#summary_total_due').text(res.total_due + " ₹");
        $('#summary_paid').text(res.total_paid + " ₹");
        $('#summary_balance').text(res.balance_due + " ₹");
        $('#summary_count').text(res.installment_count);
    });
}

function loadTable() {
    $.get("{{ route('fee.installment.list') }}", {
        student_id: "{{ $student->student_id }}",
        course_id: "{{ $course->course_id }}",
        session_year_name: "{{ $session }}"
    }, function(rows) {
        let html = "";
        let i = 1;

        if (rows.length === 0) {
            html = `<tr><td colspan="7" class="text-center text-muted">No records yet.</td></tr>`;
        } else {
            rows.forEach(r => {
                let status = `<span class="badge bg-success">Paid</span>`;
                if (parseFloat(r.balance_due.replace(/,/g, '')) > 0) {
                    status = `<span class="badge bg-warning text-dark">Pending</span>`;
                    if (r.overdue) status = `<span class="badge bg-danger">Overdue</span>`;
                }

                html += `
                <tr>
                    
                    <td>${r.installment_no}</td>
                    <td>${r.installment_title}</td>
                    <td>${r.installment_amount}</td>
                    <td>${r.balance_due}</td>
                    <td>${r.due_date}</td>
                    <td>${r.created_at}</td>
                </tr>`;
            });
        }

        $("#installmentTableBody").html(html);
    });
}

function createInstallment() {
    $.post("{{ route('fee.installment.store') }}",
        $('#installmentForm').serialize(),
        function(res) {
            if (!res.status) {
                Swal.fire("Error", res.message, "error");
                return;
            }

            Swal.fire("Success", "Installment added", "success");
            $('#installmentForm')[0].reset();
            refreshAll();
        }
    ).fail(() => {
        Swal.fire("Error", "Server error", "error");
    });
}
</script>

@endsection
