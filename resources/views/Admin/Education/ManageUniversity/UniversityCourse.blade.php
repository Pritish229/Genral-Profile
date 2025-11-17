@extends('Admin.layout.app')

@section('title', 'University Courses')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="University Courses"
        :links="[
            'Home' => 'Admin.Dashboard',
            'University Master' => 'education.university.index',
            'University Courses' => ''
        ]" />

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-start">

            <div>
                <h4 class="fw-bold mb-1">University: {{ $university->org_name }}</h4>
                <p class="text-muted small mb-0">
                    {{ $university->city }}, {{ $university->state }} |
                    {{ $university->email_id }} |
                    {{ $university->phone_no }}
                </p>
                <a href="{{ route('education.universitycourse.view', $university) }}"
                    class="mt-2 d-inline-block text-decoration-none fw-bold">
                    View Assigned Courses
                </a>
            </div>



        </div>
    </div>

    <div class="card p-4 mt-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Manage University Courses</h5>

            <div class="d-flex gap-2 align-items-center">
                <button class="btn btn-secondary btn-sm" id="selectAllBtn" type="button">Select All</button>
                <button class="btn btn-warning btn-sm" id="unselectAllBtn" type="button">Unselect All</button>

                <input type="text" id="searchCourse" class="form-control form-control-sm"
                    placeholder="Search Course..." style="width: 220px;">
            </div>
        </div>

        <div id="validationErrors" class="alert alert-danger py-2 px-3 small mt-3 d-none"></div>

        <form id="assignCoursesForm">
            @csrf
            <input type="hidden" name="university_id" value="{{ $university->id }}">

            <div id="courseList" class="row mt-3"></div>

            <div class="sticky-save text-end mt-4">
                <button type="submit" id="saveChangesBtn" class="btn btn-primary px-4">
                    Save Changes
                </button>
            </div>
        </form>

    </div>
    <div id="screenLoader">
        <div class="screen-loader-spinner"></div>
    </div>
</div>
@endsection

