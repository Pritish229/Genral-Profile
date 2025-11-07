@extends('Admin.layout.app')

@section('title', 'Home | Manage Courses')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Manage Courses" :links="['Home' => 'Admin.Dashboard', 'Manage Courses' => '']" />

    <div class="p-1 ">

        <div class="d-flex justify-content-between">
            <div class=" mb-3 ">
                <label class="form-label">Filter by Session Year</label>
                <select id="filter_session_year" class="form-select"></select>
            </div>
            <div class="pt-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">+ Add Course</button>
            </div>

            
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="courseTable">
            <thead>
                <tr>
                    <th>Session Year</th>
                    <th>Course Name</th>
                    <th>Code</th>
                    <th>Active</th>
                    <th width="120px">Actions</th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addCourseForm">@csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Course</h5></div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Session Year</label>
                        <select id="add_session_year_id" name="session_year_id" class="form-select" required></select>
                    </div>

                    <x-inputbox id="add_name" type="text" name="course_name" label="Course Name" placeholder="Enter Course Name" :required="true" />
                    <x-inputbox id="add_code" type="text" name="course_code" label="Course Name" placeholder="Enter Course Code" :required="true" />
                    <x-textareabox id="description" label="Description" placeholder="Enter description" name="description" value="{{ old('description') }}" helpertxt="Optional: Add  description." />
                    
                    <div class="form-check">
                        <input type="checkbox" id="add_is_active" name="is_active" value="1" class="form-check-input">
                        <label for="add_is_active" class="form-check-label">Active</label>
                    </div>
                    
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Add</button></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editCourseForm">@csrf
            <input type="hidden" id="edit_course_id">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Course</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Session Year</label>
                        <select id="edit_session_year_id" name="session_year_id" class="form-select" required></select>
                    </div>

                    <x-inputbox id="edit_name" type="text" name="course_name" label="Course Name" placeholder="Enter Course Name" :required="true" />
                    <x-inputbox id="edit_code" type="text" name="course_code" label="Course Code" placeholder="Enter Course Code" :required="true" />
                    <x-textareabox id="edit_description" label="Description" placeholder="Enter description" name="description" value="{{ old('description') }}" helpertxt="Optional: Add  description." />

                    <div class="form-check">
                        <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="form-check-input">
                        <label for="edit_is_active" class="form-check-label">Active</label>
                    </div>

                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {

    function initSelect2() {
        $('#add_session_year_id').select2({ width: '100%', dropdownParent: $('#addCourseModal') });
        $('#edit_session_year_id').select2({ width: '100%', dropdownParent: $('#editCourseModal') });
        $('#filter_session_year').select2({ width: '100%' });
    }
    initSelect2();

    function loadSessionYears(selectedId = null) {
        $.get("{{ route('education.sessionyear.list') }}", function(data) {
            let options = `<option value="">-- Select Session Year --</option>`;
            data.data.forEach(item => { options += `<option value="${item.id}">${item.name}</option>`; });

            $('#add_session_year_id').html(options);
            $('#edit_session_year_id').html(options);
            $('#filter_session_year').html(options);

            initSelect2();

            if(selectedId){
                $('#edit_session_year_id').val(selectedId).trigger('change');
            }
        });
    }
    loadSessionYears();

    let table = $('#courseTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('education.course.paginate') }}",
            data: function(d){ d.session_year_id = $('#filter_session_year').val(); }
        },
        columns: [
           
            { data: 'session_year' },
            { data: 'course_name' },
            { data: 'course_code' },
            { data: 'is_active', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#filter_session_year').change(() => table.ajax.reload());

    $('#addCourseForm').submit(function(e){
        e.preventDefault();
        $.post("{{ route('education.course.store') }}", $(this).serialize(), function(res){
            $('#addCourseModal').modal('hide'); table.ajax.reload();
            Swal.fire('Success', res.message, 'success');
        });
    });

    $(document).on('click', '.editCourse', function(){
        let id = $(this).data('id');
        $.get("{{ url('education/courses') }}/" + id + "/edit", function(data){
            loadSessionYears(data.session_year_id);
            $('#edit_course_id').val(data.id);
            $('#edit_name').val(data.course_name);
            $('#edit_code').val(data.course_code);
            $('#edit_description').val(data.description);
            $('#edit_is_active').prop('checked', data.is_active == 1);
            $('#editCourseModal').modal('show');
        });
    });

    $('#editCourseForm').submit(function(e){
        e.preventDefault();
        let id = $('#edit_course_id').val();
        $.post("{{ route('education.course.update', ':id') }}".replace(':id', id), $(this).serialize(), function(res){
            $('#editCourseModal').modal('hide'); table.ajax.reload();
            Swal.fire('Success', res.message, 'success');
        });
    });

    $(document).on('click', '.deleteCourse', function(){
        let id = $(this).data('id');
        Swal.fire(
            {
            title: "Course Delete?", 
            text: "Are you sure you want to delete this course?",
            icon: "warning",
            confirmButtonText: "Yes, Delete",
            showCancelButton: true
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: "{{ route('education.course.delete', ':id') }}".replace(':id', id),
                    type: "DELETE",
                    data: {_token:"{{ csrf_token() }}"},
                    success:function(){
                        table.ajax.reload();
                        Swal.fire("Deleted!","Course Removed","success");
                    }
                });
            }
        });
    });

});
</script>
@endsection
