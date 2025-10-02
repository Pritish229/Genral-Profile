@extends('Admin.layout.app')

@section('title', 'Home | Vendors | Manage Online Profiles')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Online Profiles"
        :links="['Home' => 'Admin.Dashboard', 'Vendors' => 'vendors.List', 'Vendor Details' => ['vendors.viewDetails', ['id' => $id]],'Manage Online Profiles' => '']" />

    <div class="card p-3">
        <form id="onlineProfileForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Social Platform</label>
                    <input type="text" name="social_platform" id="social_platform" class="form-control" placeholder="LinkedIn" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icon</label>
                    <input type="text" name="icon" id="icon" class="form-control" placeholder="fab fa-linkedin">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profile URL</label>
                    <input type="url" name="profile_url" id="profile_url" class="form-control" placeholder="https://..." required>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                <button type="button" class="btn btn-secondary d-none" id="cancelEditBtn">Cancel Edit</button>
            </div>
        </form>
    </div>

    <div class="card p-3 mt-3">
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
@endsection

@section('script')
<script>
    let baseUrl = "{{ url('/vendors') }}";
    let vendor_id = "{{ $id }}";
    let type = "{{ $type }}";
    let editId = null;

    function loadProfiles() {
        $.get(`${baseUrl}/${vendor_id}/${type}/OnlineProfile/List`, function(res) {
            console.log(res);
            
            let rows = '';
            if (res.success && res.data.length) {
                res.data.forEach((p, i) => {
                    rows += `
                        <tr>
                            <td>${i+1}</td>
                            <td>${p.social_platform || '-'}</td>
                            <td>${p.icon || '-'}</td>
                            <td><a href="${p.profile_url}" target="_blank">${p.profile_url}</a></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick='editProfile(${JSON.stringify(p)})'>Edit</button>
                                <button class="btn btn-sm btn-danger" onclick='deleteProfile(${p.id})'>Delete</button>
                            </td>
                        </tr>`;
                });
            } else {
                rows = `<tr><td colspan="5" class="text-center">No profiles found</td></tr>`;
            }
            $('#profilesTable tbody').html(rows);
        });
    }

    function editProfile(p) {
        editId = p.id;
        $('#social_platform').val(p.social_platform || '');
        $('#icon').val(p.icon || '');
        $('#profile_url').val(p.profile_url || '');
        $('#cancelEditBtn').removeClass('d-none');
        $('#saveBtn').text('Update');
    }

    $('#cancelEditBtn').on('click', function() {
        editId = null;
        $('#onlineProfileForm')[0].reset();
        $('#cancelEditBtn').addClass('d-none');
        $('#saveBtn').text('Save');
    });

    function deleteProfile(id) {
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/${vendor_id}/${type}/OnlineProfile/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        Swal.fire('Deleted', res.message, 'success');
                        loadProfiles();
                    }
                });
            }
        });
    }

    $('#onlineProfileForm').on('submit', function(e) {
        e.preventDefault();
        const url = editId
            ? `${baseUrl}/${vendor_id}/${type}/OnlineProfile/${editId}`
            : `${baseUrl}/${vendor_id}/${type}/OnlineProfile`;
        const method = editId ? 'PUT' : 'POST';
        $.ajax({
            url,
            type: method,
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                Swal.fire('Success', res.message, 'success');
                $('#onlineProfileForm')[0].reset();
                $('#cancelEditBtn').addClass('d-none');
                $('#saveBtn').text('Save');
                editId = null;
                loadProfiles();
            },
            error: function(xhr) {
                Swal.fire('Error', 'Validation failed', 'error');
            }
        });
    });

    $(document).ready(function() {
        loadProfiles();
    });
</script>
@endsection
