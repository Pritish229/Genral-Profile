@extends('Admin.layout.app')

@section('title', 'Assign College Course to Student')

@section('content')
<div class="page-content">

    <x-breadcrumb
        title="Assign College + Course + Add Student"
        :links="['Home' => 'Admin.Dashboard', 'Assign Student' => '']" />

    <div class="p-1">

        {{-- FILTERS --}}
        <div class="card p-3 mb-3">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">University *</label>
                    <select id="filter_university" class="form-select">
                        <option value="">-- Select University --</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">College *</label>
                    <select id="filter_college" class="form-select">
                        <option value="">-- Select College --</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Course *</label>
                    <select id="filter_course" class="form-select">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Session Year *</label>
                    <select id="filter_session_year" class="form-select">
                        <option value="">-- Select Session Year --</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Semester / Class *</label>
                    <select id="filter_sem_class" class="form-select">
                        <option value="">-- Select Semester/Class --</option>
                    </select>
                </div>

                <div class="col-md-3 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        + Add Student
                    </button>
                </div>

            </div>
        </div>

        {{-- TABLE --}}
        <div class="card p-3">
            <table class="table table-bordered" id="studentTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>University</th>
                        <th>College</th>
                        <th>Course</th>
                        <th>Session</th>
                        <th>Semester/Class</th>
                        <th>Student Name</th>
                        <th>Mobile</th>
                        <th>Active</th>
                        <th width="140">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

</div>

{{-- ADD STUDENT MODAL --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addStudentForm">@csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Student Inputs --}}
                    <div class="row g-3">

                        <div class="col-md-4">
                            <x-inputbox
                                id="add_student_name"
                                type="text"
                                name="student_name"
                                label="Student Name"
                                placeholder="Enter Student Name"
                                required="true"
                            />
                        </div>

                        <div class="col-md-4">
                            <x-inputbox
                                id="add_mobile"
                                type="text"
                                name="mobile"
                                label="Mobile Number"
                                placeholder="Enter Mobile Number"
                                required="true"
                            />
                        </div>

                        <div class="col-md-4">
                            <x-inputbox
                                id="add_email"
                                type="email"
                                name="email"
                                label="Email"
                                placeholder="Enter Email"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-inputbox
                                id="add_father_name"
                                type="text"
                                name="father_name"
                                label="Father Name"
                                placeholder="Enter Father Name"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-inputbox
                                id="add_mother_name"
                                type="text"
                                name="mother_name"
                                label="Mother Name"
                                placeholder="Enter Mother Name"
                            />
                        </div>

                        <div class="col-12">
                            <x-switch-toggle
                                id="add_is_active"
                                name="is_active"
                                :checked="true"
                                label="Active"
                            />
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Student</button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
$(function() {

    // Enable Select2
    const initSelect2 = () => {
        $('select').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    width: "100%",
                    dropdownParent:
                        $(this).closest('.modal').length
                        ? $(this).closest('.modal')
                        : $('body')
                });
            }
        });
    };

    initSelect2();

    // Dummy Data
    const loadUniversities = t => {
        $(t).html(`
            <option value="">-- Select --</option>
            <option value="1">University A</option>
            <option value="2">University B</option>
        `).trigger('change');
    };

    const loadColleges = t => {
        $(t).html(`
            <option value="">-- Select --</option>
            <option value="1">College of Engineering</option>
            <option value="2">College of Science</option>
        `).trigger('change');
    };

    const loadCourses = t => {
        $(t).html(`
            <option value="">-- Select --</option>
            <option value="1">B.Tech</option>
            <option value="2">B.Tech - CS</option>
            <option value="3">MCA</option>
        `).trigger('change');
    };

    const loadSessions = t => {
        $(t).html(`
            <option value="">-- Select --</option>
            <option value="2024">2024-2025</option>
            <option value="2025">2025-2026</option>
        `).trigger('change');
    };

    const loadSemClasses = t => {
        $(t).html(`
            <option value="">-- Select --</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Class A</option>
            <option value="4">Class B</option>
        `).trigger('change');
    };

    loadUniversities('#filter_university');
    loadColleges('#filter_college');
    loadCourses('#filter_course');
    loadSessions('#filter_session_year');
    loadSemClasses('#filter_sem_class');

});
</script>
@endsection
