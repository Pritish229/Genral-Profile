@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Dashboard"
        :links="['Home' => 'Admin.Dashboard', 'Dashboard' => '']" />

    <div class="mt-4">
       <h2>Wellcome to Dashboard</h2>
    </div>
</div>
@endsection


@section('script')
<script>
    
</script>
@endsection