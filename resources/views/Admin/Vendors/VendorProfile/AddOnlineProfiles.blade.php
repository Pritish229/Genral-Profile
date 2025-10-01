@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Add Online Profiles')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Add Online Profiles"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List', 'Online Profiles' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-3" id="vendor-details">
                Loading details...
            </div>
        </div>
    </div>
    <div class="progress mb-3" style="height: 25px; display:none;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width:0%;" id="progressBar">0%</div>
    </div>

    <div class="card mt-3 p-3">
        <form id="onlineProfilesForm">
            <input type="hidden" name="vendor_id" value="{{ $id }}">

            <table class="table table-bordered" id="profilesTable">
                <thead>
                    <tr>
                        <th>Social Platform</th>
                        <th>Icon URL</th>
                        <th>Profile URL</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="profile-row">
                        <td>
                            <input type="text" name="social_platform[]" class="form-control" placeholder="Platform (e.g., LinkedIn)" required>
                        </td>
                        <td>
                            <input type="text" name="icon[]" class="form-control" placeholder="Icon URL or name">
                        </td>
                        <td>
                            <input type="url" name="profile_url[]" class="form-control" placeholder="https://..." required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm addRow">+</button>
                        </td>
                    </tr>
                </tbody>
            </table>



            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save Profiles</button>
                <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    function updateProgress(percent) {
        $("#progressContainer").show(); // ensure visible
        $("#progressBar").css("width", percent + "%").text(percent + "%");
    }

    $(document).ready(function() {
        let baseUrl = "{{ url('/vendors') }}";
        let vendor_id = "{{ $id }}";

        // Initial progress = 90%
        updateProgress(90);

        function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${vendor_id}/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    
                    let imgSrc = `storage/${response.data.avatar_url}`;
                    let manageBankUrl = `/vendors/${response.data.id}/manageBank`;
                    let manageDocUrl = `/vendors/${response.data.id}/manageDocument`;
                    let managemediaUrl = `/vendors/${response.data.id}/Media/manage`;

                    $("#vendor-details").html(`
                <div class="d-flex align-items-start justify-content-between">
                    <!-- Profile + Info -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 160px;">
                            <img src="{{asset('${imgSrc}')}}" class="img-thumbnail w-100" alt="Profile picture">
                        </div>
                        <div class="flex-grow-1">
                                <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.vendor_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Gender:</strong> ${response.data.gender}</p>
                                <p><strong>Occupation:</strong> ${response.data.occupation}</p>
                                <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status badge on top-right -->
                    <div>
                        <span class="badge ${response.primary_details.status === 'active' ? 'bg-success' : 'bg-danger'}">
                            ${response.primary_details.status}
                        </span>
                    </div>
                </div>

                <!-- Footer with Documents & Media -->
                <div class="d-flex justify-content-end gap-3 mt-3 border-top pt-2">
                    <a href="${manageBankUrl}" class="text-decoration-none">
                        <i class="fas fa-university me-1"></i> Bank Details
                    </a>
                    <a href="${manageDocUrl}" class="text-decoration-none">
                        <i class="fas fa-file-alt me-1"></i> Documents
                    </a>
                    <a href="${managemediaUrl}" class="text-decoration-none">
                        <i class="fas fa-photo-video me-1"></i> Medias
                    </a>
                </div>
            `);

                } else {
                    $("#vendor-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#vendor-details").html(`<p class="text-danger">Something went wrong.</p>`);
                console.error(xhr.responseText);
            }
        });
    }

        fetchDetails();

        // form submit
        $("#onlineProfilesForm").on("submit", function(e) {
            e.preventDefault();

            let valid = true;
            $("#profilesTable tbody tr").each(function() {
                let platform = $(this).find("input[name='social_platform[]']").val();
                let url = $(this).find("input[name='profile_url[]']").val();
                if (!platform || !url) valid = false;
            });

            if (!valid) {
                Swal.fire("Validation Error", "Social Platform and Profile URL are required.", "error");
                return;
            }

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('vendors.onlineProfiles.store', ['id' => $id]) }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // animate to 100%
                        updateProgress(100);
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: response.message || 'All Steps are Complete Successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('vendors.create') }}";
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error", "Something went wrong.", "error");
                }
            });
        });


        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'All Steps are Complete Successfully.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "{{ route('vendors.create') }}";
            });
        });
    });
    // Add new row
$(document).on("click", ".addRow", function() {
    let newRow = `
        <tr class="profile-row">
            <td>
                <input type="text" name="social_platform[]" class="form-control"
                       placeholder="Platform (e.g., LinkedIn)" required>
            </td>
            <td>
                <input type="text" name="icon[]" class="form-control"
                       placeholder="Icon URL or name">
            </td>
            <td>
                <input type="url" name="profile_url[]" class="form-control"
                       placeholder="https://..." required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
            </td>
        </tr>
    `;
    $("#profilesTable tbody").append(newRow);
});

// Remove row
$(document).on("click", ".removeRow", function() {
    $(this).closest("tr").remove();
});

</script>
@endsection