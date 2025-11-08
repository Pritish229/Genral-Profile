{{-- resources/views/Admin/Education/ManageSubject/index.blade.php --}}
@extends('Admin.layout.app')

@section('title', 'Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Subject Master" :links="['Home' => 'Admin.Dashboard', 'Subject Master' => '']" />

    <div class="p-1">
        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label class="form-label">Session Year</label>
                <select id="filter_session_year" class="form-select">
                    <option value="">-- Select Session Year --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Course</label>
                <select id="filter_course_id" class="form-select" disabled>
                    <option value="">-- Select Course --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select id="filter_class_id" class="form-select" disabled>
                    <option value="">-- Select Class --</option>
                </select>
            </div>
            <div class="col-md-3 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    + Add Subject
                </button>
            </div>
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="subjectTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Session</th>
                    <th>Course</th>
                    <th>Class</th>
                    <th>Subject Name</th>
                    <th>Code</th>
                    <th>Active</th>
                    <th width="140">Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addSubjectForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Session Year *</label>
                            <select id="add_session_year_id" name="session_year_id" class="form-select" required>
                                <option value="">-- Select Session Year --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Course *</label>
                            <select id="add_course_id" name="course_id" class="form-select" required disabled>
                                <option value="">-- Select Course --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Class *</label>
                            <select id="add_class_id" name="course_class_id" class="form-select" required disabled>
                                <option value="">-- Select Class --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_subject_name" type="text" name="subject_name" label="Subject Name" placeholder="Enter subject name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_subject_code" type="text" name="subject_code" label="Subject Code" placeholder="Enter code (optional)" />
                        </div>
                        <div class="col-12">
                            <x-switch-toggle id="add_is_active" name="is_active" :checked="true" label="Active" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editSubjectForm">@csrf
            <input type="hidden" id="edit_subject_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                       
                        <div class="col-md-6">
                            <x-inputbox id="edit_subject_name" type="text" name="subject_name" label="Subject Name" placeholder="Enter subject name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="edit_subject_code" type="text" name="subject_code" label="Subject Code" placeholder="Enter code (optional)" />
                        </div>
                        <div class="col-12">
                            <x-switch-toggle id="edit_is_active" name="is_active" :checked="false" label="Active" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Subject</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    // === Safe Select2 Init ===
    const initSelect2 = () => {
        const selects = $('#filter_session_year, #filter_course_id, #filter_class_id, ' +
                        '#add_session_year_id, #add_course_id, #add_class_id, ' +
                        '#edit_session_year_id, #edit_course_id, #edit_class_id');
        selects.each(function() {
            const $el = $(this);
            if ($el.length && !$el.data('select2')) {
                $el.select2({
                    width: '100%',
                    dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $('body')
                });
            }
        });
    };

    // === Load Session Years ===
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
        }).fail(() => $(target).html('<option value="">-- Error --</option>'));
    };

    // === Load Courses ===
    const loadCourses = (sessionId, target, disableInactive = false) => {
        if (!sessionId) {
            $(target).prop('disabled', true).html('<option value="">-- Select Course --</option>');
            if ($(target).data('select2')) $(target).trigger('change.select2');
            return;
        }
        $.get("{{ route('education.course.SessionWise') }}", { session_year_id: sessionId }, res => {
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

    const loadClasses = (sessionId, courseId, target, disableInactive = false) => {
        if (!sessionId || !courseId) {
            $(target).prop('disabled', true).html('<option value="">-- Select Class --</option>');
            if ($(target).data('select2')) $(target).trigger('change.select2');
            return;
        }
        $.get("{{ route('education.class.CourseWise') }}", {
            session_year_id: sessionId,
            course_id: courseId
        }, res => {
            const html = `<option value="">-- Select Class --</option>` +
                res.data.map(c => {
                    const status = c.is_active == 1 ? ' (Active)' : ' (Inactive)';
                    const disabled = disableInactive && c.is_active == 0 ? ' disabled' : '';
                    return `<option value="${c.id}"${disabled}>${c.class_name}${status}</option>`;
                }).join('');
            $(target).html(html).prop('disabled', false);
            if ($(target).data('select2')) $(target).trigger('change.select2');
        });
    };

    // === Initial Load ===
    loadSessionYears('#filter_session_year', false);
    loadSessionYears('#add_session_year_id', true);
    loadSessionYears('#edit_session_year_id', true);

    // === Filter Events ===
    $(document).on('change', '#filter_session_year', function () {
        loadCourses(this.value, '#filter_course_id', false);
        loadClasses(this.value, $('#filter_course_id').val(), '#filter_class_id', false);
        table.ajax.reload();
    });
    $(document).on('change', '#filter_course_id', function () {
        loadClasses($('#filter_session_year').val(), this.value, '#filter_class_id', false);
        table.ajax.reload();
    });
    $(document).on('change', '#filter_class_id', () => table.ajax.reload());

    // === Add Modal Events ===
    $(document).on('change', '#add_session_year_id', function () {
        loadCourses(this.value, '#add_course_id', true);
        $('#add_class_id').prop('disabled', true).html('<option value="">-- Select Class --</option>');
    });
    $(document).on('change', '#add_course_id', function () {
        loadClasses($('#add_session_year_id').val(), this.value, '#add_class_id', true);
    });

    // === Edit Modal Events ===
    $(document).on('change', '#edit_session_year_id', function () {
        loadCourses(this.value, '#edit_course_id', true);
        $('#edit_class_id').prop('disabled', true).html('<option value="">-- Select Class --</option>');
    });
    $(document).on('change', '#edit_course_id', function () {
        loadClasses($('#edit_session_year_id').val(), this.value, '#edit_class_id', true);
    });

    // === DataTable ===
    const table = $('#subjectTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('education.subject.paginate') }}",
            data: d => {
                d.session_year_id = $('#filter_session_year').val();
                d.course_id = $('#filter_course_id').val();
                d.course_class_id = $('#filter_class_id').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'session_year' },
            { data: 'course_name' },
            { data: 'class_name' },
            { data: 'subject_name' },
            { data: 'subject_code' },
            { data: 'is_active', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#addSubjectForm').on('submit', function (e) {
        e.preventDefault();
        $.post("{{ route('education.subject.store') }}", $(this).serialize())
            .done(res => {
                $('#add_session_year_id').val('').trigger('change');
                $('#addSubjectModal').modal('hide');
                $(this)[0].reset();
                $('#add_course_id, #add_class_id').prop('disabled', true).html('<option value="">-- Select --</option>');
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

    // === Edit Click ===
    $(document).on('click', '.editSubject', function () {
        const id = $(this).data('id');
        $.get("{{ route('education.subject.edit', ':id') }}".replace(':id', id), d => {
            $('#edit_subject_id').val(d.id);
            $('#edit_subject_name').val(d.subject_name);
            $('#edit_subject_code').val(d.subject_code || '');
            $('#edit_is_active').prop('checked', d.is_active == 1);

            // Session (readonly)
            const sessionName = d.sessionYear?.name || 'Unknown';
            $('#edit_session_year_id').html(`<option value="${d.session_year_id}" selected>${sessionName}</option>`);

            // Courses
            const courseHtml = `<option value="">-- Select Course --</option>` +
                d.courses.map(c => {
                    const status = c.is_active == 1 ? ' (Active)' : ' (Inactive)';
                    const disabled = c.is_active == 0 ? ' disabled' : '';
                    return `<option value="${c.id}"${disabled} ${c.id == d.course_id ? 'selected' : ''}>${c.course_name}${status}</option>`;
                }).join('');
            $('#edit_course_id').html(courseHtml).prop('disabled', false);

            // Classes
            const classHtml = `<option value="">-- Select Class --</option>` +
                d.classes.map(c => {
                    const status = c.is_active == 1 ? ' (Active)' : ' (Inactive)';
                    const disabled = c.is_active == 0 ? ' disabled' : '';
                    return `<option value="${c.id}"${disabled} ${c.id == d.course_class_id ? 'selected' : ''}>${c.class_name}${status}</option>`;
                }).join('');
            $('#edit_class_id').html(classHtml).prop('disabled', false);

            // Re-init Select2 after DOM update
            setTimeout(() => initSelect2(), 100);

            $('#editSubjectModal').modal('show');
        });
    });

    // === Update Form ===
    $('#editSubjectForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_subject_id').val();
        $.post("{{ route('education.subject.update', ':id') }}".replace(':id', id), $(this).serialize())
            .done(res => {
                
                $('#editSubjectModal').modal('hide');
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

    // === Delete ===
    $(document).on('click', '.deleteSubject', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Subject?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!'
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: "{{ route('education.subject.delete', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: () => {
                        table.ajax.reload();
                        Swal.fire('Deleted!', '', 'success');
                    },
                    error: (xhr) => {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed', 'error');
                    }
                });
            }
        });
    });

    // === Initial Select2 ===
    initSelect2();
});
</script>
@endsection