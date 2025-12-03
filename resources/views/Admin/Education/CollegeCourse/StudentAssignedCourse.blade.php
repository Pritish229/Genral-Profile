@extends('Admin.layout.app')

@section('title', 'Course Students')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Course Students"
        :links="['Home' => 'Admin.Dashboard', 'Course Students' => '']" />

    <div class="row">

        <div class="col-md-3 mb-3">
            <label>Select University</label>
            <select id="university_id" class="form-control select2"></select>
        </div>

        <div class="col-md-3 mb-3">
            <label>Select College</label>
            <select id="college_id" class="form-control select2"></select>
        </div>

        <div class="col-md-3 mb-3">
            <label>Select Parent Course</label>
            <select id="parent_course_id" class="form-control select2"></select>
        </div>

        <div class="col-md-3 mb-3">
            <label>Select Child Course</label>
            <select id="child_course_id" class="form-control select2"></select>
        </div>

        <div class="col-md-3 mb-3">
            <label>Select Session</label>
            <select id="session_name" class="form-control select2"></select>
        </div>

    </div>

    <hr>

    <div id="student_result_table" class="">
        <table id="studentsTable" class="table table-bordered table-striped w-100 d-none">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Student UID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>

        <div id="placeholder_box" class="p-5 text-center border rounded bg-light">
            <h4 class="fw-bold text-secondary mb-2">No Data Loaded</h4>
            <p class="text-muted">Please select all filters to load year-wise students.</p>
        </div>
    </div>

</div>

@endsection

@section('script')

<script>
$(document).ready(function () {

    $('.select2').select2();

    let table = null;

    function showPlaceholder() {
        $('#studentsTable').addClass('d-none');
        $('#placeholder_box').removeClass('d-none');
    }

    function showTable() {
        $('#studentsTable').removeClass('d-none');
        $('#placeholder_box').addClass('d-none');
    }

    $('#university_id').select2({
        placeholder: 'Select University',
        ajax: {
            url: '{{ route("education.university.allUniversities") }}',
            dataType: 'json',
            processResults: data => ({
                results: data.map(u => ({ id: u.id, text: u.org_name }))
            })
        }
    });

    $('#university_id').on('change', function () {
        resetFilters(['#college_id','#parent_course_id','#child_course_id','#session_name']);
        showPlaceholder();

        let id = $(this).val();
        $('#college_id').select2({
            placeholder: 'Select College',
            ajax: {
                url: '{{ route("education.college.universitycolleges", ":id") }}'.replace(':id', id),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(c => ({ id: c.id, text: c.org_name }))
                })
            }
        });
    });

    function resetFilters(list) {
        list.forEach(x => $(x).empty().trigger('change'));
    }

    $('#college_id').on('change', function () {
        resetFilters(['#parent_course_id','#child_course_id','#session_name']);
        showPlaceholder();

        let id = $(this).val();
        $('#parent_course_id').select2({
            placeholder: 'Select Parent Course',
            ajax: {
                url: '{{ route("education.collegecourse.parentCourses", ":id") }}'.replace(':id', id),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(pc => ({ id: pc.course_id, text: pc.course_name }))
                })
            }
        });
    });

    $('#parent_course_id').on('change', function () {
        resetFilters(['#child_course_id','#session_name']);
        showPlaceholder();

        let pc = $(this).val();
        let college = $('#college_id').val();

        $('#child_course_id').select2({
            placeholder: 'Select Child Course',
            ajax: {
                url: '{{ route("education.collegecourse.childCourses", ["college"=>":cid","id"=>":pid"]) }}'
                    .replace(':cid', college)
                    .replace(':pid', pc),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(cc => ({ id: cc.course_id, text: cc.course_name }))
                })
            }
        });
    });

    $('#child_course_id').on('change', function () {
        resetFilters(['#session_name']);
        showPlaceholder();

        $('#session_name').select2({
                placeholder: 'Select Session',
                ajax: {
                    url: '{{ route("coursefee.sessions") }}',
                    dataType: 'json',
                    processResults: function(data) {

                        console.log("Session API Response:", data); // 🔥 LOG THE DATA HERE

                        return {
                            results: (data.sessions || []).map(s => ({
                                id: s,
                                text: s
                            }))
                        };
                    }
                }
            });
    });

    $('#session_name').on('change', function () {
        let college = $('#college_id').val();
        let course  = $('#child_course_id').val();
        let session = $('#session_name').val();

        if (!college || !course || !session) {
            showPlaceholder();
            return;
        }

        showTable();
        loadDataTable(college, course, session);
    });

    function loadDataTable(college, course, session) {

        if (table !== null) {
            table.destroy();
            $('#studentsTable tbody').empty();
        }

        table = $('#studentsTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('education.coursestudent.yearwise.datatable') }}",
                type: "POST",
                data: {
                    college_id: college,
                    course_id: course,
                    session_name: session,
                    _token: "{{ csrf_token() }}"
                }
            },
            columns: [
                { data: 'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false },
                { data: 'student_uid', name:'student_uid' },
                { data: 'full_name', name:'full_name' },
                { data: 'primary_email', name:'primary_email' },
                { data: 'primary_phone', name:'primary_phone' },
                { data: 'status', name:'status' },
                { data: 'action', name:'action', orderable:false, searchable:false }
            ]
        });
    }

});
</script>

@endsection
