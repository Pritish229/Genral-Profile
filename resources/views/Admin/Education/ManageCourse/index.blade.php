@extends('Admin.layout.app')

@section('title', 'Home | Courses Master')

@section('content')

<div class="page-content">
    <x-breadcrumb title="Courses Master" :links="['Home' => 'Admin.Dashboard', 'Courses Master' => '']" />

    <div class="p-1">
        <div class="d-flex justify-content-between">
            <div></div>
            <div class="my-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">+ Add Course</button>
            </div>
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="courseTable">
                <thead>
                    <tr>
                        <th>Parent</th>
                        <th>Course Name</th>
                        <th>Code</th>
                        <th>Active</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

{{-- ADD COURSE MODAL --}}
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addCourseForm">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add Course</h5>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Parent Course</label>
                            <select id="add_parent_id" name="parent_id" class="form-select">
                                <option value="">-- None --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <x-inputbox id="add_name" type="text" name="course_name" label="Course Name"
                                placeholder="Enter Course Name" :required="true" value="" helpertxt="" />
                        </div>

                        <div class="col-md-6">
                            <x-switch-toggle id="add_is_parent" name="is_parent" :checked="true" label="Is Parent" />
                        </div>

                        <div class="col-md-12">
                            <x-inputbox id="add_code" type="text" name="course_code" label="Course Code"
                                placeholder="Enter Course Code" :required="false" value="" helpertxt="" />
                        </div>

                        <div class="col-md-12">
                            <x-textareabox id="add_description" label="Description" name="description"
                                placeholder="Enter course description (optional)" value="" helpertxt="" />
                        </div>

                        <div class="col-md-6">
                            <x-switch-toggle id="add_is_active" name="is_active" :checked="true" label="Active" />
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- EDIT COURSE MODAL --}}
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editCourseForm">@csrf
            <input type="hidden" id="edit_course_id">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">



                        <div class="col-md-6">
                            <label>Parent Course</label>
                            <select id="edit_parent_id" name="parent_id" class="form-select">
                                <option value="">-- None --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <x-inputbox id="edit_name" type="text" name="course_name" label="Course Name"
                                placeholder="Enter Course Name" :required="true" value="" helpertxt="" />
                        </div>

                        <div class="col-md-6">
                            <x-switch-toggle id="edit_is_parent" name="is_parent" :checked="false" label="Is Parent" />
                        </div>

                        <div class="col-md-12">
                            <x-inputbox id="edit_code" type="text" name="course_code" label="Course Code"
                                placeholder="Enter Course Code" :required="false" value="" helpertxt="" />
                        </div>

                        <div class="col-md-12">
                            <x-textareabox id="edit_description" label="Description" name="description"
                                placeholder="Enter course description (optional)" value="" helpertxt="" />
                        </div>

                        <div class="col-md-6">
                            <x-switch-toggle id="edit_is_active" name="is_active" :checked="false" label="Active" />
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </div>

        </form>
    </div>
</div>

@endsection


