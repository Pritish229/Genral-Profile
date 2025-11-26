@extends('Admin.layout.app')

@section('title', 'Home | Courses Master')

@section('content')

<div class="page-content">
    <x-breadcrumb title="Courses Master" :links="['Home' => 'Admin.Dashboard', 'Courses Master' => '']" />

    <div class="p-1">
        <div class="d-flex justify-content-between">
            <div></div>
            <div class="my-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    + Add Course
                </button>
            </div>
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="courseTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Parent</th>
                        <th>Course Name</th>
                        <th>Code</th>
                        <th>Type</th>
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
        <form id="addCourseForm" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Course</h5>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6 parent-wrapper-add">
                            <label>Parent Course</label>
                            <select id="add_parent_id" name="parent_id" class="form-select">
                                <option value="">-- None --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <x-inputbox id="add_name" type="text" name="course_name"
                                label="Course Name" placeholder="Enter Course Name"
                                :required="true" value="" helpertxt="Course name must be unique" />
                        </div>

                        <div class="col-md-6">
                            <x-inputbox id="add_code" type="text" name="course_code"
                                label="Course Code" placeholder="Enter Course Code"
                                :required="false" value="" helpertxt="Optional code" />
                        </div>

                        <div class="col-md-6">
                            <x-switch-toggle id="add_is_parent" name="is_parent" :checked="true" label="Is Parent" />
                        </div>

                        <div class="col-md-12">
                            <label class="labeltxt mb-1" for="add_duration">Course Duration</label>
                            <select id="add_duration" name="course_duration" class="form-select">
                                <option value="">Select Duration</option>
                                <option value="1">1 Year</option>
                                <option value="2">2 Years</option>
                                <option value="3">3 Years</option>
                                <option value="4">4 Years</option>
                                <option value="5">5 Years</option>
                            </select>
                            <small class="helpertxt">Select between 1 to 5 years</small>
                        </div>

                        <div class="col-md-12">
                            <label>Course Image</label>
                            <div id="add_course_image" class="input-images"></div>
                        </div>

                        <div class="col-md-12">
                            <x-textareabox id="add_description" label="Description"
                                name="description" placeholder="Enter course description"
                                value="" helpertxt="Optional description" />
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
        <form id="editCourseForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_course_id">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6 parent-wrapper-edit">
                            <label>Parent Course</label>
                            <select id="edit_parent_id" name="parent_id" class="form-select">
                                <option value="">-- None --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <x-inputbox id="edit_name" type="text" name="course_name"
                                label="Course Name" placeholder="Enter Name" :required="true" value="" helpertxt="Course name must be unique" />
                        </div>

                        

                        <div class="col-md-6">
                            <x-inputbox id="edit_code" type="text" name="course_code"
                                label="Course Code" placeholder="Enter Code" :required="false" value="" helpertxt="Optional" />
                        </div>
                        <div class="col-md-6">
                            <x-switch-toggle id="edit_is_parent" name="is_parent" :checked="false" label="Is Parent" />
                        </div>

                        <div class="col-md-12">
                            <label class="labeltxt mb-1" for="edit_duration">Course Duration</label>
                            <select id="edit_duration" name="course_duration" class="form-select">
                                <option value="">Select Duration</option>
                                <option value="1">1 Year</option>
                                <option value="2">2 Years</option>
                                <option value="3">3 Years</option>
                                <option value="4">4 Years</option>
                                <option value="5">5 Years</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label>Course Image</label>
                            <div id="edit_course_image" class="input-images"></div>
                        </div>

                        <div class="col-md-12">
                            <x-textareabox id="edit_description" label="Description"
                                name="description" placeholder="Enter Description"
                                value="" helpertxt="Optional" />
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

        $('#add_parent_id').select2({
            dropdownParent: $('#addCourseModal'),
            width: '100%',
            placeholder: '-- None --',

        });

        $('#edit_parent_id').select2({
            dropdownParent: $('#editCourseModal'),
            width: '100%',
            placeholder: '-- None --',

        });

        $('#add_course_image').imageUploader({
            multiple: false,
            imagesInputName: 'course_image',
            preloaded: []
        });

        function toggleAddParent() {
            let checked = $('#add_is_parent').is(':checked');
            checked ? $('.parent-wrapper-add').hide() : $('.parent-wrapper-add').show();
            $('#add_parent_id').val('').trigger('change');
        }

        function toggleEditParent() {
            let checked = $('#edit_is_parent').is(':checked');
            checked ? $('.parent-wrapper-edit').hide() : $('.parent-wrapper-edit').show();
            $('#edit_parent_id').val('').trigger('change');
        }

        $('#add_is_parent').on('change', toggleAddParent);
        $('#edit_is_parent').on('change', toggleEditParent);

        toggleAddParent();
        toggleEditParent();

        function loadParents(exclude = null) {
            $.get("{{ route('education.course.parentCourses') }}", res => {
                let options = `<option value="">-- None --</option>`;
                res.data.forEach(c => {
                    if (exclude && exclude == c.id) return;
                    options += `<option value="${c.id}">${c.course_name}</option>`;
                });

                $('#add_parent_id').html(options).trigger('change');
                $('#edit_parent_id').html(options).trigger('change');
            });
        }

        loadParents();

        const table = $('#courseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('education.course.paginate') }}",
            columns: [{
                    data: 'image',
                    orderable: false
                },
                {
                    data: 'parent'
                },
                {
                    data: 'course_name'
                },
                {
                    data: 'course_code'
                },
                {
                    data: 'type'
                },
                {
                    data: 'is_active'
                },
                {
                    data: 'action',
                    orderable: false
                }
            ]
        });

        $('#addCourseForm').submit(function(e) {
            e.preventDefault();
            let form = new FormData(this);
            form.set('is_parent', $('#add_is_parent').is(':checked') ? "true" : "false");

            $.ajax({
                url: "{{ route('education.course.store') }}",
                type: "POST",
                data: form,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.restore) {
                        // Show restore popup
                        Swal.fire({
                            title: 'Course Exists!',
                            text: res.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Restore it!',
                            cancelButtonText: 'No, Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.post("{{ url('/courses') }}/" + res.restore_id + "/restore", {
                                    _token: "{{ csrf_token() }}"
                                }, function() {
                                    table.ajax.reload();
                                    $('#addCourseModal').modal('hide');
                                    Swal.fire('Restored!', 'Course has been restored.', 'success');
                                });
                            }
                        });
                    } else if (res.status) {
                        $('#addCourseModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire("Success", res.message, "success");

                        // Reset form
                        $('#addCourseForm')[0].reset();
                        toggleAddParent();
                        $('#add_course_image').empty().imageUploader({
                            multiple: false,
                            imagesInputName: 'course_image',
                            preloaded: []
                        });
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.errors ?
                        Object.values(xhr.responseJSON.errors)[0][0] :
                        'Something went wrong';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $(document).on('click', '.editCourse', function() {

            let id = $(this).data('id');

            $.get("{{ route('education.course.edit', ':id') }}".replace(':id', id), res => {

                loadParents(id);

                $('#edit_course_id').val(res.course.id);
                $('#edit_name').val(res.course.course_name);
                $('#edit_code').val(res.course.course_code);
                $('#edit_duration').val(res.course.course_duration);
                $('#edit_description').val(res.course.description);
                $('#edit_is_active').prop('checked', res.course.is_active == 1);
                $('#edit_is_parent').prop('checked', res.course.is_parent == 1);

                toggleEditParent();

                $('#edit_course_image').empty().imageUploader({
                    multiple: false,
                    imagesInputName: 'course_image',
                    preloaded: res.preloaded
                });

                $('#editCourseModal').modal('show');
            });
        });


        $('#editCourseForm').submit(function(e) {
            e.preventDefault();

            let id = $('#edit_course_id').val();
            let form = new FormData(this);

            form.set('is_parent', $('#edit_is_parent').is(':checked') ? "true" : "false");

            $.ajax({
                url: "{{ route('education.course.update', ':id') }}".replace(':id', id),
                type: "POST",
                data: form,
                processData: false,
                contentType: false,
                success: res => {
                    $('#editCourseModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire("Success", res.message, "success");
                }
            });
        });


        $(document).on('click', '.deleteCourse', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: "Delete this course?",
                icon: "warning",
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: "{{ route('education.course.delete', ':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: () => {
                            table.ajax.reload();
                            Swal.fire("Deleted!", "Course deleted.", "success");
                        }
                    });
                }
            });

        });

    });
</script>
@endsection