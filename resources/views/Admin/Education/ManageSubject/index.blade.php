{{-- resources/views/Admin/Education/ManageSubject/index.blade.php --}}
@extends('Admin.layout.app')

@section('title', 'Subject Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Subject Master" :links="['Home' => 'Admin.Dashboard', 'Subject Master' => '']" />

    <div class="p-1">
        {{-- FILTERS --}}
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

        {{-- DATATABLE --}}
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

{{-- ADD MODAL --}}
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
                            <x-inputbox id="add_subject_name" type="text" name="subject_name" label="Subject Name"
                                placeholder="Enter subject name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_subject_code" type="text" name="subject_code" label="Subject Code"
                                placeholder="Enter subject code" :required="true" />
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

{{-- EDIT MODAL --}}
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
                            <x-inputbox id="edit_subject_name" type="text" name="subject_name" label="Subject Name"
                                placeholder="Enter subject name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="edit_subject_code" type="text" name="subject_code" label="Subject Code"
                                placeholder="Enter subject code" :required="true" />
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
    // === Select2 Initialization ===
    const initSelect2 = () => {
        const selects = $('#filter_session_year, #filter_course_id, #filter_class_id, #add_session_year_id, #add_course_id, #add_class_id');
        selects.each(function() {
            const $el = $(this);
            if (!$el.data('select2')) {
                $el.select2({
                    width: '100%',
                    dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $('body')
                });
            }
        });
    };

    // === Load Session Years ===
    const loadSessionYears = (target, activeOnly = false) => {
        const $target = $(target);
        $target.prop('disabled', true).html('<option value="">Loading...</option>');
        const url = activeOnly
            ? "{{ route('education.sessionyear.active') }}"
            : "{{ route('education.sessionyear.list') }}";

        $.get(url)
            .done(res => {
                const data = res.data || res.active || [];
                if (!data.length) {
                    $target.html('<option value="">-- No Session Years Found --</option>');
                    return;
                }
                let html = `<option value="">-- Select Session Year --</option>`;
                data.forEach(i => {
                    html += `<option value="${i.id}">${i.name}${i.is_active ? ' (Active)' : ' (Inactive)'}</option>`;
                });
                $target.html(html).prop('disabled', false);
                if ($target.data('select2')) $target.trigger('change.select2');
                else initSelect2();
            })
            .fail(() => $target.html('<option value="">-- Failed to Load --</option>'));
    };

    // === Load Courses ===
    const loadCourses = (sessionId, target, activeOnly = false) => {
        const $target = $(target);
        if (!sessionId) {
            $target.html('<option value="">-- Select Course --</option>').prop('disabled', true);
            if ($target.data('select2')) $target.trigger('change.select2');
            return;
        }

        $.get("{{ route('education.course.SessionWise') }}", { session_year_id: sessionId })
            .done(res => {
                const data = activeOnly ? (res.active || []) : (res.data || []);
                if (!data.length) {
                    $target.html('<option value="">-- No Courses Found --</option>').prop('disabled', true);
                    return;
                }
                let html = `<option value="">-- Select Course --</option>`;
                data.forEach(c => {
                    html += `<option value="${c.id}">${c.course_name}${c.is_active ? ' (Active)' : ' (Inactive)'}</option>`;
                });
                $target.html(html).prop('disabled', false);
                if ($target.data('select2')) $target.trigger('change.select2');
                else initSelect2();
            })
            .fail(() => $target.html('<option value="">-- Failed to Load --</option>'));
    };

    // === Load Classes ===
    const loadClasses = (sessionId, courseId, target, activeOnly = false) => {
        const $target = $(target);
        if (!sessionId || !courseId) {
            $target.html('<option value="">-- Select Class --</option>').prop('disabled', true);
            if ($target.data('select2')) $target.trigger('change.select2');
            return;
        }

        $.get("{{ route('education.class.CourseWise') }}", {
            session_year_id: sessionId,
            course_id: courseId
        })
            .done(res => {
                const data = activeOnly ? (res.active || []) : (res.data || []);
                if (!data.length) {
                    $target.html('<option value="">-- No Classes Found --</option>').prop('disabled', true);
                    return;
                }
                let html = `<option value="">-- Select Class --</option>`;
                data.forEach(c => {
                    html += `<option value="${c.id}">${c.class_name}${c.is_active ? ' (Active)' : ' (Inactive)'}</option>`;
                });
                $target.html(html).prop('disabled', false);
                if ($target.data('select2')) $target.trigger('change.select2');
                else initSelect2();
            })
            .fail(() => $target.html('<option value="">-- Failed to Load --</option>'));
    };

    // === Initial Load ===
    loadSessionYears('#filter_session_year', false);
    loadSessionYears('#add_session_year_id', true);

    // === Filter Chain (All Sessions/Courses/Classes) ===
    $('#filter_session_year').on('change', function () {
        loadCourses(this.value, '#filter_course_id', false);
        $('#filter_class_id').prop('disabled', true).html('<option value="">-- Select Class --</option>');
        table.ajax.reload();
    });

    $('#filter_course_id').on('change', function () {
        loadClasses($('#filter_session_year').val(), this.value, '#filter_class_id', false);
        table.ajax.reload();
    });

    $('#filter_class_id').on('change', () => table.ajax.reload());

    // === Add Modal Chain (Active Only) ===
    $('#add_session_year_id').on('change', function () {
        loadCourses(this.value, '#add_course_id', true);
        $('#add_class_id').prop('disabled', true).html('<option value="">-- Select Class --</option>');
    });

    $('#add_course_id').on('change', function () {
        loadClasses($('#add_session_year_id').val(), this.value, '#add_class_id', true);
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

    // === Add Subject ===
    $('#addSubjectForm').on('submit', function (e) {
        e.preventDefault();
        $.post("{{ route('education.subject.store') }}", $(this).serialize())
            .done(res => {
                $('#addSubjectModal').modal('hide');
                this.reset();
                $('#add_course_id, #add_class_id').prop('disabled', true);
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

    initSelect2();
});
</script>
@endsection
