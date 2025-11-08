@extends('Admin.layout.app')

@section('title', 'Chapter Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Chapter Master" :links="['Home' => 'Admin.Dashboard', 'Chapter Master' => '']" />

    <div class="p-1">
    <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChapterModal">+ Add Chapter</button>
        </div>
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
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select id="filter_subject_id" class="form-select" disabled>
                    <option value="">-- Select Subject --</option>
                </select>
            </div>
        </div>

        

        <div class="card p-3">
            <table class="table table-bordered" id="chapterTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Session</th>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Chapter</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addChapterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addChapterForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Chapter</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Session Year *</label>
                            <select id="add_session_year_id" name="session_year_id" class="form-select" required>
                                <option value="">-- Select Session Year --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course *</label>
                            <select id="add_course_id" name="course_id" class="form-select" required disabled>
                                <option value="">-- Select Course --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class *</label>
                            <select id="add_class_id" name="course_class_id" class="form-select" required disabled>
                                <option value="">-- Select Class --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject *</label>
                            <select id="add_subject_id" name="subject_id" class="form-select" required disabled>
                                <option value="">-- Select Subject --</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <x-inputbox id="add_chapter_name" placeholder="Enter Chapter Name" type="text" name="chapter_name" label="Chapter Name" :required="true" value="" helpertxt="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Add</button></div>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editChapterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editChapterForm">@csrf
            <input type="hidden" id="edit_chapter_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Chapter</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <x-inputbox id="edit_chapter_name" placeholder="Enter Chapter Name" type="text" name="chapter_name" label="Chapter Name" :required="true" value="" helpertxt="" />
                        </div>
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

        const initSelect2 = () => {
            const selects = $('#filter_session_year, #filter_course_id, #filter_class_id, #filter_subject_id, ' +
                '#add_session_year_id, #add_course_id, #add_class_id, #add_subject_id');

            selects.each(function() {
                let $el = $(this);
                if (!$el.data('select2')) {
                    $el.select2({
                        width: "100%",
                        dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $('body')
                    });
                }
            });
        };


        const loadSessionYears = (target, disableInactive = false) => {
            $.get("{{ route('education.sessionyear.list') }}", res => {
                let html = `<option value="">-- Select Session Year --</option>`;
                res.data.forEach(i => {
                    let status = i.is_active ? '(Active)' : '(Inactive)';
                    let disabled = disableInactive && !i.is_active ? ' disabled' : '';
                    html += `<option value="${i.id}"${disabled}>${i.name} ${status}</option>`;
                });
                $(target).html(html);
                initSelect2();
            });
        };


        const loadCourses = (sessionId, target, disableInactive = false) => {
            if (!sessionId) {
                $(target).html('<option value="">-- Select Course --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $.get("{{ route('education.course.SessionWise') }}", {
                session_year_id: sessionId
            }, res => {
                let html = `<option value="">-- Select Course --</option>`;
                res.data.forEach(c => {
                    let status = c.is_active ? '(Active)' : '(Inactive)';
                    let disabled = disableInactive && !c.is_active ? ' disabled' : '';
                    html += `<option value="${c.id}"${disabled}>${c.course_name} ${status}</option>`;
                });
                $(target).html(html).prop('disabled', false).trigger('change');
            });
        };


        const loadClasses = (sessionId, courseId, target, disableInactive = false) => {
            if (!sessionId || !courseId) {
                $(target).html('<option value="">-- Select Class --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $.get("{{ route('education.class.CourseWise') }}", {
                session_year_id: sessionId,
                course_id: courseId
            }, res => {
                let html = `<option value="">-- Select Class --</option>`;
                res.data.forEach(c => {
                    let status = c.is_active ? '(Active)' : '(Inactive)';
                    let disabled = disableInactive && !c.is_active ? ' disabled' : '';
                    html += `<option value="${c.id}"${disabled}>${c.class_name} ${status}</option>`;
                });
                $(target).html(html).prop('disabled', false).trigger('change');
            });
        };


        const loadSubjects = (classId, target, disableInactive = false) => {
            if (!classId) {
                $(target).html('<option value="">-- Select Subject --</option>').prop('disabled', true).trigger('change');
                return;
            }

            $.get("{{ route('education.subject.ClassWise') }}", {
                course_class_id: classId
            }, res => {
                let html = `<option value="">-- Select Subject --</option>`;
                res.data.forEach(s => {
                    let status = s.is_active ? '(Active)' : '(Inactive)';
                    let disabled = disableInactive && !s.is_active ? ' disabled' : '';
                    html += `<option value="${s.id}"${disabled}>${s.subject_name} ${status}</option>`;
                });
                $(target).html(html).prop('disabled', false).trigger('change');
            });
        };


        loadSessionYears('#filter_session_year', false);
        loadSessionYears('#add_session_year_id', true);


        $('#filter_session_year').on('change', function() {
            loadCourses(this.value, '#filter_course_id', false);
            $('#filter_class_id, #filter_subject_id').prop('disabled', true).html('<option value="">-- Select --</option>');
            table.ajax.reload();
        });

        $('#filter_course_id').on('change', function() {
            loadClasses($('#filter_session_year').val(), this.value, '#filter_class_id', false);
            $('#filter_subject_id').prop('disabled', true).html('<option value="">-- Select --</option>');
            table.ajax.reload();
        });

        $('#filter_class_id').on('change', function() {
            loadSubjects(this.value, '#filter_subject_id', false);
            table.ajax.reload();
        });

        $('#filter_subject_id').on('change', () => table.ajax.reload());


        $('#add_session_year_id').on('change', function() {
            loadCourses(this.value, '#add_course_id', true);
            $('#add_class_id, #add_subject_id').prop('disabled', true).html('<option value="">-- Select --</option>');
        });

        $('#add_course_id').on('change', function() {
            loadClasses($('#add_session_year_id').val(), this.value, '#add_class_id', true);
            $('#add_subject_id').prop('disabled', true).html('<option value="">-- Select --</option>');
        });

        $('#add_class_id').on('change', function() {
            loadSubjects(this.value, '#add_subject_id', true);
        });


        const table = $('#chapterTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('education.chapter.paginate') }}",
                data: function(d) {
                    d.session_year_id = $('#filter_session_year').val();
                    d.course_id = $('#filter_course_id').val();
                    d.course_class_id = $('#filter_class_id').val();
                    d.subject_id = $('#filter_subject_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'session_year'
                },
                {
                    data: 'course_name'
                },
                {
                    data: 'class_name'
                },
                {
                    data: 'subject_name'
                },
                {
                    data: 'chapter_name'
                },
                {
                    data: 'action',
                    searchable: false,
                    orderable: false
                }
            ]
        });


        $('#addChapterForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('education.chapter.store') }}", $(this).serialize())
                .done(res => {
                    $('#add_session_year_id').val('').trigger('change');
                    $('#addChapterModal').modal('hide');
                    this.reset();
                    table.ajax.reload();
                    Swal.fire('Success', res.message, 'success');
                });
        });


        $(document).on('click', '.editChapter', function() {
            let id = $(this).data('id');

            $.get("{{ route('education.chapter.edit', ':id') }}".replace(':id', id), d => {
                $('#edit_chapter_id').val(d.id);
                $('#edit_chapter_name').val(d.chapter_name);

                $('#edit_session_year_text').val(d.sessionYear.name);
                $('#edit_course_text').val(d.course.course_name);
                $('#edit_class_text').val(d.class.class_name);
                $('#edit_subject_text').val(d.subject.subject_name);

                $('#editChapterModal').modal('show');
            });
        });


        $('#editChapterForm').submit(function(e) {
            e.preventDefault();
            let id = $('#edit_chapter_id').val();

            $.post("{{ route('education.chapter.update', ':id') }}".replace(':id', id), $(this).serialize())
                .done(res => {
                    $('#editChapterModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Updated', res.message, 'success');
                });
        });


        $(document).on('click', '.deleteChapter', function() {
            let id = $(this).data('id');

            Swal.fire({
                    title: "Delete chapter?",
                    icon: "warning",
                    showCancelButton: true
                })
                .then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "{{ route('education.chapter.delete', ':id') }}".replace(':id', id),
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: () => {
                                table.ajax.reload();
                                Swal.fire("Deleted", "", "success");
                            }
                        });
                    }
                });
        });


        initSelect2();
    });
</script>

@endsection