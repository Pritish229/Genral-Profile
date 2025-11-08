@extends('Admin.layout.app')

@section('title', 'Home | Class Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Class Master" :links="['Home' => 'Admin.Dashboard', 'Class Master' => '']" />

    <div class="p-1">
        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-4">
                <label class="form-label">Session Year</label>
                <select id="filter_session_year" class="form-select">
                    <option value="">-- Select Session Year --</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Course</label>
                <select id="filter_course_id" class="form-select" disabled>
                    <option value="">-- Select Course --</option>
                </select>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">+ Add Class</button>
            </div>
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="classTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Session</th>
                    <th>Course</th>
                    <th>Class Name</th>
                    <th>Code</th>
                    <th>Active</th>
                    <th width="140">Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addClassForm">@csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Class</h5></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Session Year</label>
                            <select id="add_session_year_id" name="session_year_id" class="form-select" required>
                                <option value="">-- Select Session Year --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <select id="add_course_id" name="course_id" class="form-select" required disabled>
                                <option value="">-- Select Course --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_name" type="text" name="class_name" label="Class Name" placeholder="Enter class name" :required="true" value="" helpertxt="" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_code" type="text" name="class_code" label="Class Code" placeholder="Unique code" :required="true" value="" helpertxt="" />
                        </div>
                        <div class="col-md-12">
                            <x-textareabox id="add_description" name="description" label="Description" placeholder="Optional details" value="" helpertxt="" />
                        </div>
                        <div class="col-12">
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

<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editClassForm">@csrf
            <input type="hidden" id="edit_class_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Class</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-inputbox id="edit_name" type="text" name="class_name" label="Class Name" placeholder="Enter class name" :required="true" value="" helpertxt="" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="edit_code" type="text" name="class_code" label="Class Code" placeholder="Unique code" :required="true" value="" helpertxt="" />
                        </div>
                        <div class="col-md-12">
                            <x-textareabox id="edit_description" name="description" label="Description" placeholder="Optional details" value="" helpertxt="" />
                        </div>
                        <div class="col-12">
                            <x-switch-toggle id="edit_is_active" name="is_active" :checked="old('is_active') ?? false" label="Active" />
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
$(function () {
    const initSelect2 = () => {
        const selects = $('#filter_session_year, #filter_course_id, #add_session_year_id, #add_course_id, #edit_session_year_id, #edit_course_id');
        selects.each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    width: '100%',
                    dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $('body')
                });
            }
        });
    };

    const loadSessionYears = (target, disableInactive = false) => {
        $.get("{{ route('education.sessionyear.list') }}", res => {
            const html = `<option value="">-- Select Session Year --</option>` +
                res.data.map(i => {
                    const status = i.is_active == 1 ? ' (Active)' : ' (Inactive)';
                    const disabled = disableInactive && i.is_active == 0 ? ' disabled' : '';
                    return `<option value="${i.id}"${disabled}>${i.name}${status}</option>`;
                }).join('');
            $(target).html(html);
            initSelect2();
        });
    };

    const loadCourses = (sessionId, target, disableInactive = false) => {
        if (!sessionId) {
            $(target).prop('disabled', true).html('<option value="">-- Select Course --</option>');
            if ($(target).data('select2')) $(target).trigger('change.select2');
            return;
        }
        $.get("{{ route('education.course.SessionWise') }}", { session_year_id: sessionId }, res => {
            console.log(res);
            
            const html = `<option value="">-- Select Course --</option>` +
                res.data.map(c => {
                    const status = c.is_active == 1 ? ' (Active)' : ' (Inactive)';
                    const disabled = disableInactive && c.is_active == 0 ? ' disabled' : '';
                    return `<option value="${c.id}"${disabled}>${c.course_name}${status}</option>`;
                }).join('');
            $(target).html(html).prop('disabled', false);
            if ($(target).data('select2')) $(target).trigger('change.select2');
        });
    };

    // Load session years (no disable for filter, disable inactive for add/edit)
    loadSessionYears('#filter_session_year', false);
    loadSessionYears('#add_session_year_id', true);
    loadSessionYears('#edit_session_year_id', true);

    // Filter: no disable
    $(document).on('change', '#filter_session_year', function () {
        loadCourses(this.value, '#filter_course_id', false);
        $('#classTable').DataTable().ajax.reload();
    });
    $(document).on('change', '#filter_course_id', () => $('#classTable').DataTable().ajax.reload());

    // Add modal: disable inactive
    $(document).on('change', '#add_session_year_id', function () {
        loadCourses(this.value, '#add_course_id', true);
    });

    // Edit modal: disable inactive
    $(document).on('change', '#edit_session_year_id', function () {
        loadCourses(this.value, '#edit_course_id', true);
    });

    const table = $('#classTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('education.class.paginate') }}",
            data: d => {
                d.session_year_id = $('#filter_session_year').val();
                d.course_id = $('#filter_course_id').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'session_year' },
            { data: 'course_name' },
            { data: 'class_name' },
            { data: 'class_code' },
            { data: 'is_active', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#addClassForm').on('submit', function (e) {
        e.preventDefault();
        $.post("{{ route('education.class.store') }}", $(this).serialize())
            .done(res => {
                $('#addClassModal').modal('hide');
                $('#addClassForm')[0].reset();
                $('#add_session_year_id').val('').trigger('change');
                $('#add_course_id').prop('disabled', true).html('<option value="">-- Select Course --</option>');
                if ($('#add_course_id').data('select2')) $('#add_course_id').trigger('change.select2');
                $('#add_is_active').prop('checked', true);
                table.ajax.reload();
                Swal.fire('Success', res.message, 'success');
            })
            .fail(xhr => {
                const msg = xhr.status === 422
                    ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                    : (xhr.responseJSON?.message || 'Error');
                Swal.fire('Error', msg, 'error');
            });
    });

    $(document).on('click', '.editClass', function () {
        const id = $(this).data('id');
        $.get("{{ route('education.class.edit', ':id') }}".replace(':id', id), d => {
            $('#edit_class_id').val(d.id);
            $('#edit_name').val(d.class_name);
            $('#edit_code').val(d.class_code);
            $('#edit_description').val(d.description);
            $('#edit_is_active').prop('checked', d.is_active == 1);
            $('#edit_session_year_id').val(d.session_year_id);
            if ($('#edit_session_year_id').data('select2')) $('#edit_session_year_id').trigger('change.select2');

            // Load courses with inactive disabled
            loadCourses(d.session_year_id, '#edit_course_id', true);
            setTimeout(() => {
                $('#edit_course_id').val(d.course_id).trigger('change.select2');
            }, 100);

            $('#editClassModal').modal('show');
        });
    });

    $('#editClassForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_class_id').val();
        $.post("{{ route('education.class.update', ':id') }}".replace(':id', id), $(this).serialize())
            .done(res => {
                $('#add_session_year_id').val('').trigger('change');
                $('#editClassModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', res.message, 'success');
            })
            .fail(xhr => {
                const msg = xhr.status === 422
                    ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                    : (xhr.responseJSON?.message || 'Error');
                Swal.fire('Error', msg, 'error');
            });
    });

    $(document).on('click', '.deleteClass', function () {
        const id = $(this).data('id');
        Swal.fire({ title: 'Delete?', icon: 'warning', showCancelButton: true })
            .then(r => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: "{{ route('education.class.delete', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: () => { table.ajax.reload(); Swal.fire('Deleted', '', 'success'); }
                    });
                }
            });
    });
});
</script>
@endsection