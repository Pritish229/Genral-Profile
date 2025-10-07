@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
<x-breadcrumb
        title="Business Address"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List', 'Vendor Detail' => ['vendors.viewDetails', $id], 'Bank Details' => '']" />

    <div class="mt-4">
        <h2>Wellcome to Business Address</h2>
    </div>
</div>
@endsection


@section('script')
<script>

</script>
@endsection