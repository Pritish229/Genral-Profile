@extends('Admin.layout.app')

@section('title', 'View Course Fees')

@section('content')

<div class="page-content">
    <x-breadcrumb title="View Assigned Course Fees"
        :links="['Home' => 'Admin.Dashboard', 'College Course Fees' => 'coursefee.index' , 'View Assigned Course Fees'=>'']" />

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

    <div id="fee_result_table" class=" text-center border rounded bg-light p-2">
        <div class="p-5">
            <h4 class="fw-bold text-secondary mb-2">No Data Loaded</h4>
            <p class="text-muted">Please select University, College, Parent Course, Child Course, and Session.</p>
        </div>
    </div>

</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {

        function placeholderBox() {
            return `
                <div class="p-5 text-center border rounded bg-light">
                    <h4 class="fw-bold text-secondary mb-2">No Data Loaded</h4>
                    <p class="text-muted">Please select University, College, Parent Course, Child Course, and Session.</p>
                </div>
            `;
        }

        $('.select2').select2();

        $('#university_id').select2({
            placeholder: 'Select University',
            ajax: {
                url: '{{ route("education.university.allUniversities") }}',
                dataType: 'json',
                delay: 250,
                processResults: data => ({
                    results: data.map(item => ({
                        id: item.id,
                        text: item.org_name
                    }))
                })
            }
        });

        $('#university_id').on('change', function() {
            $('#college_id,#parent_course_id,#child_course_id,#session_name').empty().trigger('change');
            $('#fee_result_table').html(placeholderBox());
            let id = $(this).val();
            $('#college_id').select2({
                placeholder: 'Select College',
                ajax: {
                    url: '{{ route("education.college.universitycolleges", ":id") }}'.replace(':id', id),
                    dataType: 'json',
                    processResults: data => ({
                        results: data.data.map(item => ({
                            id: item.id,
                            text: item.org_name
                        }))
                    })
                }
            });
        });

        $('#college_id').on('change', function() {
            $('#parent_course_id,#child_course_id,#session_name').empty().trigger('change');
            $('#fee_result_table').html(placeholderBox());
            let id = $(this).val();
            $('#parent_course_id').select2({
                placeholder: 'Select Parent Course',
                ajax: {
                    url: '{{ route("education.collegecourse.parentCourses", ":id") }}'.replace(':id', id),
                    dataType: 'json',
                    processResults: data => ({
                        results: data.data.map(item => ({
                            id: item.course_id,
                            text: item.course_name
                        }))
                    })
                }
            });
        });

        $('#parent_course_id').on('change', function() {
            $('#child_course_id,#session_name').empty().trigger('change');
            $('#fee_result_table').html(placeholderBox());
            let pid = $(this).val();
            let cid = $('#college_id').val();
            $('#child_course_id').select2({
                placeholder: 'Select Child Course',
                ajax: {
                    url: '{{ route("education.collegecourse.childCourses", ["college"=>":cid","id"=>":pid"]) }}'
                        .replace(':cid', cid).replace(':pid', pid),
                    dataType: 'json',
                    processResults: data => ({
                        results: data.data.map(item => ({
                            id: item.course_id,
                            text: item.course_name
                        }))
                    })
                }
            });
        });

        $('#child_course_id').on('change', function() {
            $('#session_name').empty().trigger('change');
            $('#fee_result_table').html(placeholderBox());
            $('#session_name').select2({
                placeholder: 'Select Session',
                ajax: {
                    url: '{{ route("coursefee.sessions") }}',
                    dataType: 'json',
                    delay: 200,
                    processResults: data => ({
                        results: data.map(s => ({
                            id: s,
                            text: s
                        }))
                    })
                }
            });
        });

        $('#session_name').on('change', function() {
            let college = $('#college_id').val();
            let parent = $('#parent_course_id').val();
            let child = $('#child_course_id').val();
            let session = $(this).val();

            if (!college || !parent || !child || !session) {
                $('#fee_result_table').html(placeholderBox());
                return;
            }

            $.ajax({
                url: "{{ route('coursefee.list') }}",
                type: "GET",
                data: {
                    college_id: college,
                    parent_course_id: parent,
                    course_id: child,
                    session_name: session
                },
                success: function(res) {
                    renderFeeTable(res.data);
                }
            });
        });

        function renderFeeTable(data) {

            if (data.length === 0) {
                $('#fee_result_table').html(`
                    <div class="alert alert-warning text-center">
                        No fee records found.
                    </div>
                `);
                return;
            }

            let total = 0;
            data.forEach(x => total += parseFloat(x.total_amount));

            let html = `
                <div class="table-responsive m-3">
                <table class="table table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Fee Head</th>
                        <th>Collection</th>
                        <th>Amount</th>
                        <th>Times</th>
                        <th>Session</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
            `;

            data.forEach((f, i) => {
                html += `
                    <tr>
                        <td>${i+1}</td>
                        <td>${f.fee_head}</td>
                        <td>${f.collection_type}</td>
                        <td>${f.amount}</td>
                        <td>${f.times_in_year}</td>
                        <td>${f.session_name}</td>
                        <td>${f.total_amount}</td>
                    </tr>
                `;
            });

            html += `
                </tbody>
                <tfoot>
                    <tr class="bg-light fw-bold">
                        <td colspan="6" class="text-end">Final Total Amount:</td>
                        <td class="text-success">${total} Rs</td>
                    </tr>
                </tfoot>
                </table>
                </div>
            `;

            $('#fee_result_table').html(html);
        }

    });
</script>

@endsection