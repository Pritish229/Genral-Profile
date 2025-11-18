@extends('Admin.layout.app')

@section('title', 'Fee Master')

@section('content')
<div class="page-content">
    <x-breadcrumb title="Fee Master" :links="['Home' => 'Admin.Dashboard', 'Fee Master' => '']" />

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">+ Add Fee</button>
    </div>

    <div class="card p-3">
        <table class="table table-bordered" id="feeMasterTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fee Name</th>
                    <th>Fee Type</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="addFeeModal">
    <div class="modal-dialog">
        <form id="addFeeForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Fee</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Fee Name *</label>
                    <input type="text" name="fee_name" class="form-control" required>

                    <label class="mt-2">Fee Type *</label>
                    <select name="fee_type" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="1">Addon</option>
                        <option value="0">Deduct</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editFeeModal">
    <div class="modal-dialog">
        <form id="editFeeForm">@csrf
            <input type="hidden" id="edit_fee_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Fee</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Fee Name *</label>
                    <input type="text" name="fee_name" id="edit_fee_name" class="form-control" required>

                    <label class="mt-2">Fee Type *</label>
                    <select name="fee_type" id="edit_fee_type" class="form-select" required>
                        <option value="1">Addon</option>
                        <option value="0">Deduct</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {

    let table = $('#feeMasterTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('fee.feemaster.paginate') }}",
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'fee_name' },
            { data: 'fee_type' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    const reload = () => table.ajax.reload(null, false);

    $('#addFeeForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('fee.feemaster.store') }}", $(this).serialize())
            .done(res => {
                $('#addFeeModal').modal('hide');
                Swal.fire('Success', res.message, 'success');
                this.reset();
                reload();
            });
    });

    $(document).on('click', '.editFee', function () {
        let id = $(this).data('id');

        $.get("{{ route('fee.feemaster.edit', ':id') }}".replace(':id', id), res => {
            $('#edit_fee_id').val(res.data.id);
            $('#edit_fee_name').val(res.data.fee_name);
            $('#edit_fee_type').val(res.data.fee_type);
            $('#editFeeModal').modal('show');
        });
    });

    $('#editFeeForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_fee_id').val();

        $.post("{{ route('fee.feemaster.update', ':id') }}".replace(':id', id), $(this).serialize())
            .done(res => {
                $('#editFeeModal').modal('hide');
                Swal.fire('Updated', res.message, 'success');
                reload();
            });
    });

    $(document).on('click', '.deleteFee', function () {
        let id = $(this).data('id');

        Swal.fire({ title: "Delete?", icon: "warning", showCancelButton: true })
        .then(s => {
            if (s.isConfirmed) {
                $.ajax({
                    url: "{{ route('fee.feemaster.delete', ':id') }}".replace(':id', id),
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: res => {
                        Swal.fire('Deleted', res.message, 'success');
                        reload();
                    }
                });
            }
        });
    });

});
</script>
@endsection
