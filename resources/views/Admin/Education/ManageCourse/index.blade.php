@extends('Admin.layout.app')

@section('title', 'Home | Courses Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Courses Master" :links="['Home' => 'Admin.Dashboard', 'Courses Master' => '']" />

    <div class="p-1">
        <div class="d-flex justify-content-between">
            <div class="mb-3">
                <label class="form-label">Filter by Session Year</label>
                <select id="filter_session_year" class="form-select">
                    <option value="">-- Select Session Year --</option>
                </select>
            </div>
            <div class="pt-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">+ Add Course</button>
            </div>
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="courseTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Session Year</th>
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

<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addCourseForm" enctype="multipart/form-data">@csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Course</h5></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Session Year</label>
                            <select id="add_session_year_id" name="session_year_id" class="form-select" required>
                                <option value="">-- Select Session Year --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_name" type="text" name="course_name" label="Course Name" placeholder="Enter Course Name" :required="true" />
                        </div>
                        <div class="col-md-12">
                            <x-inputbox id="add_code" type="text" name="course_code" label="Course Code" placeholder="Enter Course Code" :required="true" />
                        </div>
                        <div class="col-md-12">
                            <label>Course Image</label>
                            <div class="course-images"></div>
                        </div>
                        <div class="col-md-12">
                            <x-textareabox id="description" label="Description" name="description" placeholder="Enter course description (optional)" />
                        </div>
                        <div class="col-md-12">
                            <x-switch-toggle id="add_is_active" name="is_active" :checked="true" label="Active" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a><button type="submit" class="btn btn-primary">Add</button></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editCourseForm" enctype="multipart/form-data">@csrf
            <input type="hidden" id="edit_course_id">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Course</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-inputbox id="edit_name" type="text" name="course_name" label="Course Name" placeholder="Enter Course Name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="edit_code" type="text" name="course_code" label="Course Code" placeholder="Enter Course Code" :required="true" />
                        </div>
                        <div class="col-md-12">
                            <label>Course Image</label>
                            <div class="course-images-edit"></div>
                        </div>
                        <div class="col-md-12">
                            <x-textareabox id="edit_description" label="Description" name="description" placeholder="Enter course description (optional)" />
                        </div>
                        <div class="col-md-12">
                            <x-switch-toggle id="edit_is_active" name="is_active" :checked="old('is_active') ?? false" label="Active" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a><button type="submit" class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(function(){

    const initSelect2 = () => {
        $('#add_session_year_id').select2({width:"100%",dropdownParent:$('#addCourseModal')});
        $('#filter_session_year').select2({width:"100%"});
    };

    const loadSessionYears = () => {
        $.get("{{ route('education.sessionyear.list') }}", d=>{
            let o = `<option value="">-- Select Session Year --</option>`;
            d.data.forEach(i=>{
                let status = i.is_active == 1 ? "(Active)" : "(Inactive)";
                let disable = i.is_active == 0 ? "disabled" : "";
                o += `<option value="${i.id}" ${disable}>${i.name} ${status}</option>`;
            });
            $('#add_session_year_id').html(o);
            $('#filter_session_year').html(o.replace(/disabled/g,""));
            initSelect2();
        });
    };
    loadSessionYears();

    const table = $('#courseTable').DataTable({
        processing:true, serverSide:true,
        ajax:{ url:"{{ route('education.course.paginate') }}", data:d=>d.session_year_id=$('#filter_session_year').val() },
        columns:[
            { data:'course_image', orderable:false, searchable:false },
            { data:'session_year' },
            { data:'course_name' },
            { data:'course_code' },
            { data:'is_active', orderable:false, searchable:false },
            { data:'action', orderable:false, searchable:false }
        ]
    });

    $('#filter_session_year').change(()=>table.ajax.reload());

    $('.course-images').imageUploader({multiple:false,imagesInputName:'course_image'});

    $('#addCourseForm').on('submit',function(e){
        e.preventDefault();
        $.ajax({
            url:"{{ route('education.course.store') }}",
            type:"POST",
            data:new FormData(this),
            processData:false, contentType:false,
            success:r=>{
                $('#addCourseModal').modal('hide');
                $('#addCourseForm')[0].reset();
                $('.course-images').empty().imageUploader({multiple:false,imagesInputName:'course_image'});
                $('#add_is_active').prop('checked',true);
                table.ajax.reload();
                Swal.fire("Success",r.message,"success");
            },
            error:err=>{
                let msg = Object.values(err.responseJSON.errors)[0];
                Swal.fire("Error",msg,"error");
            }
        });
    });

    $(document).on('click','.editCourse',function(){
        let id=$(this).data('id');
        $.get("{{ route('education.course.edit',':id') }}".replace(':id',id),d=>{
            $('#edit_course_id').val(d.id);
            $('#edit_name').val(d.course_name);
            $('#edit_code').val(d.course_code);
            $('#edit_description').val(d.description);
            $('#edit_is_active').prop('checked',d.is_active==1);
            $('.course-images-edit').empty().imageUploader({
                multiple:false,
                imagesInputName:'course_image',
                preloaded:d.course_image_url?[{id:1,src:d.course_image_url}]:[]
            });
            $('#editCourseModal').modal('show');
        });
    });

    $('#editCourseForm').on('submit',function(e){
        e.preventDefault();
        let id=$('#edit_course_id').val();
        $.ajax({
            url:"{{ route('education.course.update',':id') }}".replace(':id',id),
            type:"POST",
            data:new FormData(this),
            processData:false, contentType:false,
            success:r=>{
                $('#editCourseModal').modal('hide');
                table.ajax.reload();
                Swal.fire("Success",r.message,"success");
            },
            error:err=>{
                let msg = Object.values(err.responseJSON.errors)[0];
                Swal.fire("Error",msg,"error");
            }
        });
    });

    $(document).on('click','.deleteCourse',function(){
        let id=$(this).data('id');
        Swal.fire({title:"Delete?",icon:"warning",showCancelButton:true})
        .then(r=>{ if(r.isConfirmed){
            $.ajax({ url:"{{ route('education.course.delete',':id') }}".replace(':id',id), type:"DELETE", data:{_token:"{{ csrf_token() }}"},
                success:()=>{ table.ajax.reload(); Swal.fire("Deleted","","success"); }
            });
        }});
    });

});
</script>
@endsection