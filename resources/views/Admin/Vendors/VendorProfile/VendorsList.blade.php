@extends('Admin.layout.app')

@section('title', 'Home | Vendors')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Vendors"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => '']" />

    <div class=" mt-4">
        <div class="card p-3">
        <table id="vendors-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Type</th>
                    <th>Vendor UID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>

    $('#vendors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("vendors.paginate") }}',
        columns: [
            { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
            { data: 'type', name: 'type', orderable: true, searchable: false },
            { data: 'vendor_uid', name: 'vendor_uid' },
            { data: 'full_name', name: 'full_name' },
            { data: 'primary_email', name: 'primary_email' },
            { data: 'primary_phone', name: 'primary_phone' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },

        ]
    });
    

</script>
@endsection