@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Basic info"
        :links="['Home' => 'Admin.Dashboard', 'Students' => '', 'Basic info' => '']" />

    <div class="mt-4">
       <h2>Wellcome to Basicinfo</h2>
    </div>
</div>
@endsection


@section('script')
<script>
    
</script>
@endsection