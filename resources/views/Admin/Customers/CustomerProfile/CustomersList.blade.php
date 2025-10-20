@extends('Admin.layout.app')

@section('title', 'Home | customers')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="customers"
        :links="['Home' => 'Admin.Dashboard', 'customers' => '']" />

    <div class=" mt-4">
        <div class="card p-3">
        <table id="customers-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Type</th>
                    <th>customer UID</th>
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

    $('#customers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("customers.paginate") }}',
        columns: [
            { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
            { data: 'type', name: 'type', orderable: true, searchable: false },
            { data: 'customer_uid', name: 'customer_uid' },
            { data: 'full_name', name: 'full_name' },
            { data: 'primary_email', name: 'primary_email' },
            { data: 'primary_phone', name: 'primary_phone' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },

        ]
    });
    

</script>
@endsection