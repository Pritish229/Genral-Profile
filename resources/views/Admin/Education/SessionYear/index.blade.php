{{-- resources/views/Admin/session/manage.blade.php --}}
@extends('Admin.layout.app')

@section('title', 'Home | Session Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Session Master"
                  :links="['Home' => 'Admin.Dashboard', 'Session Master' => '']" />

    <div class="">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addSessionModal">+ Add Session Year</button>
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
                    <th width="140">Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- ==================== ADD MODAL ==================== --}}
<div class="modal fade" id="addSessionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addSessionForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Session Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <x-inputbox id="add_name" type="text" placeholder="Enter Session Name" label="Session Name" name="name"
                                :required="true" value="" helpertxt="" />

                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label>Start Date</label>
                            <input type="text" id="add_start_date"
                                   name="start_date"
                                   class="form-control flatpickr">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>End Date</label>
                            <input type="text" id="add_end_date"
                                   name="end_date"
                                   class="form-control flatpickr">
                        </div>

                        <div class="col-12 mt-3">
                            <x-switch-toggle id="add_is_active"
                                             name="is_active"
                                             :checked="true"
                                             label="Active" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ==================== EDIT MODAL ==================== --}}
<div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editSessionForm">@csrf
            <input type="hidden" id="edit_session_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Session Year</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <x-inputbox id="edit_name"
                                type="text"
                                placeholder="Enter Session Name"
                                label="Session Name"
                                name="name"
                                :required="true" value="" helpertxt="" />

                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label>Start Date</label>
                            <input type="text" id="edit_start_date"
                                   name="start_date"
                                   class="form-control flatpickr">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>End Date</label>
                            <input type="text" id="edit_end_date"
                                   name="end_date"
                                   class="form-control flatpickr">
                        </div>

                        <div class="col-12 mt-3">
                            {{-- old() is only for validation errors – defaults to false --}}
                            <x-switch-toggle id="edit_is_active"
                                             name="is_active"
                                             :checked="old('is_active') ?? false"
                                             label="Active" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
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

    const initFlatpickr = () => $(".flatpickr").flatpickr({ dateFormat: "Y-m-d" });
    initFlatpickr();

    const table = $('#sessionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('education.sessionyear.sessionPaginate') }}",
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'start_date' },
            { data: 'end_date' },
            { data: 'is_active', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#addSessionForm').on('submit', function (e) {
        e.preventDefault();

        $.post("{{ route('education.sessionyear.store') }}", $(this).serialize())
            .done(function (res) {
                $('#addSessionModal').modal('hide');

                // ---- RESET FORM ----
                $('#addSessionForm')[0].reset();           // clears inputs + unchecks checkbox
                initFlatpickr();                           // re-init datepickers
                $('#add_is_active').prop('checked', true); // default = active

                table.ajax.reload();
                Swal.fire('Success', res.message, 'success');
            })
            .fail(function (err) {
                const msg = Object.values(err.responseJSON.errors)[0];
                Swal.fire('Error', msg, 'error');
            });
    });
    $(document).on('click', '.editSessionYear', function () {
        const id = $(this).data('id');

        $.get("{{ route('education.sessionyear.edit', ':id') }}".replace(':id', id))
            .done(function (res) {
                const d = res.data;

                $('#edit_session_id').val(d.id);
                $('#edit_name').val(d.name);
                $('#edit_start_date').val(d.start_date);
                $('#edit_end_date').val(d.end_date);
                $('#edit_start_date, #edit_end_date').flatpickr({ dateFormat: "Y-m-d" });
                $('#edit_is_active').prop('checked', d.is_active == 1);

                $('#editSessionModal').modal('show');
            });
    });

    $('#editSessionForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_session_id').val();

        $.post("{{ route('education.sessionyear.update', ':id') }}".replace(':id', id), $(this).serialize())
            .done(function (res) {
                $('#editSessionModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', res.message, 'success');
            })
            .fail(function (err) {
                const msg = Object.values(err.responseJSON.errors)[0];
                Swal.fire('Error', msg, 'error');
            });
    });

    $(document).on('click', '.deleteSessionYear', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: "Delete?",
            icon: "warning",
            showCancelButton: true
        }).then(r => {
            if (r.isConfirmed) {
                $.ajax({
                    url: "{{ route('education.sessionyear.delete', ':id') }}".replace(':id', id),
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: () => {
                        table.ajax.reload();
                        Swal.fire("Deleted", "", "success");
                    }
                });
            }
        });
    });

});
</script>
@endsection