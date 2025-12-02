@extends('Admin.layout.app')

@section('title', 'College Courses')

@section('content')

<div class="page-content">
    <x-breadcrumb
        title="College Courses"
        :links="['Home' => 'Admin.Dashboard', 'College Master' => 'education.college.index', 'College Courses' => '']" />

    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1" id="college_name">College Name</h5>
                <p class="text-muted m-0" id="college_details"></p>
            </div>

            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    + Assign Course
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold">Assigned Courses</h5>

            <div class="d-flex gap-2 align-items-center">
                <input type="text" id="search" class="form-control" placeholder="Search..." style="width:260px;">
            </div>
        </div>

        <div class="card-body">
            <table class="table table-hover align-middle" id="courseTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th></th>
                        <th>Course</th>
                        <th>Code</th>
                        <th>Parent</th>
                        <th>Duration</th>
                        <th>Starting Date</th>
                        <th>Ending Date</th>
                        <th>Status</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
            </table>
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
                            <select id="child_course" name="child_course_id" class="form-control select2"></select>
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


<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Course</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editCourseForm">
                    @csrf
                    <input type="hidden" id="edit_id">

                    <div class="mb-3">
                        <label class="fw-semibold">Starting Date</label>
                        <input type="text" class="form-control datepicker" id="edit_start_date">
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold">End Date</label>
                        <input type="text" class="form-control datepicker" id="edit_ending_date">
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold">Status</label>
                        <select id="edit_status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="updateCourseBtn">Update</button>
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

        flatpickr(".datepicker", {
            dateFormat: "Y-m-d"
        });

        $.get("{{ route('education.college.edit', ':id') }}".replace(':id', collegeId),
            function(res) {
                let c = res.data.college;
                $("#college_name").text(c.org_name);
                $("#college_details").text(`${c.address} | ${c.email_id} | ${c.phone_no}`);
            }
        );

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


        let table = $('#courseTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            order: [
                [8, 'asc']   // correct index for "group"
            ],
            ajax: {
                url: "{{ route('education.collegecourse.list', $college) }}",
                data: function(d) {
                    d.search = $('#search').val();
                }
            },
            rowId: 'id',
            columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'arrow', orderable: false, searchable: false },
                    { data: 'course_name',orderable: false,  },
                    { data: 'course_code',orderable: false },
                    { data: 'parent_course_name',orderable: false },
                    { data: 'duration_in_years' },
                    { data: 'starting_date' },   
                    { data: 'ending_date' },    
                    { data: 'is_active',orderable: false },
                    { data: 'action', orderable: false, searchable: false },
                    { data: 'group', visible: false },
                    { data: 'parent_id', visible: false }
                ],
            createdRow: function(row, data) {
                if (data.group === "child") {
                    $(row).addClass('child-row child-of-' + data.parent_id + " table-warning").hide();
                } else {
                    $(row).addClass('parent-row table-primary').attr("data-id", data.id);
                }
            }
        });



        $("#search").on("keyup", function() {
            table.draw();
            $(".child-row").hide();
            $(".toggle-arrow").removeClass("fa-chevron-down").addClass("fa-chevron-right");
        });


        $(document).on("click", "#courseTable tbody tr", function(e) {
            if ($(e.target).closest(".deleteCourse").length) return;
            if ($(e.target).closest(".editCourse").length) return;
            if ($(e.target).closest(".toggleStatus").length) return;
            if ($(e.target).closest(".toggle-arrow").length) return;

            let row = table.row(this).data();
            if (row.group === "child") return;

            let children = $(".child-of-" + row.id);
            let arrow = $(".toggle-arrow[data-id='" + row.id + "']");

            if (children.is(":visible")) {
                children.slideUp(200);
                arrow.removeClass("fa-chevron-down").addClass("fa-chevron-right");
            } else {
                children.slideDown(200);
                arrow.removeClass("fa-chevron-right").addClass("fa-chevron-down");
            }
        });


        $(document).on("click", ".toggle-arrow", function(e) {
            e.stopPropagation();
            let id = $(this).data("id");
            let row = $(".child-of-" + id);

            if (row.is(":visible")) {
                row.slideUp(200);
                $(this).removeClass("fa-chevron-down").addClass("fa-chevron-right");
            } else {
                row.slideDown(200);
                $(this).removeClass("fa-chevron-right").addClass("fa-chevron-down");
            }
        });


        $("#courseForm").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('education.collegecourse.store', $college) }}",
                type: "POST",
                data: {
                    college_id: collegeId,
                    parent_course_id: $("#parent_course").val(),
                    child_course_id: $("#child_course").val(),
                    starting_date: $("input[name='starting_date']").val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        $("#addCourseModal").modal("hide");
                        $("#courseForm")[0].reset();
                        $("#parent_course").val("").trigger("change");
                        $("#child_course").val("").trigger("change");
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        table.ajax.reload();
                    }
                }
            });
        });


        $(document).on("click", ".deleteCourse", function(e) {
            e.stopPropagation();
            let id = $(this).data("id");

            Swal.fire({
                title: "Are you sure?",
                text: "This course will be removed",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/education/college-course/delete') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload();
                            }
                        }
                    });
                }
            });
        });


        $(document).on("click", ".editCourse", function(e) {
            e.stopPropagation();
            let id = $(this).data("id");

            $.get("{{ url('/education/college-course/show') }}/" + id, function(res) {
                $("#edit_id").val(res.data.id);
                $("#edit_start_date").val(res.data.starting_date || "");
                $("#edit_ending_date").val(res.data.ending_date || "");
                $("#edit_status").val(res.data.is_active);
                $("#editCourseModal").modal("show");
            });
        });



        $("#updateCourseBtn").on("click", function() {
            let id = $("#edit_id").val();

            $.post("{{ url('/education/college-course/update') }}/" + id, {
                _token: "{{ csrf_token() }}",
                starting_date: $("#edit_start_date").val(),
                ending_date: $("#edit_ending_date").val(),
                status: $("#edit_status").val()
            }, function(res) {
                $("#editCourseModal").modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "Updated",
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                table.ajax.reload(null, false);
            });
        });



    });
</script>
@endsection