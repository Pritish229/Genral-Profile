@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Manage Business Online Profiles')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Business Online Profiles"
        :links="[
        'Home' => 'Admin.Dashboard',
        'Vendors' => 'vendors.List',
        'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],
        'Business List' => ['vendors.Businesslist', $id],
        'Business Details' => ['vendors.BusinessDetails', ['id' => $id, 'business_id' => $business_id]],
            'Business Profiles' => ''
        ]" />

    {{-- ==================== ADD FORM ==================== --}}
    <div class="card p-3 mb-3">
        <form id="businessProfileForm">
            @csrf
            <input type="hidden" name="business_id" id="business_id" value="{{ $business_id ?? '' }}">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Social Platform</label>
                    <input type="text" name="social_platform" class="form-control" placeholder="LinkedIn" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icon</label>
                    <input type="text" name="icon" class="form-control" placeholder="fab fa-linkedin">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profile URL</label>
                    <input type="url" name="profile_url" class="form-control" placeholder="https://..." required>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>

    {{-- ==================== TABLE ==================== --}}
    <div class="card p-3">
        <table class="table table-bordered" id="businessProfilesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Business</th>
                    <th>Platform</th>
                    <th>Icon</th>
                    <th>URL</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ==================== EDIT MODAL ==================== --}}
<div class="modal fade" id="editBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="businessProfileEditForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="business_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Business Profile</h5>
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
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";
    let business_id = "{{ $business_id ?? '' }}";
    let editId = null;

    function getApiUrl(path = '') {
        return `${baseUrl}/${vendor_id}/business/OnlineProfile${path}`;
    }

    // ======== LOAD BUSINESS PROFILES ========
    function loadBusinessProfiles() {
        $.get((`${baseUrl}/${vendor_id}/${business_id}/business/OnlineProfile/List/`), function(res) {
            let rows = '';
            if (res.success && res.data.length > 0) {
                res.data.forEach((p, i) => {
                    rows += `<tr>
                        <td>${i + 1}</td>
                        <td>${p.business_name || '-'}</td>
                        <td>${p.social_platform || '-'}</td>
                        <td>${p.icon || '-'}</td>
                        <td><a href="${p.profile_url}" target="_blank">${p.profile_url}</a></td>
                        <td>
                            <button 
                                class="btn btn-sm btn-primary edit-btn"
                                data-id="${p.id}"
                                data-business="${p.business_id}"
                                data-platform="${p.social_platform}"
                                data-icon="${p.icon || ''}"
                                data-url="${p.profile_url}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBusinessProfile(${p.id})">Delete</button>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="6" class="text-center">No profiles found</td></tr>`;
            }
            $('#businessProfilesTable tbody').html(rows);
        }).fail(function() {
            $('#businessProfilesTable tbody').html(`<tr><td colspan="6" class="text-center text-danger">Error loading profiles</td></tr>`);
        });
    }

    // ======== OPEN EDIT MODAL ========
    $(document).on('click', '.edit-btn', function() {
        let p = {
            id: $(this).data('id'),
            business_id: $(this).data('business'),
            social_platform: $(this).data('platform'),
            icon: $(this).data('icon'),
            profile_url: $(this).data('url')
        };
        editId = p.id;
        $('#businessProfileEditForm [name="business_id"]').val(p.business_id);
        $('#businessProfileEditForm [name="social_platform"]').val(p.social_platform);
        $('#businessProfileEditForm [name="icon"]').val(p.icon);
        $('#businessProfileEditForm [name="profile_url"]').val(p.profile_url);
        $('#editBusinessModal').modal('show');
    });

    function closeBusinessEditModal() {
        $('#editBusinessModal').modal('hide');
        $('#businessProfileEditForm')[0].reset();
        editId = null;
    }

    // ======== DELETE PROFILE ========
    function deleteBusinessProfile(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the record!",
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
                        loadBusinessProfiles();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to delete';
                        Swal.fire('Error!', msg, 'error');
                    }
                });
            }
        });
    }

    // ======== ADD PROFILE ========
    $('#businessProfileForm').submit(function(e) {
        e.preventDefault();
        let url = getApiUrl('');
        let data = $(this).serialize();
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                Swal.fire('Success!', res.message, 'success');
                $('#businessProfileForm')[0].reset();
                loadBusinessProfiles();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error occurred';
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    // ======== EDIT PROFILE ========
    $('#businessProfileEditForm').submit(function(e) {
        e.preventDefault();
        if (!editId) return;
        let url = getApiUrl('/' + editId);
        let data = $(this).serialize();
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                Swal.fire('Updated!', res.message, 'success');
                closeBusinessEditModal();
                loadBusinessProfiles();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error occurred';
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    // ======== INIT ========
    $(document).ready(function() {
        loadBusinessProfiles();
        $('#editBusinessModal').on('hidden.bs.modal', closeBusinessEditModal);
    });
</script>
@endsection