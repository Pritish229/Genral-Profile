@extends('Admin.layout.app')

@section('title', 'Fee Master')

@section('content')

<div class="page-content">
    <x-breadcrumb title="Fee Master" :links="['Home' => 'Admin.Dashboard', 'Fee Master' => '']" />
    <div class="d-flex justify-content-end mb-3">


        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">
            + Add Fee
        </button>
    </div>

    <div class="card p-3">
        <table class="table table-bordered" id="feeMasterTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fee Name</th>
                    <th width="140">Actions</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addFeeModal">
    <div class="modal-dialog">
        <form id="addFeeForm">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Fee</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <label class="form-label">Fee Name *</label>
                    <input type="text" name="fee_name" id="add_fee_name" class="form-control" required>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
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
                    <label class="form-label">Fee Name *</label>
                    <input type="text" name="fee_name" id="edit_fee_name" class="form-control" required>
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
    $(document).ready(function() {

        const table = $('#feeMasterTable').DataTable({
            processing: true,
            serverSide: true,

            order: [], 

            ajax: {
                url: "{{ route('fee.feemaster.paginate') }}",
                data: d => {
                    const dt = $('#feeMasterTable').DataTable();
                    const order = dt.order();

                    if (order.length) {
                        d.sort_column = dt.settings()[0].aoColumns[order[0][0]].data;
                        d.sort_dir = order[0][1];
                    }
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'fee_name'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Helper
        const reloadTable = () => table.ajax.reload(null, false);

        // --------------------------
        // Add Fee
        // --------------------------
        $('#addFeeForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('fee.feemaster.store') }}", $(this).serialize())
                .done(res => {
                    $('#addFeeModal').modal('hide');
                    Swal.fire('Success', res.message, 'success');
                    this.reset();
                    reloadTable();
                })
                .fail(() => Swal.fire('Error', 'Failed to create fee', 'error'));
        });

        // --------------------------
        // Edit Fee
        // --------------------------
        $(document).on('click', '.editFee', function() {
            const id = $(this).data('id');

            $.get("{{ route('fee.feemaster.edit', '') }}/" + id, res => {
                $('#edit_fee_id').val(res.data.id);
                $('#edit_fee_name').val(res.data.fee_name);
                $('#editFeeModal').modal('show');
            });
        });

        // --------------------------
        // Update
        // --------------------------
        $('#editFeeForm').submit(function(e) {
            e.preventDefault();
            let id = $('#edit_fee_id').val();

            $.post("{{ route('fee.feemaster.update', '') }}/" + id, $(this).serialize())
                .done(res => {
                    $('#editFeeModal').modal('hide');
                    Swal.fire('Updated', res.message, 'success');
                    reloadTable();
                })
                .fail(() => Swal.fire('Error', 'Update failed', 'error'));
        });

        // --------------------------
        // Delete
        // --------------------------
        $(document).on('click', '.deleteFee', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: "Delete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Delete"
            }).then(result => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('fee.feemaster.delete', '') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: res => {
                            Swal.fire('Deleted', res.message, 'success');
                            reloadTable();
                        }
                    });

                }
            });
        });

    });
</script>
@endsection