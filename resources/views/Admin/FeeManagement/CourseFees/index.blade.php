@extends('Admin.layout.app')

@section('title', 'Course Fee Master')

@section('content')

<div class="page-content">

    <x-breadcrumb title="Course Fee Master" :links="['Home' => 'Admin.Dashboard', 'Course Fee' => '']" />

    <div class="row g-3 align-items-end mb-3 p-2">

        <div class="col-md-3">
            <label class="form-label">Session Year</label>
            <select id="filter_session_year" class="form-select select2">
                <option value="">-- Select Session Year --</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Course</label>
            <select id="filter_course_id" class="form-select select2" disabled>
                <option value="">-- Select Course --</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Class</label>
            <select id="filter_class_id" class="form-select select2" disabled>
                <option value="">-- Select Class --</option>
            </select>
        </div>

        <div class="col-md-3 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseFeeModal">
                + Add Course Fee
            </button>
        </div>
    </div>

    <div class="card p-3">
        <table class="table table-bordered" id="courseFeeTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Session</th>
                    <th>Course</th>
                    <th>Class</th>
                    <th>Fee Name</th>
                    <th>Fee Type</th>
                    <th>Fee Amt</th>
                    <th>Total Fee</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

<div class="modal fade" id="addCourseFeeModal">
    <div class="modal-dialog modal-lg">
        <form id="addCourseFeeForm">@csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add Course Fee</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label>Session Year *</label>
                            <select id="add_session_year_id" name="session_year_id"
                                class="form-select select2" required></select>
                        </div>

                        <div class="col-md-4">
                            <label>Course *</label>
                            <select id="add_course_id" name="course_id"
                                class="form-select select2" disabled required></select>
                        </div>

                        <div class="col-md-4">
                            <label>Class *</label>
                            <select id="add_class_id" name="course_class_id"
                                class="form-select select2" disabled required></select>
                        </div>

                        <div class="col-md-4">
                            <label>Fee Master *</label>
                            <select id="add_fee_master_id" name="fee_master_id"
                                class="form-select select2" required></select>
                        </div>



                        <div class="col-md-4">
                            <label>Fee Amount *</label>
                            <input type="number" id="add_fee_amount" name="fee_amount"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>Fee Type *</label>
                            <select id="add_feetype" name="feestype" class="form-select">
                                <option value="1">Annual</option>
                                <option value="2">Monthly</option>
                                <option value="3">Other</option>
                            </select>
                        </div>


                        <div class="col-md-4">
                            <label>Status *</label>
                            <select id="add_status" name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editCourseFeeModal">
    <div class="modal-dialog modal-lg">
        <form id="editCourseFeeForm">@csrf
            <input type="hidden" id="edit_id">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Course Fee</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label>Fee Amount *</label>
                            <input type="number" id="edit_fee_amount" name="fee_amount"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>Fee Type *</label>
                            <select id="edit_feetype" name="feestype" class="form-select">
                                <option value="1">Annual</option>
                                <option value="2">Monthly</option>
                                <option value="3">Other</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Total Fee</label>
                            <input type="number" id="edit_total_fee" class="form-control" readonly>
                        </div>

                        <div class="col-md-4">
                            <label>Status *</label>
                            <select id="edit_status" name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
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

        const initSelect2 = (selector = null) => {
            const $els = selector ? $(selector) : $('.select2');
            $els.each(function() {
                const $el = $(this);
                if (!$el.data('select2')) {
                    $el.select2({
                        width: '100%',
                        dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $('body')
                    });
                }
            });
        };

        const loadSessionYears = (target, activeOnly = false) => {
            const $target = $(target);
            $target.prop('disabled', true).html('<option>Loading...</option>');

            const url = activeOnly ?
                "{{ route('education.sessionyear.active') }}" :
                "{{ route('education.sessionyear.list') }}";

            $.get(url)
                .done(res => {
                    const data = res.data || res.active || [];
                    let html = '<option value="">-- Select Session Year --</option>';
                    data.forEach(i => {
                        html += `<option value="${i.id}">${i.name}${i.is_active ? ' (Active)' : ' (Inactive)'}</option>`;
                    });
                    $target.html(html).prop('disabled', false);
                    if ($target.data('select2')) $target.trigger('change.select2');
                    else initSelect2($target);
                })
                .fail(() => {
                    $target.html('<option value="">-- Failed to load --</option>');
                });
        };

        const loadCourses = (sessionId, target, activeOnly = false) => {
            const $target = $(target);
            if (!sessionId) {
                $target.html('<option value="">-- Select Course --</option>').prop('disabled', true);
                if ($target.data('select2')) $target.trigger('change.select2');
                return;
            }

            $.get("{{ route('education.course.SessionWise') }}", {
                    session_year_id: sessionId
                })
                .done(res => {
                    const data = activeOnly ? (res.active || []) : (res.data || []);
                    let html = '<option value="">-- Select Course --</option>';
                    data.forEach(c => html += `<option value="${c.id}">${c.course_name}${c.is_active ? ' (Active)' : ''}</option>`);
                    $target.html(html).prop('disabled', false);
                    if ($target.data('select2')) $target.trigger('change.select2');
                    else initSelect2($target);
                })
                .fail(() => {
                    $target.html('<option value="">-- Failed to load --</option>');
                });
        };

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
                    let html = '<option value="">-- Select Class --</option>';
                    data.forEach(c => html += `<option value="${c.id}">${c.class_name}${c.is_active ? ' (Active)' : ''}</option>`);
                    $target.html(html).prop('disabled', false);
                    if ($target.data('select2')) $target.trigger('change.select2');
                    else initSelect2($target);
                })
                .fail(() => {
                    $target.html('<option value="">-- Failed to load --</option>');
                });
        };

        const loadFeeMaster = () => {
            const $target = $('#add_fee_master_id');
            $target.prop('disabled', true).html('<option>Loading...</option>');
            $.get("{{ route('fee.feemaster.list') }}")
                .done(res => {
                    let html = '<option value="">-- Select Fee --</option>';
                    (res.data || []).forEach(f => {
                        html += `<option value="${f.id}" data-name="${f.fee_name}">${f.fee_name}</option>`;
                    });
                    $target.html(html).prop('disabled', false);
                    if ($target.data('select2')) $target.trigger('change.select2');
                    else initSelect2($target);
                })
                .fail(() => {
                    $target.html('<option value="">-- Failed to load fees --</option>');
                });
        };

        initSelect2();

        const table = $("#courseFeeTable").DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('coursefee.paginate') }}",
                data: function(d) {
                    d.session_year_id = $("#filter_session_year").val();
                    d.course_id = $("#filter_course_id").val();
                    d.course_class_id = $("#filter_class_id").val();
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    orderable: false
                },
                {
                    data: "session"
                },
                {
                    data: "course"
                },
                {
                    data: "class"
                },
                {
                    data: "fee_name"
                },
                {
                    data: "feestype",
                    render: function(data) {
                        const types = {
                            '0': 'Annual',
                            '1': 'Monthly',
                            '2': 'Other'
                        };
                        return types[data] || data;
                    }
                },
                {
                    data: "fee_amount"
                },
                {
                    data: "total_fee"
                },
                {
                    data: "status"
                },
                {
                    data: "action",
                    orderable: false
                }
            ]
        });

        const reloadTable = () => table.ajax.reload(null, false);

        loadSessionYears('#filter_session_year', false);
        loadSessionYears('#add_session_year_id', true);
        loadFeeMaster();

        $('#filter_session_year').on('change', function() {
            loadCourses(this.value, '#filter_course_id', false);
            $('#filter_class_id').prop('disabled', true).html('<option value="">-- Select Class --</option>');
            reloadTable();
        });

        $('#filter_course_id').on('change', function() {
            loadClasses($('#filter_session_year').val(), this.value, '#filter_class_id', false);
            reloadTable();
        });

        $('#filter_class_id').on('change', () => reloadTable());

        $('#add_session_year_id').on('change', function() {
            loadCourses(this.value, '#add_course_id', true);
            $('#add_class_id').prop('disabled', true).html('<option value="">-- Select Class --</option>');
        });

        $('#add_course_id').on('change', function() {
            loadClasses($('#add_session_year_id').val(), this.value, '#add_class_id', true);
        });

        $(document).on('change', '#add_fee_master_id', function() {
            $('#add_fee_name').val($(this).find(':selected').data('name') || '');
        });

        const calculateTotal = (prefix) => {
            const amount = parseFloat($(`${prefix}_fee_amount`).val()) || 0;
            const type = $(`${prefix}_feetype`).val();
            const times = type === '2' ? 12 : 1;
            $(`${prefix}_total_fee`).val(amount * times);
        };

        $('#add_fee_amount, #add_feetype').on('input change', () => calculateTotal('#add'));
        $('#edit_fee_amount, #edit_feetype').on('input change', () => calculateTotal('#edit'));

        $('#addCourseFeeForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const feeType = $('#add_feetype').val();
            const amount = parseFloat($('#add_fee_amount').val()) || 0;
            const total = feeType === '2' ? amount * 12 : amount;
            formData.set('total_fee', total);
            formData.set('feestype', feeType);

            $.ajax({
                url: "{{ route('coursefee.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#addCourseFeeModal').modal('hide');
                    Swal.fire('Success', res.message, 'success');
                    $('#addCourseFeeForm')[0].reset();
                    loadSessionYears('#add_session_year_id', true);
                    loadFeeMaster();
                    reloadTable();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to save';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $(document).on('click', '.editCourseFee', function() {
            const id = $(this).data('id');
            $.get("{{ route('coursefee.edit', '') }}/" + id)
                .done(res => {
                    const d = res.data;
                    $('#edit_id').val(d.id);
                    $('#edit_fee_amount').val(d.fee_amount);
                    $('#edit_feetype').val(d.feestype);
                    $('#edit_status').val(d.status);
                    calculateTotal('#edit');
                    $('#editCourseFeeModal').modal('show');
                })
                .fail(() => Swal.fire('Error', 'Failed to load data', 'error'));
        });

        $('#editCourseFeeForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            const formData = new FormData(this);
            const feeType = $('#edit_feetype').val();
            const amount = parseFloat($('#edit_fee_amount').val()) || 0;
            const total = feeType === '2' ? amount * 12 : amount;
            formData.set('total_fee', total);
            formData.set('feestype', feeType);

            $.ajax({
                url: "{{ route('coursefee.update', '') }}/" + id,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#editCourseFeeModal').modal('hide');
                    Swal.fire('Updated', res.message, 'success');
                    reloadTable();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to update';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $(document).on('click', '.deleteCourseFee', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('coursefee.delete', '') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    }).done(res => {
                        Swal.fire('Deleted', res.message, 'success');
                        reloadTable();
                    }).fail(() => Swal.fire('Error', 'Failed to delete', 'error'));
                }
            });
        });

    });
</script>
@endsection