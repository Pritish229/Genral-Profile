@extends('Admin.layout.app')

@section('title', 'Home | Vendor | All Business')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="All Businesses"
        :links="['Home' => route('Admin.Dashboard'), 'All Businesses' => '']" />

    <div class="mt-4 container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                   

                        <table id="business-vendor-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Vendor ID</th>
                                    <th>Legal Name</th>
                                    <th>Trade Name</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')


<script>
$(document).ready(function() {
    // Check if DataTable is already initialized to avoid duplicates
    if (!$.fn.DataTable.isDataTable('#business-vendor-table')) {
        $('#business-vendor-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: `{{ route('vendors.totalBusiness') }}`,
            columns: [
                { data: 'vendor_name', name: 'vendor_name' },
                { data: 'legal_name', name: 'businessProfile.legal_name' },
                { data: 'trade_name', name: 'businessProfile.trade_name' },
                { data: 'primary_email', name: 'primary_email' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            responsive: true,
            order: [[1, 'asc']],
            
        });
    }
});
</script>
@endsection