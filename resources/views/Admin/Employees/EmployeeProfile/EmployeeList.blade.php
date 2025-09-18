@extends('Admin.layout.app')

@section('title', 'Home | employees')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Employees"
        :links="['Home' => 'Admin.Dashboard', 'Employees' => '']" />

    <div class="mt-4">
        <table id="employees-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Employee UID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Hire Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection


@section('script')
<script>
    $('#employees-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("employees.Employeeslist.paginate") }}',
    columns: [
        { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
        { data: 'employee_uid', name: 'employee_uid' },
        { data: 'full_name', name: 'full_name' },
        { data: 'primary_email', name: 'primary_email' },
        { data: 'primary_phone', name: 'primary_phone' },
        { data: 'status', name: 'status', orderable: false },
        { data: 'hire_date', name: 'hire_date' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ]
});
</script>
@endsection