@section('script')
<style>
    .sticky-save {
        position: sticky;
        bottom: 0;
        padding: 12px 0;
        z-index: 5;
    }

    .parent-block {
        position: relative;
    }

    .child-list {
        margin-left: 20px;
        border-left: 1px solid #e0e0e0;
        padding-left: 15px;
    }

    .child-item {
        margin-bottom: 12px;
    }

    .course-chip-parent,
    .course-chip-child {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .course-chip-parent {
        background: #f3f4ff;
        color: #3b49df;
    }

    .course-chip-child {
        background: #e3f5ff;
        color: #0d6efd;
    }

    #screenLoader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(2px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .screen-loader-spinner {
        width: 45px;
        height: 45px;
        border: 4px solid #0d6efd;
        border-top: 4px solid transparent;
        border-radius: 50%;
        animation: spinLoader 0.8s linear infinite;
    }

    @keyframes spinLoader {
        100% {
            transform: rotate(360deg);
        }
    }

    /* ---------- BUTTON LOADER ---------- */
    .saving-loader {
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        display: inline-block;
        animation: spin 0.7s linear infinite;
        vertical-align: middle;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    $(document).ready(function() {

        let assignedMap = {};
        const universityId = "{{ $university->id }}";

        loadCourses();

        // -------------------------------
        // LOAD COURSES
        // -------------------------------
        function loadCourses() {
            $.when(
                $.get("{{ route('education.course.listAll') }}"),
                $.get("{{ route('education.university.show', $university->id) }}")
            ).done(function(courseRes, univRes) {

                let all = courseRes[0].data || [];
                let assigned = univRes[0].university_courses || [];

                assignedMap = {};
                assigned.forEach(x => {
                    assignedMap[x.course_id] = {
                        duration: x.duration_in_years,
                        semesters: x.total_semesters,
                        is_active: x.is_active
                    };
                });

                let parents = all.filter(x => x.is_parent === "true");
                let children = all.filter(x => x.parent_id !== null);
                let independent = all.filter(x => x.is_parent === "false" && x.parent_id === null);

                let html = "";
                parents.forEach(p => html += renderParent(p, children));
                independent.forEach(c => html += renderIndependent(c));

                $("#courseList").html(html);

                Object.keys(assignedMap).forEach(cid => {
                    $("#course_" + cid).prop("checked", true);
                    toggleMetaInputs($(`.single-course-block[data-id='${cid}']`), true);
                });

                attachEvents();
                updateSaveButton();
            });
        }

        // -------------------------------
        // RENDER PARENT BLOCK
        // -------------------------------
        function renderParent(parent, children) {
            let checked = assignedMap[parent.id] ? "checked" : "";

            let html = `
        <div class="col-md-4 mb-4 single-course-block" data-id="${parent.id}">
            <div class="course-card parent-block">

                <div class="form-check mb-2">
                    <input class="form-check-input course-checkbox" type="checkbox"
                        id="course_${parent.id}" value="${parent.id}" name="course_ids[]"
                        data-type="parent" ${checked}>

                    <!-- REQUIRED -->
                    <input type="hidden" name="is_parent[${parent.id}]" value="1">

                    <label class="form-check-label fw-semibold" for="course_${parent.id}">
                        ${parent.course_name}
                        <span class="course-chip-parent">Parent</span>
                    </label>
                </div>

                <div class="child-list">
        `;

            children.filter(c => c.parent_id == parent.id).forEach(ch => {
                let cChecked = assignedMap[ch.id] ? "checked" : "";
                let meta = assignedMap[ch.id] ?? {};
                let enabled = cChecked ? "" : "disabled";

                html += `
                <div class="child-item single-course-block" data-id="${ch.id}">
                    <div class="form-check">
                        <input class="form-check-input course-checkbox" type="checkbox"
                            id="course_${ch.id}" value="${ch.id}" name="course_ids[]"
                            data-type="child" ${cChecked}>

                        <!-- REQUIRED -->
                        <input type="hidden" name="is_parent[${ch.id}]" value="0">

                        <label class="form-check-label" for="course_${ch.id}">
                            <span class="course-chip-child">Child</span> ${ch.course_name}
                        </label>
                    </div>

                    <div class="d-flex gap-2 mt-2">

                        <input type="number" class="form-control form-control-sm duration-field"
                            placeholder="Years" name="duration[${ch.id}]"
                            value="${meta.duration ?? ''}" min="1" ${enabled}>

                        <input type="number" class="form-control form-control-sm semester-field"
                            placeholder="Semesters" name="semesters[${ch.id}]"
                            value="${meta.semesters ?? ''}" min="1" ${enabled}>

                        <div class="form-check form-switch">
                            <input class="form-check-input is-active-switch" type="checkbox"
                                name="is_active[${ch.id}]" value="1"
                                ${meta.is_active ? "checked" : ""} ${enabled}>
                        </div>

                    </div>
                </div>
            `;
            });

            return html + "</div></div></div>";
        }

        // -------------------------------
        // RENDER INDEPENDENT COURSE
        // -------------------------------
        function renderIndependent(course) {
            let checked = assignedMap[course.id] ? "checked" : "";
            let meta = assignedMap[course.id] ?? {};
            let enabled = checked ? "" : "disabled";

            return `
        <div class="col-md-4 mb-4 single-course-block" data-id="${course.id}">
            <div class="course-card">

                <div class="form-check">
                    <input class="form-check-input course-checkbox" type="checkbox"
                        id="course_${course.id}" value="${course.id}"
                        name="course_ids[]" data-type="single" ${checked}>

                    <!-- REQUIRED -->
                    <input type="hidden" name="is_parent[${course.id}]" value="0">

                    <label class="form-check-label" for="course_${course.id}">
                        ${course.course_name}
                    </label>
                </div>

                <div class="d-flex gap-2 mt-2">

                    <input type="number" class="form-control form-control-sm duration-field"
                        placeholder="Years" name="duration[${course.id}]"
                        value="${meta.duration ?? ''}" min="1" ${enabled}>

                    <input type="number" class="form-control form-control-sm semester-field"
                        placeholder="Semesters" name="semesters[${course.id}]"
                        value="${meta.semesters ?? ''}" min="1" ${enabled}>

                    <div class="form-check form-switch">
                        <input class="form-check-input is-active-switch" type="checkbox"
                            name="is_active[${course.id}]" value="1"
                            ${meta.is_active ? "checked" : ""} ${enabled}>
                    </div>

                </div>

            </div>
        </div>`;
        }

        // -------------------------------
        // ENABLE / DISABLE FIELDS
        // -------------------------------
        function toggleMetaInputs(block, enable) {

            block.find(".duration-field").prop("disabled", !enable);
            block.find(".semester-field").prop("disabled", !enable);

            let activeSwitch = block.find(".is-active-switch");

            if (enable) {
                activeSwitch.prop("disabled", false);
                activeSwitch.prop("checked", true); // 👈 ALWAYS turn ON when enabled
            } else {
                block.find(".duration-field, .semester-field").val("");
                activeSwitch.prop("disabled", true);
                activeSwitch.prop("checked", false); // 👈 ALWAYS turn OFF when disabled
            }
        }

        // -------------------------------
        // ATTACH EVENTS
        // -------------------------------
        function attachEvents() {

            $("#courseList").on("change", ".course-checkbox", function() {

                let cb = $(this);
                let id = cb.val();
                let type = cb.data("type");
                let checked = cb.is(":checked");
                let block = $(`.single-course-block[data-id='${id}']`);

                if (type === "parent") {
                    block.find(".child-item .course-checkbox").each(function() {
                        $(this).prop("checked", checked).trigger("change");
                    });
                }

                if (type === "child") {
                    let parentBlock = cb.closest(".parent-block");
                    toggleMetaInputs(block, checked);

                    if (checked) {
                        parentBlock.find("> .form-check .course-checkbox").prop("checked", true);
                    } else {
                        if (parentBlock.find(".child-item .course-checkbox:checked").length == 0) {
                            parentBlock.find("> .form-check .course-checkbox").prop("checked", false);
                        }
                    }
                }

                if (type === "single") {
                    toggleMetaInputs(block, checked);
                }

                updateSaveButton();
            });

            $("#courseList").on("input", ".duration-field, .semester-field", updateSaveButton);
            $("#courseList").on("change", ".is-active-switch", updateSaveButton);

            $("#selectAllBtn").click(function() {
                $(".course-checkbox:visible").prop("checked", true).trigger("change");
            });

            $("#unselectAllBtn").click(function() {
                $(".course-checkbox:visible").prop("checked", false).trigger("change");
            });

            $("#searchCourse").keyup(function() {
                let q = $(this).val().toLowerCase().trim();
                $(".single-course-block").each(function() {
                    let cb = $(this).find(".course-checkbox").first();
                    let label = $(`label[for='${cb.attr("id")}']`).clone();
                    label.find(".course-chip-parent, .course-chip-child").remove();
                    $(this).toggle(label.text().toLowerCase().includes(q));
                });
            });

            $("#assignCoursesForm").submit(function(e) {
                e.preventDefault();
                if ($("#saveChangesBtn").prop("disabled")) return;

                $.ajax({
                    url: "{{ route('education.universitycourse.store', $university->id) }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(r) {
                        Swal.fire("Success", r.message, "success")
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message ?? "Operation failed", "error");
                    }
                });
            });
        }

        // -------------------------------
        // VALIDATION (NO SEMESTER REQUIRED)
        // -------------------------------
        function updateSaveButton() {

            let errors = [];

            $(".course-checkbox:checked").each(function() {

                let cb = $(this);
                let id = cb.val();
                let block = $(`.single-course-block[data-id='${id}']`);
                let dur = block.find(".duration-field").val();

                let label = $(`label[for='${cb.attr("id")}']`).clone();
                label.find(".course-chip-child, .course-chip-parent").remove();
                let name = label.text().trim();

                if (cb.data("type") === "parent") return;

                if (!dur || dur <= 0) errors.push(`${name}: Duration required`);

                // Semester validation removed
            });

            let box = $("#validationErrors");

            if ($(".course-checkbox:checked").length === 0) {
                box.removeClass("d-none alert-danger").addClass("alert-info")
                    .html("<div>• Select at least one course.</div>");
                $("#saveChangesBtn").prop("disabled", true);
                return;
            }

            if (errors.length > 0) {
                box.removeClass("d-none alert-info").addClass("alert-danger")
                    .html(errors.map(e => `<div>• ${e}</div>`).join(""));
                $("#saveChangesBtn").prop("disabled", true);
                return;
            }

            box.addClass("d-none").empty();
            $("#saveChangesBtn").prop("disabled", false);
        }

    });
</script>

@endsection