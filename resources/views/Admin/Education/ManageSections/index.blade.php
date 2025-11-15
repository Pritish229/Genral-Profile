{{-- resources/views/Admin/Education/ManageSections/index.blade.php --}}
@extends('Admin.layout.app')

@section('title', 'Section Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Section Master" :links="['Home' => 'Admin.Dashboard', 'Section Master' => '']" />

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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                    + Add Section
                </button>
            </div>
        </div>

        {{-- DATATABLE --}}
        <div class="card p-3">
            <table class="table table-bordered" id="sectionTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Session</th>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Section Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addSectionForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Class Section</h5>
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
                            <x-inputbox id="add_section_name" name="section_name" type="text"
                                label="Section Name" placeholder="Enter section name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="add_section_code" name="section_code" type="text"
                                label="Section Code" placeholder="Enter unique code" :required="true" />
                        </div>
                        <div class="col-12">
                            <x-switch-toggle id="add_is_active" name="is_active" :checked="true" label="Active" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Add Section</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editSectionForm">@csrf
            <input type="hidden" id="edit_section_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Class Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-inputbox id="edit_section_name" name="section_name" type="text"
                                label="Section Name" placeholder="Enter section name" :required="true" />
                        </div>
                        <div class="col-md-6">
                            <x-inputbox id="edit_section_code" name="section_code" type="text"
                                label="Section Code" placeholder="Enter section code" :required="true" />
                        </div>
                        <div class="col-12">
                            <x-switch-toggle id="edit_is_active" name="is_active" :checked="false" label="Active" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Update Section</button>
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
        const selects = $('#filter_session_year, #filter_course_id, #filter_class_id, #add_session_year_id, #add_course_id, #add_class_id');
        selects.each(function () {
            const $el = $(this);
            if (!$el.data('select2')) {
                $el.select2({
                    width: '100%',
                    dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $('body')  
                });
            }
        });
    };

    // ---------------------------
    // Dropdown Loaders
    // ---------------------------
    const loadSessionYears = (target, activeOnly = false) => {
        const $target = $(target);
        const url = activeOnly ? "{{ route('education.sessionyear.active') }}" : "{{ route('education.sessionyear.list') }}";
        $target.prop('disabled', true).html('<option value="">Loading...</option>');
        $.get(url)
            .done(res => {
                const data = res.data || res.active || [];
                let html = `<option value="">-- Select Session Year --</option>`;
                data.forEach(i => {
                    html += `<option value="${i.id}">${i.name}${i.is_active ? ' (Active)' : ' (Inactive)'}</option>`;
                });
                $target.html(html).prop('disabled', false);
                initSelect2();
            })
            .fail(() => $target.html('<option>Failed to Load</option>'));
    };

    const loadCourses = (sessionId, target, activeOnly = false) => {
        const $target = $(target);
        if (!sessionId) {
            $target.html('<option value="">-- Select Course --</option>').prop('disabled', true);
            return;
        }
        $.get("{{ route('education.course.SessionWise') }}", { session_year_id: sessionId })
            .done(res => {
                const data = activeOnly ? (res.active || []) : (res.data || []);
                let html = `<option value="">-- Select Course --</option>`;
                data.forEach(c => html += `<option value="${c.id}">${c.course_name}</option>`);
                $target.html(html).prop('disabled', false);
                initSelect2();
            });
    };

    const loadClasses = (sessionId, courseId, target, activeOnly = false) => {
        const $target = $(target);
        if (!sessionId || !courseId) {
            $target.html('<option value="">-- Select Class --</option>').prop('disabled', true);
            return;
        }
        $.get("{{ route('education.class.CourseWise') }}", { session_year_id: sessionId, course_id: courseId })
            .done(res => {
                const data = activeOnly ? (res.active || []) : (res.data || []);
                let html = `<option value="">-- Select Class --</option>`;
                data.forEach(c => html += `<option value="${c.id}">${c.class_name}</option>`);
                $target.html(html).prop('disabled', false);
                initSelect2();
            });
    };

    // ---------------------------
    // Initialize DataTable
    // ---------------------------
    function initTable() {
        return $('#sectionTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('education.section.paginate') }}",
                data: function (d) {
                    // safely fetch filter values
                    const dt = $('#sectionTable').DataTable();
                    const order = dt.order();
                    if (order.length) {
                        const columnIndex = order[0][0];
                        const columnDef = dt.settings()[0].aoColumns[columnIndex];
                        d.sort_column = columnDef.data;
                        d.sort_dir = order[0][1];
                    }
                    d.session_year_id = $('#filter_session_year').val() || '';
                    d.course_id = $('#filter_course_id').val() || '';
                    d.course_class_id = $('#filter_class_id').val() || '';
                }
            },
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'session_year', name: 'session_year' },
                { data: 'course_name', name: 'course_name' },
                { data: 'class_name', name: 'class_name' },
                { data: 'section_name', name: 'section_name' },
                { data: 'section_code', name: 'section_code' },
                { data: 'is_active', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }

    let table = initTable();

    // ---------------------------
    // Filters
    // ---------------------------
    $('#filter_session_year').on('change', function () {
        loadCourses(this.value, '#filter_course_id', false);
        $('#filter_class_id').html('<option value="">-- Select Class --</option>').prop('disabled', true);
        table.ajax.reload(null, true);
    });

    $('#filter_course_id').on('change', function () {
        loadClasses($('#filter_session_year').val(), this.value, '#filter_class_id', false);
        table.ajax.reload(null, true);
    });

    $('#filter_class_id').on('change', function () {
        table.ajax.reload(null, true);
    });

    // ---------------------------
    // Reset Filters Button
    // ---------------------------
    $('<button class="btn btn-outline-secondary ms-2" id="resetFilters">Reset</button>')
        .appendTo('.text-md-end')
        .on('click', function () {
            $('#filter_session_year').val('').trigger('change');
            $('#filter_course_id').html('<option value="">-- Select Course --</option>').prop('disabled', true);
            $('#filter_class_id').html('<option value="">-- Select Class --</option>').prop('disabled', true);
            initSelect2();
            table.ajax.reload(null, true);
        });

    // ---------------------------
    // Add Modal (Active Only)
    // ---------------------------
    $('#add_session_year_id').on('change', function () {
        loadCourses(this.value, '#add_course_id', true);
        $('#add_class_id').prop('disabled', true).html('<option>-- Select Class --</option>');
    });

    $('#add_course_id').on('change', function () {
        loadClasses($('#add_session_year_id').val(), this.value, '#add_class_id', true);
    });

    // ---------------------------
    // Edit Section
    // ---------------------------
    $(document).on('click', '.editSection', function () {
        const id = $(this).data('id');
        $.get("{{ route('education.section.edit', ':id') }}".replace(':id', id))
            .done(res => {
                const d = res.data;
                $('#edit_section_id').val(d.id);
                $('#edit_section_name').val(d.section_name);
                $('#edit_section_code').val(d.section_code);
                $('#edit_is_active').prop('checked', d.is_active);
                $('#editSectionModal').modal('show');
            })
            .fail(() => Swal.fire('Error', 'Failed to fetch section details.', 'error'));
    });

    // ---------------------------
    // Update Section
    // ---------------------------
    $('#editSectionForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_section_id').val();
        const formData = $(this).serialize();

        $.post("{{ route('education.section.update', ':id') }}".replace(':id', id), formData)
            .done(res => {
                $('#editSectionModal').modal('hide');
                Swal.fire('Updated', res.message, 'success');
                table.ajax.reload(null, false);
            })
            .fail(xhr => {
                const msg = xhr.status === 422
                    ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                    : (xhr.responseJSON?.message || 'Something went wrong.');
                Swal.fire('Error', msg, 'error');
            });
    });

    // ---------------------------
    // Initialize everything
    // ---------------------------
    loadSessionYears('#filter_session_year', false);
    loadSessionYears('#add_session_year_id', true);
    initSelect2();

});
</script>
@endsection
