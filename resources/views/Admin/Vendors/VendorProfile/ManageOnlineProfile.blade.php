@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Manage Online Profiles')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Online Profiles"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List',
                'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
                'Manage Online Profiles' => '']" />

    {{-- ==================== ADD FORM ==================== --}}
    <div class="card p-3 mb-3">
        <form id="onlineProfileForm">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Social Platform</label>
                    <input type="text" name="social_platform" class="form-control"
                           placeholder="LinkedIn" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icon</label>
                    <input type="text" name="icon" class="form-control"
                           placeholder="fab fa-linkedin">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profile URL</label>
                    <input type="url" name="profile_url" class="form-control"
                           placeholder="https://..." required>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>

    {{-- ==================== TABLE ==================== --}}
    <div class="card p-3">
        <table class="table table-bordered" id="profilesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Platform</th>
                    <th>Icon</th>
                    <th>URL</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ==================== EDIT MODAL ==================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="onlineProfileEditForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Online Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Social Platform</label>
                            <input type="text" name="social_platform" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icon</label>
                            <input type="text" name="icon" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Profile URL</label>
                            <input type="url" name="profile_url" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    let baseUrl   = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";
    let type      = "{{ $type }}"; 
    let editId    = null;

    function getApiUrl(path) {
        return `${baseUrl}/${vendor_id}/individual/OnlineProfile${path}`;
    }

    function loadProfiles() {
        $.get(getApiUrl('/List'), function(res) {
            let rows = '';
            if (res.success && res.data.length > 0) {
                res.data.forEach((p, i) => {
                    rows += `<tr>
                        <td>${i + 1}</td>
                        <td>${p.social_platform || '-'}</td>
                        <td>${p.icon || '-'}</td>
                        <td><a href="${p.profile_url}" target="_blank">${p.profile_url}</a></td>
                        <td>
                            <button 
                                class="btn btn-sm btn-primary edit-btn"
                                data-id="${p.id}"
                                data-platform="${p.social_platform || ''}"
                                data-icon="${p.icon || ''}"
                                data-url="${p.profile_url || ''}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProfile(${p.id})">Delete</button>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="5" class="text-center">No profiles found</td></tr>`;
            }
            $('#profilesTable tbody').html(rows);
        }).fail(function() {
            $('#profilesTable tbody').html(`<tr><td colspan="5" class="text-center">Error loading</td></tr>`);
        });
    }

    // Use delegated event listener instead of inline onclick
    $(document).on('click', '.edit-btn', function() {
        let p = {
            id: $(this).data('id'),
            social_platform: $(this).data('platform'),
            icon: $(this).data('icon'),
            profile_url: $(this).data('url')
        };
        openEditModal(p);
    });

    function openEditModal(p) {
        editId = p.id;
        $('#onlineProfileEditForm [name="social_platform"]').val(p.social_platform || '');
        $('#onlineProfileEditForm [name="icon"]').val(p.icon || '');
        $('#onlineProfileEditForm [name="profile_url"]').val(p.profile_url || '');
        $('#editModal').modal('show');
    }

    function closeEditModal() {
        $('#editModal').modal('hide');
        $('#onlineProfileEditForm')[0].reset();
        editId = null;
    }

    function deleteProfile(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: getApiUrl('/' + id),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        loadProfiles();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to delete';
                        Swal.fire('Error!', msg, 'error');
                    }
                });
            }
        });
    }

    $('#onlineProfileForm').submit(function(e) {
        e.preventDefault();

        let url = getApiUrl('/Add');
        let data = $(this).serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                Swal.fire('Success!', res.message, 'success');
                $('#onlineProfileForm')[0].reset();
                loadProfiles();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error occurred';
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    $('#onlineProfileEditForm').submit(function(e) {
        e.preventDefault();

        if (!editId) return;

        let url = getApiUrl('/' + editId);
        let data = $(this).serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                Swal.fire('Success!', res.message, 'success');
                closeEditModal();
                loadProfiles();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error occurred';
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    $(document).ready(function() {
        loadProfiles();

        $('#editModal').on('hidden.bs.modal', function () {
            closeEditModal();
        });
    });
</script>
@endsection
