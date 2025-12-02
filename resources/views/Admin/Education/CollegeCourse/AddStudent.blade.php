@extends('Admin.layout.app')

@section('title', 'College Student Enrollment')

@section('content')

<div class="page-content">

    <x-breadcrumb title="College Student Enrollment"
        :links="['Home' => 'Admin.Dashboard', 'College Student Enrollment' => '']" />

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
            <p class="text-muted">Please select all filters to load students.</p>
            <a href="{{route('education.coursestudent.assignedstudents')}}" class="btn btn-primary">View Assign Students</a>
        </div>
    </div>

    <div class="mt-3 text-end">
        <button id="assignStudentsBtn" class="btn btn-primary d-none">
            Assign Selected Students
        </button>
    </div>

</div>
@endsection

@section('script')

<style>
.fade-out {
    opacity: 0;
    transition: opacity 0.7s ease-out;
}
.spinner-border-sm {
    margin-right: 6px;
}
</style>

<script>
$(document).ready(function () {

    function placeholderBox() {
        return `
            <div class="p-5 text-center border rounded bg-light">
                <h4 class="fw-bold text-secondary mb-2">No Data Loaded</h4>
                <p class="text-muted">Please select all filters to load students .</p>
                <a href="{{route('education.coursestudent.assignedstudents')}}" class="btn btn-primary">View Assign Students</a>
            </div>
        `;
    }

    $('.select2').select2();

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

        $('#college_id').val(null).trigger('change');
        $('#parent_course_id').val(null).trigger('change');
        $('#child_course_id').val(null).trigger('change');
        $('#session_name').val(null).trigger('change');
        $('#student_result_table').html(placeholderBox());

        let id = $(this).val();
        if (!id) return;

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

    $('#college_id').on('change', function () {

        $('#parent_course_id').val(null).trigger('change');
        $('#child_course_id').val(null).trigger('change');
        $('#session_name').val(null).trigger('change');
        $('#student_result_table').html(placeholderBox());

        let id = $(this).val();
        if (!id) return;

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

        $('#child_course_id').val(null).trigger('change');
        $('#session_name').val(null).trigger('change');
        $('#student_result_table').html(placeholderBox());

        let parentId = $(this).val();
        let collegeId = $('#college_id').val();
        if (!parentId || !collegeId) return;

        $('#child_course_id').select2({
            placeholder: 'Select Child Course',
            ajax: {
                url: '{{ route("education.collegecourse.childCourses", ["college"=>":cid","id"=>":pid"]) }}'
                    .replace(':cid', collegeId)
                    .replace(':pid', parentId),
                dataType: 'json',
                processResults: data => ({
                    results: data.data.map(cc => ({ id: cc.course_id, text: cc.course_name }))
                })
            }
        });
    });

    $('#child_course_id').on('change', function () {

        $('#session_name').val(null).trigger('change');
        $('#student_result_table').html(placeholderBox());

        $('#session_name').select2({
            placeholder: 'Select Session',
            ajax: {
                url: '{{ route("coursefee.sessions") }}',
                dataType: 'json',
                processResults: data => ({
                    results: data.map(s => ({ id: s, text: s }))
                })
            }
        });
    });

    $('#session_name').on('change', function () {

        let college = $('#college_id').val();
        let parent = $('#parent_course_id').val();
        let child = $('#child_course_id').val();
        let session = $(this).val();

        if (!college || !parent || !child || !session) {
            $('#student_result_table').html(placeholderBox());
            return;
        }

        $.ajax({
            url: "{{ route('education.coursestudent.getStudents') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                college_id: college,
                parent_course_id: parent,
                course_id: child,
                session_name: session
            },
            success: function (res) {
                renderStudents(res.data);
            }
        });
    });

    function renderStudents(data) {

        if (!data || data.length === 0) {
            $('#student_result_table').html(`
                <div class="alert alert-warning text-center">No students found.</div>
            `);
            $('#assignStudentsBtn').addClass('d-none');
            return;
        }

        let html = `
            <div class="table-responsive m-3" id="tableWrapper">
            <table class="table table-bordered" id="studentTable">
            <thead class="bg-light">
                <tr>
                    <th><input type="checkbox" id="select_all"></th>
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
                    <td><input type="checkbox" class="student_checkbox" data-id="${s.id}" data-name="${s.full_name}"></td>
                    <td>${i + 1}</td>
                    <td>${s.student_uid}</td>
                    <td>${s.full_name ?? ''}</td>
                    <td>${s.primary_email ?? ''}</td>
                    <td>${s.primary_phone ?? ''}</td>
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
        $('#assignStudentsBtn').removeClass('d-none');

        $('#select_all').on('change', function () {
            $('.student_checkbox').prop('checked', $(this).prop('checked'));
        });
    }

    $('#assignStudentsBtn').on('click', function () {

        let selected = [];

        $('.student_checkbox:checked').each(function () {
            selected.push({
                id: $(this).data('id'),
                name: $(this).data('name')
            });
        });

        if (selected.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        $('#assignStudentsBtn').prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm"></span> Assigning...
        `);

        $('#studentTable input').prop('disabled', true);
        $('.select2').prop('disabled', true);

        $.ajax({
            url: "{{ route('education.coursestudent.store') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                college_id: $('#college_id').val(),
                course_id: $('#child_course_id').val(),
                session_year_name: $('#session_name').val(),
                session_start: "2024-07-01",
                session_end: "2025-06-30",
                students: selected
            },
            success: function (res) {

                swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $('#tableWrapper').addClass('fade-out');

                setTimeout(() => {
                    resetAllUI();
                }, 700);
            }
        });
    });

    function resetAllUI() {

        $('#assignStudentsBtn')
            .prop('disabled', false)
            .addClass('d-none')
            .html('Assign Selected Students');

        $('.select2').prop('disabled', false);

        $('#student_result_table').html(placeholderBox());

        $('#university_id').val(null).trigger('change');
        $('#college_id').val(null).trigger('change');
        $('#parent_course_id').val(null).trigger('change');
        $('#child_course_id').val(null).trigger('change');
        $('#session_name').val(null).trigger('change');
    }

});
</script>

@endsection
