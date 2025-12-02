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

    <div id="student_result_table" class="text-center border rounded bg-light p-2">
        <div class="p-5">
            <h4 class="fw-bold text-secondary mb-2">No Data Loaded</h4>
            <p class="text-muted">Please select all filters to load year-wise students.</p>
            
        </div>
    </div>

</div>

@endsection

@section('script')

<script>
$(document).ready(function () {

    function placeholderBox() {
        return `
            <div class="p-5 text-center border rounded bg-light">
                <h4 class="fw-bold text-secondary mb-2">No Data Loaded</h4>
                <p class="text-muted">Please select all filters to load year-wise students.</p>
            </div>
        `;
    }

    $('.select2').select2();

    // -----------------------
    // UNIVERSITY
    // -----------------------
    $('#university_id').select2({
        placeholder: 'Select University',
        ajax: {
            url: '{{ route("education.university.allUniversities") }}',
            dataType: 'json',
            processResults: data => ({
                results: data.map(u => ({
                    id: u.id,
                    text: u.org_name
                }))
            })
        }
    });

    // -----------------------
    // UNIVERSITY → COLLEGE
    // -----------------------
    $('#university_id').on('change', function () {
        $('#college_id,#parent_course_id,#child_course_id,#session_name')
            .empty().trigger('change');
        $('#student_result_table').html(placeholderBox());

        let id = $(this).val();

        $('#college_id').select2({
            placeholder: 'Select College',
            ajax: {
                url: '{{ route("education.college.universitycolleges", ":id") }}'.replace(':id', id),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(c => ({
                        id: c.id,
                        text: c.org_name
                    }))
                })
            }
        });
    });

    // -----------------------
    // COLLEGE → PARENT COURSE
    // -----------------------
    $('#college_id').on('change', function () {
        $('#parent_course_id,#child_course_id,#session_name').empty().trigger('change');
        $('#student_result_table').html(placeholderBox());

        let id = $(this).val();

        $('#parent_course_id').select2({
            placeholder: 'Select Parent Course',
            ajax: {
                url: '{{ route("education.collegecourse.parentCourses", ":id") }}'.replace(':id', id),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(pc => ({
                        id: pc.course_id,
                        text: pc.course_name
                    }))
                })
            }
        });
    });

    // -----------------------
    // PARENT → CHILD COURSE
    // -----------------------
    $('#parent_course_id').on('change', function () {
        $('#child_course_id,#session_name').empty().trigger('change');
        $('#student_result_table').html(placeholderBox());

        let pid = $(this).val();
        let cid = $('#college_id').val();

        $('#child_course_id').select2({
            placeholder: 'Select Child Course',
            ajax: {
                url: '{{ route("education.collegecourse.childCourses", ["college"=>":cid","id"=>":pid"]) }}'
                        .replace(':cid', cid)
                        .replace(':pid', pid),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(cc => ({
                        id: cc.course_id,
                        text: cc.course_name
                    }))
                })
            }
        });
    });

    $('#child_course_id').on('change', function () {
        $('#session_name').empty().trigger('change');
        $('#student_result_table').html(placeholderBox());

        $('#session_name').select2({
            placeholder: 'Select Session',
            ajax: {
                url: '{{ route("coursefee.sessions") }}',
                dataType: 'json',
                processResults: data => ({
                    results: data.map(s => ({
                        id: s,
                        text: s
                    }))
                })
            }
        });
    });

    $('#session_name').on('change', function () {

        let college = $('#college_id').val();
        let child = $('#child_course_id').val();
        let session = $(this).val();

        if (!college || !child || !session) {
            $('#student_result_table').html(placeholderBox());
            return;
        }

        $.ajax({
            url: "{{ route('education.coursestudent.yearwise') }}",
            type: "POST",
            data: {
                college_id: college,
                course_id: child,
                session_name: session
            },
            success: function(res) {
                renderStudents(res.data);
            }
        });

    });

    function renderStudents(data) {

        if (!data || data.length === 0) {
            $('#student_result_table').html(`
                <div class="alert alert-warning text-center">No students found for this year.</div>
            `);
            return;
        }

        let html = `
            <div class="table-responsive m-3">
            <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Student UID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
        `;

        data.forEach((s, i) => {
            html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${s.student_uid}</td>
                    <td>${s.full_name}</td>
                    <td>${s.primary_email}</td>
                    <td>${s.primary_phone}</td>
                    <td>${s.status}</td>
                </tr>
            `;
        });

        html += `
            </tbody>
            </table>
            </div>
        `;

        $('#student_result_table').html(html);
    }

});
</script>

@endsection
