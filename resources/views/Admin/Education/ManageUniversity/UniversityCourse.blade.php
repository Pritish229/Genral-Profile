@extends('Admin.layout.app')

@section('title', 'University Courses')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="University Courses"
        :links="[
            'Home' => 'Admin.Dashboard',
            'University Master' => 'education.university.index',
            'University Courses' => ''
        ]" />

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="fw-bold mb-1">University: {{ $university->org_name }}</h4>
                <p class="text-muted small mb-0">
                    {{ $university->city }}, {{ $university->state }}
                    | {{ $university->email_id }}
                    | {{ $university->phone_no }}
                </p>
            </div>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignCourseModal">
                + Assign Course
            </button>
        </div>
    </div>

    <div class="card p-4 mt-3">
        <h5 class="fw-bold mb-3">Assigned Courses</h5>
        <table id="ucTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>C</th>
                    <th>Parent</th>
                    <th>Duration</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th width="80">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="assignCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="assignCourseForm">
            @csrf
            <input type="hidden" name="university_id" value="{{ $university->id }}">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Courses</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Parent Course</label>
                            <select id="parent_course_id" name="parent_course_id" class="form-select"></select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Child Courses</label>
                            <select id="child_course_ids" name="child_course_ids[]" class="form-select" multiple></select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Assign</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    const universityId = "{{ $university->id }}";

    $('#parent_course_id').select2({
        dropdownParent: $('#assignCourseModal'),
        width: '100%',
        placeholder: 'Select Parent Course'
    });

    $('#child_course_ids').select2({
        dropdownParent: $('#assignCourseModal'),
        width: '100%',
        placeholder: 'Select Child Courses'
    });

    $.get("{{ route('education.course.parentCourses') }}", res => {
        let html = `<option value="">Select Parent</option>`;
        res.data.forEach(r => html += `<option value="${r.id}">${r.course_name}</option>`);
        $('#parent_course_id').html(html);
    });

    $('#parent_course_id').on('change', function () {
        const parentId = $(this).val();
        if (!parentId) {
            $('#child_course_ids').html('').trigger('change');
            return;
        }
        const url = "{{ route('education.course.childCourses', ':id') }}".replace(':id', parentId);
        $.get(url, res => {
            let html = '';
            res.data.forEach(r => html += `<option value="${r.id}">${r.course_name}</option>`);
            $('#child_course_ids').html(html).trigger('change');
        });
    });

    const table = $('#ucTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('education.universitycourse.paginate') }}",
            data: function (d) {
                d.university_id = universityId;
            }
        },
        columns: [
            { data: 'course_name', name: 'course_name' },
            { data: 'course_code', name: 'course_code' },
            { data: 'parent', name: 'parent' },
            { data: 'duration', name: 'duration' },
            { data: 'type', name: 'type' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#assignCourseForm').submit(function(e) {
        e.preventDefault();
        let form = new FormData(this);
        $.ajax({
            url: "{{ route('education.universitycourse.store') }}",
            type: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: res => {
                if (res.status) {
                    $('#assignCourseModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Success', 'Assigned Successfully', 'success');
                }
            }
        });
    });

    $(document).on('click', '.deleteUC', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete?',
            icon: 'warning',
            showCancelButton: true
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: "{{ route('education.universitycourse.delete', ':id') }}".replace(':id', id),
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: () => {
                        table.ajax.reload();
                        Swal.fire('Deleted', '', 'success');
                    }
                });
            }
        });
    });
    
});
</script>
@endsection