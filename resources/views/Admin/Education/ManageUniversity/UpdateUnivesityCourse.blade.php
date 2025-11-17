@extends('Admin.layout.app')

@section('title', 'Assigned Courses')

@section('content')
<div class="page-content">

    <x-breadcrumb
        title="Assigned Courses"
        :links="[
            'Home' => 'Admin.Dashboard',
            'University Master' => 'education.university.index',
            'University Courses' => ['education.universitycourse.index',['id' => $university->id]],
            'Assigned Courses' => ''
        ]" />

    <div class="card p-4 mb-3">
        <h4 class="fw-bold mb-1">University: {{ $university->org_name }}</h4>
        <p class="text-muted small mb-0">
            {{ $university->city }}, {{ $university->state }} |
            {{ $university->email_id }} |
            {{ $university->phone_no }}
        </p>
    </div>

    <div class="card p-4">

        <div class="d-flex justify-content-between mb-3">
            <h5 class="fw-bold mb-0">Assigned Course Structure</h5>

            <input id="searchCourse" type="text"
                class="form-control form-control-sm"
                placeholder="Search..." style="width:250px;">
        </div>

        <div id="courseTree" class="row g-4"></div>

    </div>

</div>
@endsection


@section('style')
<style>
    .tree-column {
        width: 33.33%;
    }

    .tree-item {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .tree-children {
        margin-left: 15px;
        padding-left: 10px;
        border-left: 1px solid #ccc;
    }

    .tree-child {
        font-weight: 700;
        margin: 4px 0;
    }

    /* Chip styles */
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
</style>
@endsection


@section('script')
<script>
$(function() {

    const listUrl = "{{ route('education.universitycourse.assignedList', $university->id) }}";
    const updateUrl = "{{ route('education.universitycourse.update') }}";
    const deleteUrlTemplate = "{{ route('education.universitycourse.delete', ['university' => $university->id, 'id' => ':id']) }}";
    const activeUrl = "{{ route('education.universitycourse.updateActive') }}";

    let updatePayload = {};

    loadTree();

    function loadTree() {
        $("#courseTree").html("Loading...");

        $.get(listUrl, function(res) {
            updatePayload = {};
            renderTree(res.assigned);
            bindEvents();
        });
    }

    function renderTree(items) {
        let html = "";

        items.forEach((item, index) => {
            if (index % 3 === 0) html += '<div class="w-100"></div>';
            html += `<div class="col-md-4 tree-column">${renderNode(item)}</div>`;
        });

        html += `
        <div class="d-flex justify-content-end mt-4 w-100">
            <button class="btn btn-success px-4" id="saveUpdatesBtn">
                Save Changes
            </button>
        </div>`;

        $("#courseTree").html(html);
    }

    function renderNode(node) {
        const hasChildren = node.children && node.children.length > 0;
        const showInputs = node.is_parent === "false";

        // 🔥 Disable children if parent inactive
        const disableChildren = !node.is_active;

        let h = `
        <div class="tree-item d-flex justify-content-between align-items-start">
            <div>
                ${hasChildren ? `<span class="course-chip-parent">Parent</span>` : ""}
                ${node.course_name}

                ${showInputs ? `
                    <div class="course-meta d-flex gap-2 mt-1">
                        <input type="number" class="form-control form-control-sm update-duration"
                            data-id="${node.course_id}" value="${node.duration ?? ''}"
                            min="1" placeholder="Year Duration">

                        <input type="number" class="form-control form-control-sm update-semester"
                            data-id="${node.course_id}" value="${node.semesters ?? ''}"
                            min="0" placeholder="Sem">
                    </div>
                ` : ""}
            </div>

            <div class="d-flex gap-2 pt-3">
                <div class="form-check form-switch">
                    <input class="form-check-input switch-active" type="checkbox"
                        data-id="${node.course_id}"
                        data-is-parent="${hasChildren ? 1 : 0}"
                        ${node.is_active ? "checked" : ""}>
                </div>

                <a href="javascript:;">
                    <i class="fa fa-trash delete-icon text-danger"
                       data-id="${node.course_id}"
                       data-has-children="${hasChildren ? 1 : 0}">
                    </i>
                </a>
            </div>
        </div>
        `;

        if (hasChildren) {
            h += `<div class="tree-children">`;

            node.children.forEach(child => {
                h += `
                <div class="tree-child d-flex justify-content-between">
                    <div>
                        <span class="course-chip-child">Child</span>
                        ${child.course_name}

                        <div class="course-meta d-flex gap-2 mt-1">
                            <input type="number" class="form-control form-control-sm update-duration"
                                data-id="${child.course_id}" value="${child.duration ?? ''}"
                                min="1" placeholder="Yr">

                            <input type="number" class="form-control form-control-sm update-semester"
                                data-id="${child.course_id}" value="${child.semesters ?? ''}"
                                min="0" placeholder="Sem">
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input switch-active" type="checkbox"
                                data-id="${child.course_id}"
                                ${child.is_active ? "checked" : ""}
                                ${disableChildren ? "disabled" : ""}>
                        </div>

                        <i class="fa fa-trash delete-icon text-danger"
                           data-id="${child.course_id}"
                           data-has-children="0"></i>
                    </div>
                </div>`;
            });

            h += `</div>`;
        }

        return h;
    }

    function bindEvents() {

        // 1. Store duration & semester updates
        $(".update-duration, .update-semester").on("input", function() {
            let id = $(this).data("id");
            updatePayload[id] = updatePayload[id] || {};
            updatePayload[id].duration = $(`.update-duration[data-id='${id}']`).val();
            updatePayload[id].semester = $(`.update-semester[data-id='${id}']`).val();
        });

        // 2. Active Switch Update
        $(".switch-active").on("change", function() {
            let id = $(this).data("id");
            let active = $(this).is(":checked") ? 1 : 0;
            let isParent = $(this).data("is-parent");

            // ⚠ Ask confirmation before deactivating parent
            if (isParent && active === 0) {
                Swal.fire({
                    title: "Deactivate Parent?",
                    text: "All child courses will also be deactivated.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, proceed"
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateActiveStatus(id, active);
                    } else {
                        $(this).prop("checked", true);
                    }
                });
            } else {
                updateActiveStatus(id, active);
            }
        });

        function updateActiveStatus(id, active) {
            $.post(activeUrl, {
                id: id,
                university_id: "{{ $university->id }}",
                is_active: active,
                _token: "{{ csrf_token() }}"
            }, function() {
                loadTree();
            }).fail(function(err) {
                Swal.fire("Error", "Unable to update the status.", "error");
            });
        }

        // 3. Delete parent/child
        $(".delete-icon").on("click", function() {
            let id = $(this).data("id");
            let hasChildren = $(this).data("has-children") == 1;

            let msg = hasChildren ?
                "This parent has child courses. All will be deleted. Continue?" :
                "Delete this course?";

            Swal.fire({
                title: "Are you sure?",
                text: msg,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Delete"
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteUrl = deleteUrlTemplate.replace(':id', id);

                    $.ajax({
                        url: deleteUrl,
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function() {
                            Swal.fire("Deleted!", "Record removed", "success");
                            loadTree();
                        },
                        error: function(err) {
                            Swal.fire("Error", "Unable to delete the course.", "error");
                        }
                    });
                }
            });
        });

        // 4. Save updates
        $("#saveUpdatesBtn").on("click", function() {
            if (Object.keys(updatePayload).length === 0) {
                Swal.fire("Nothing to Update", "", "info");
                return;
            }

            $.post(updateUrl, {
                _token: "{{ csrf_token() }}",
                university_id: "{{ $university->id }}",
                updates: updatePayload,
            }, function() {
                Swal.fire("Saved", "Course details updated", "success");
                loadTree();
            }).fail(function(err) {
                Swal.fire("Error", err.responseJSON.message, "error");
            });

        });

    }

    // 5. Search Filter
    $("#searchCourse").on("input", function() {
        const q = $(this).val().toLowerCase();

        $("#courseTree .tree-column").each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(q));
        });
    });

});
</script>


@endsection