@extends('Admin.layout.app')

@section('title', 'Home | Customers')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Students"
        :links="['Home' => 'Admin.Dashboard', 'Customers' => '']" />

    <div class="mt-4">
        <table id="students-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Customer UID</th>
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
@endsection


@section('script')
<script>
    $('#students-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("students.Studentlist.all") }}',
    columns: [
        { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
        { data: 'student_uid', name: 'student_uid' },
        { data: 'full_name', name: 'full_name' },
        { data: 'primary_email', name: 'primary_email' },
        { data: 'primary_phone', name: 'primary_phone' },
        { data: 'status', name: 'status', orderable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ]
});
</script>
@endsection