@extends('Admin.layout.app')

@section('title', 'Home | Manage Session')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Manage Session" :links="['Home' => 'Admin.Dashboard', 'Manage Session' => '']" />

    <div class="">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSessionModal">+ Add Session Year</button>
        </div>

        <div class="card p-3">
            <table class="table table-bordered" id="sessionTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Session Name</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Active</th>
                    <th width="120px">Actions</th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addSessionForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Session Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <x-inputbox id="add_name" type="text" placeholder="Enter Session Name" label="Session Name" name="name" :required="true" helpertxt="e.g., 2025-2026" />

                    <div class="mb-3">
                        <label for="add_start_date" class="form-label">Start Date</label>
                        <input type="text" id="add_start_date" name="start_date" class="form-control flatpickr" placeholder="Select Start Date">
                        <small class="helpertxt">Session start date</small>
                    </div>

                    <div class="mb-3">
                        <label for="add_end_date" class="form-label">End Date</label>
                        <input type="text" id="add_end_date" name="end_date" class="form-control flatpickr" placeholder="Select End Date">
                        <small class="helpertxt">Session end date</small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="add_is_active" name="is_active" value="1" checked class="form-check-input">
                        <label for="add_is_active" class="form-check-label" >Mark as Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">
                        <span class="btn-text">Add</span>
                        <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editSessionForm">@csrf
            <input type="hidden" id="edit_session_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Session Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <x-inputbox id="edit_name" type="text" placeholder="Enter Session Name" label="Session Name" name="name" :required="true" helpertxt="e.g., 2025-2026" />

                    <div class="mb-3">
                        <label for="edit_start_date" class="form-label">Start Date</label>
                        <input type="text" id="edit_start_date" name="start_date" class="form-control flatpickr" placeholder="Select Start Date">
                        <small class="helpertxt">Session start date</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_end_date" class="form-label">End Date</label>
                        <input type="text" id="edit_end_date" name="end_date" class="form-control flatpickr" placeholder="Select End Date">
                        <small class="helpertxt">Session end date</small>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="form-check-input">
                        <label for="edit_is_active" class="form-check-label" >Mark as Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <span class="btn-text">Update</span>
                        <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        let addStartPicker = null, addEndPicker = null;
        let editStartPicker = null, editEndPicker = null;

        function initAddFlatpickr() {
            addStartPicker = $("#add_start_date").flatpickr({
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true
            });
            addEndPicker = $("#add_end_date").flatpickr({
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true
            });
        }

        function initEditFlatpickr() {
            editStartPicker = $("#edit_start_date").flatpickr({
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true
            });
            editEndPicker = $("#edit_end_date").flatpickr({
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true
            });
        }

        initAddFlatpickr();
        initEditFlatpickr();

        const table = $('#sessionTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('education.sessionyear.sessionPaginate') }}",
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'is_active', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        $('#addSessionForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#addSubmitBtn');
            const $text = $btn.find('.btn-text');
            const $spinner = $btn.find('.spinner-border');

            $btn.prop('disabled', true);
            $text.text('Adding...');
            $spinner.removeClass('d-none');

            $.ajax({
                url: "{{ route('education.sessionyear.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    $('#addSessionModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Success', res.message || 'Session added successfully!', 'success');
                },
                error: function(xhr) {
                    let msg = 'Something went wrong!';
                    if (xhr.status === 422) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $text.text('Add');
                    $spinner.addClass('d-none');
                }
            });
        });

        $(document).on('click', '.editSessionYear', function() {
            const id = $(this).data('id');

            $.get("{{ url('education/session-years') }}/" + id + "/edit")
                .done(function(data) {
                    $('#edit_session_id').val(data.id);
                    $('#edit_name').val(data.name);
                    if (data.start_date && editStartPicker) editStartPicker.setDate(data.start_date, true);
                    if (data.end_date && editEndPicker) editEndPicker.setDate(data.end_date, true);
                    $('#edit_is_active').prop('checked', data.is_active == 1);
                    $('#editSessionModal').modal('show');
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to load session data.', 'error');
                });
        });

        $('#editSessionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_session_id').val();
            const $btn = $('#editSubmitBtn');
            const $text = $btn.find('.btn-text');
            const $spinner = $btn.find('.spinner-border');

            $btn.prop('disabled', true);
            $text.text('Updating...');
            $spinner.removeClass('d-none');

            const url = "{{ route('education.sessionyear.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    $('#editSessionModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Success', res.message || 'Session updated successfully!', 'success');
                },
                error: function(xhr) {
                    let msg = 'Something went wrong!';
                    if (xhr.status === 422) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $text.text('Update');
                    $spinner.addClass('d-none');
                }
            });
        });

        $(document).on('click', '.deleteSessionYear', function() {
            const id = $(this).data('id');
            const url = "{{ route('education.sessionyear.delete', ':id') }}".replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "This session year will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function() {
                            table.ajax.reload();
                            Swal.fire('Deleted!', 'Session year has been deleted.', 'success');
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete session.', 'error');
                        }
                    });
                }
            });
        });

        $('#addSessionModal').on('hidden.bs.modal', function() {
            $('#addSessionForm')[0].reset();
            if (addStartPicker) addStartPicker.clear();
            if (addEndPicker) addEndPicker.clear();
            $('#add_is_active').prop('checked', false);
        });

        $('#editSessionModal').on('hidden.bs.modal', function() {
            $('#editSessionForm')[0].reset();
            $('#edit_session_id').val('');
            if (editStartPicker) editStartPicker.clear();
            if (editEndPicker) editEndPicker.clear();
            $('#edit_is_active').prop('checked', false);
        });
    });
</script>
@endsection