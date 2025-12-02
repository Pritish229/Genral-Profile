@extends('Admin.layout.app')

@section('title','Assigned Fees')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Assigned Fee Details"
        :links="['Home'=>'Admin.Dashboard','Assigned Fees'=>'']" />

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Assigned Fee Details</h5>
        </div>

        <div class="card-body">

            <div id="studentInfo" class="row mb-4 bg-light p-3 rounded"></div>

            <h5 class="fw-bold mb-3">Assigned Fees</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="assignedFeesTable">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>#</th>
                            <th>Fee Head</th>
                            <th>Type</th>
                            <th>Collection</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <h3 class="text-end mt-3 text-primary fw-bold">
                Grand Total: ₹ <span id="grandTotal">0.00</span>
            </h3>

        </div>
    </div>

</div>

@endsection


@section('script')
<script>
$(document).ready(function() {

    loadAssignedFees();

    function loadAssignedFees() {

        $.ajax({
            url: "{{ route('fee.studentfee.viewfees') }}",
            type: "GET",
            data: {
                student_id: "{{ $student_id }}",
                course_id: "{{ $course_id }}",
                college_id: "{{ $college_id }}",
                session_year_name: "{{ $session_year_name }}"
            },
            success: function(res) {

                if (!res.status) {
                    Swal.fire("Error", "No fee records found.", "error");
                    return;
                }

                let student = res.student;
                let course  = res.course;
                let parent  = res.parent;
                let fees    = res.fees;

                $('#studentInfo').html(`
                    <div class="col-md-4"><div class="border p-3">
                        <strong>Student Name</strong><div>${student.full_name}</div>
                    </div></div>

                    <div class="col-md-4"><div class="border p-3">
                        <strong>Course</strong>
                        <div class="text-muted">Parent: ${parent ? parent.course_name : 'N/A'}</div>
                        <div>${course.course_name}</div>
                    </div></div>

                    <div class="col-md-4"><div class="border p-3">
                        <strong>Session</strong><div>${res.session}</div>
                    </div></div>
                `);

                let rows = "";
                let index = 1;
                let grand = 0;

                fees.forEach(f => {

                    if (f.is_parent == 1) {
                        rows += `
                            <tr class="table-info fw-bold">
                                <td colspan="6">Parent: ${parent.course_name}</td>
                            </tr>
                        `;
                        return;
                    }

                    let typeBadge = f.fee_type == "1"
                        ? '<span class="badge bg-success">Add (+)</span>'
                        : '<span class="badge bg-danger">Deduct (-)</span>';

                    let amt = parseFloat(f.session_one_amount);

                    if (f.fee_type == "1") grand += amt;
                    else grand -= amt;

                    rows += `
                        <tr>
                            <td>${index++}</td>
                            <td>${f.fee_head}</td>
                            <td>${typeBadge}</td>
                            <td>${f.collection_type}</td>
                            <td>${amt.toFixed(2)}</td>
                        </tr>
                    `;
                });

                $('#assignedFeesTable tbody').html(rows);
                $('#grandTotal').text(grand.toFixed(2));
            },

            error: function() {
                Swal.fire("Error", "Server error", "error");
            }
        });
    }

});
</script>
@endsection
