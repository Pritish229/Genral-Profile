@extends('Admin.layout.app')

@section('title', 'College Courses')

@section('content')
<div class="page-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">College Courses</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            + Assign Course
        </button>
    </div>

    <!-- College Details Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-1" id="college_name">College Name</h5>
            <p class="text-muted m-0" id="college_details"></p>
        </div>
    </div>

    <!-- Assigned Courses Table Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold">Assigned Courses</h5>

            <div>
                <input type="text" id="search" class="form-control" placeholder="Search...">
            </div>
        </div>

        <div class="card-body">
            <table class="table table-hover align-middle" id="courseTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Course</th>
                        <th>Code</th>
                        <th>Parent</th>
                        <th>Duration</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>

            <div class="d-flex justify-content-end mt-3">
                <nav>
                    <ul class="pagination pagination-sm" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Assign Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="courseForm">
                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="fw-semibold">Parent Course</label>
                            <select id="parent_course" name="parent_course_id" class="form-control select2"></select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-semibold">Child Course</label>
                            <select id="child_course" name="course_id" class="form-control select2"></select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="fw-semibold">Starting Date</label>
                            <input type="text" name="starting_date" class="form-control datepicker" placeholder="Select Starting Date">
                        </div>

                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="saveCourseBtn" type="submit" form="courseForm">Save</button>
            </div>

        </div>
    </div>
</div>


@endsection



@section('script')
<script>
    const universityId = "{{ $university }}";
    const collegeId = "{{ $college }}";

    $(document).ready(function() {

        /* -----------------------------
            FLATPICKR 
        -----------------------------*/
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d"
        });

        $(document).ready(function() {
            $.get(
                "{{ route('education.college.edit', ':id') }}".replace(':id', collegeId),
                function(res) {
                    let c = res.data.college;
                    $("#college_name").text(c.org_name);
                    $("#college_details").text(
                        `${c.address} | ${c.email_id} | ${c.phone_no}`
                    );
                }
            );
        });
        // loadTable();

        // function loadTable() {
        //     $.get("{{ route('education.collegecourse.list', $college) }}", function(res) {

        //         let data = res.data ?? res;
        //         let html = "";

        //         data.forEach((item, index) => {

        //             let type = item.is_parent ?
        //                 `<span class="badge bg-primary">Parent</span>` :
        //                 `<span class="badge bg-info">Child</span>`;

        //             let status = item.is_active ?
        //                 `<span class="badge bg-success">Active</span>` :
        //                 `<span class="badge bg-secondary">Inactive</span>`;

        //             html += `
        //             <tr>
        //                 <td>${index + 1}</td>
        //                 <td>${item.course_name}</td>
        //                 <td>${item.course_code ?? '-'}</td>
        //                 <td>${item.parent_course_name ?? '—'}</td>
        //                 <td>${item.duration_in_years ?? 0} Years</td>
        //                 <td>${type}</td>
        //                 <td>${status}</td>
        //                 <td class="text-center">
        //                     <button class="btn btn-danger btn-sm deleteBtn" data-id="${item.id}">
        //                         <i class="fa fa-trash"></i>
        //                     </button>
        //                 </td>
        //             </tr>
        //         `;
        //         });

        //         $("#courseTable tbody").html(html);
        //     });
        // }



        /* -----------------------------
            SELECT2 PARENT COURSE
        -----------------------------*/
        $("#parent_course").select2({
            dropdownParent: $('#addCourseModal'),
            placeholder: "Select Parent Course",
            width: "100%",
            ajax: {
                url: "{{ route('education.universitycourse.getUniversityCourses', ':id') }}"
                    .replace(':id', universityId),

                processResults: data => ({
                    results: (data.data ?? data).map(item => ({
                        id: item.id,
                        text: item.course_name
                    }))
                })
            }
        });


        /* -----------------------------
            SELECT2 CHILD COURSE
        -----------------------------*/
        $("#parent_course").on("change", function() {
            let pid = $(this).val();

            $("#child_course").html("").trigger("change");

            $("#child_course").select2({
                dropdownParent: $('#addCourseModal'),
                placeholder: "Select Child Course",
                width: "100%",
                ajax: {
                    url: "{{ route('education.universitycourse.childlist', ['university' => ':u', 'id' => ':p']) }}"
                        .replace(':u', universityId)
                        .replace(':p', pid),

                    processResults: data => ({
                        results: (data.data ?? data).map(item => ({
                            id: item.id,
                            text: item.course_name
                        }))
                    })
                }
            });
        });

    });
</script>
@endsection