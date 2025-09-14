@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Medias"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Students' => 'students.Studentlist',
            'Student Detail' => ['students.Studentlist.studentDetailsPage', $id],
            'Manage Media' => ''
        ]" />

    <div class="mt-4">
       <h2>Wellcome to Medias</h2>
    </div>
</div>
@endsection


@section('script')
<script>
    
</script>
@endsection