@section('script')
<script>
    $(function() {

        const initParentSelects = () => {
            $('#add_parent_id').select2({
                width: "100%",
                dropdownParent: $('#addCourseModal')
            });
            $('#edit_parent_id').select2({
                width: "100%",
                dropdownParent: $('#editCourseModal')
            });
        };

        const loadParents = (exclude_id = null, callback = null) => {
            $.get("{{ route('education.course.parentCourses') }}", res => {

                let options = `<option value="">-- None --</option>`;

                res.data.forEach(c => {
                    if (exclude_id && c.id == exclude_id) return;
                    options += `<option value="${c.id}">${c.course_name}</option>`;
                });

                $('#add_parent_id').html(options);
                $('#edit_parent_id').html(options);

                initParentSelects();

                if (callback) callback();
            });
        };
        loadParents();

        const toggleAddParent = () => {
            let isParent = $('#add_is_parent').is(':checked');
            if (isParent) {
                $('#add_parent_id').val('').trigger('change')
                    .prop('disabled', true).closest('.col-md-6').hide();
            } else {
                $('#add_parent_id')
                    .prop('disabled', false).closest('.col-md-6').show();
            }
        };

        const toggleEditParent = () => {
            let isParent = $('#edit_is_parent').is(':checked');
            if (isParent) {
                $('#edit_parent_id').val('').trigger('change')
                    .prop('disabled', true).closest('.col-md-6').hide();
            } else {
                $('#edit_parent_id')
                    .prop('disabled', false).closest('.col-md-6').show();
            }
        };

        toggleAddParent();
        toggleEditParent();

        $('#add_is_parent').on('change', toggleAddParent);
        $('#edit_is_parent').on('change', toggleEditParent);

        const table = $('#courseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('education.course.paginate') }}",
            columns: [{
                    data: 'parent',
                    orderable: false
                },
                {
                    data: 'course_name',
                    orderable: false
                },
                {
                    data: 'course_code',
                    orderable: false
                },
                {
                    data: 'is_active',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#addCourseForm').on('submit', function(e) {
            e.preventDefault();

            let isParent = $('#add_is_parent').is(':checked') ? 'true' : 'false';

            let formData = new FormData(this);
            formData.set('is_parent', isParent);

            if (isParent === 'true') formData.set('parent_id', '');

            $.ajax({
                url: "{{ route('education.course.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: r => {
                    $('#addCourseModal').modal('hide');
                    table.ajax.reload();
                    loadParents();
                    $('#addCourseForm')[0].reset();
                    $('#add_parent_id').val('').trigger('change');
                    $('#add_is_active').prop('checked', true);
                    $('#add_is_parent').prop('checked', true); // default parent
                    toggleAddParent();
                    Swal.fire("Success", r.message, "success");
                },
                error: err => {
                    let msg = Object.values(err.responseJSON?.errors || {
                        error: err.responseJSON?.message
                    })[0];
                    Swal.fire("Error", msg, "error");
                }
            });
        });

        $(document).on('click', '.editCourse', function() {

            let id = $(this).data('id');

            $.get("{{ route('education.course.edit',':id') }}".replace(':id', id), d => {

                loadParents(id, function() {

                    $('#edit_course_id').val(d.id);
                    $('#edit_name').val(d.course_name);
                    $('#edit_code').val(d.course_code);
                    $('#edit_description').val(d.description);
                    $('#edit_is_active').prop('checked', d.is_active == 1);
                    $('#edit_is_parent').prop('checked', d.is_parent === "true");

                    if (d.is_parent === "true") {
                        $('#edit_parent_id').val('').trigger('change')
                            .prop('disabled', true).closest('.col-md-6').hide();
                    } else {
                        $('#edit_parent_id').prop('disabled', false).closest('.col-md-6').show();
                        $('#edit_parent_id').val(d.parent_id || '').trigger('change');
                    }

                    $('#editCourseModal').modal('show');
                });

            }).fail(() => {
                Swal.fire("Error", "Unable to load course", "error");
            });
        });

        $('#editCourseForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#edit_course_id').val();
            let isParent = $('#edit_is_parent').is(':checked') ? 'true' : 'false';

            let formData = new FormData(this);
            formData.set('is_parent', isParent);

            if (isParent === 'true') formData.set('parent_id', '');

            $.ajax({
                url: "{{ route('education.course.update',':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: r => {
                    $('#editCourseModal').modal('hide');
                    table.ajax.reload();
                    loadParents();
                    Swal.fire("Success", r.message, "success");
                },
                error: err => {
                    let msg = Object.values(err.responseJSON?.errors || {
                        error: err.responseJSON?.message
                    })[0];
                    Swal.fire("Error", msg, "error");
                }
            });
        });

        $(document).on('click', '.deleteCourse', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Delete this course?",
                icon: "warning",
                showCancelButton: true
            }).then(res => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: "{{ route('education.course.delete',':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: () => {
                            table.ajax.reload();
                            loadParents();
                            Swal.fire("Deleted", "Course deleted successfully", "success");
                        }
                    });
                }
            });
        });

    });
</script>

@endsection