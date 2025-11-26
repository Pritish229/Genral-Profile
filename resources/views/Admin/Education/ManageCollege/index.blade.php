@extends('Admin.layout.app')

@section('title', 'University College Master')

@section('content')
<div class="page-content">

    <x-breadcrumb title="University College Master" :links="['Home' => 'Admin.Dashboard', 'College Master' => '']" />

    <div class="d-flex justify-content-end p-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCollegeModal">
            + Add College
        </button>
    </div>

    <div class="card p-2">
        <table class="table table-bordered table-striped" id="collegeTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Logo</th>
                    <th>University</th>
                    <th>College Name</th>
                    <th>City</th>
                    <th>District</th>
                    <th>State</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="addCollegeModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="addCollegeForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add College</h5>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-6">
                                <label>University</label>
                                <select id="university_id" name="university_id" class="form-select" required>
                                    {{-- select2 fills options --}}
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <x-inputbox
                                    id="org_name"
                                    name="org_name"
                                    type="text"
                                    label="College Name"
                                    placeholder="Enter College Name"
                                    value="{{ old('org_name') }}"
                                    :required="true" />
                            </div>

                            <div class="col-lg-4">
                                <x-inputbox
                                    id="city"
                                    name="city"
                                    type="text"
                                    label="City"
                                    placeholder="Enter City Name"
                                    value="{{ old('city') }}"
                                    :required="true" />
                            </div>

                            <div class="col-lg-4">
                                <x-inputbox
                                    id="district"
                                    name="district"
                                    type="text"
                                    label="District"
                                    placeholder="Enter District Name"
                                    value="{{ old('district') }}"
                                    :required="true" />
                            </div>

                            <div class="col-lg-4">
                                <label>State</label>
                                <select id="state" name="state" class="form-select" required>
                                    <option value="">{{ old('state') ? old('state') : '-- Select state --' }}</option>
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <x-inputbox
                                    id="email_id"
                                    name="email_id"
                                    type="email"
                                    label="Email"
                                    placeholder="Enter Email Address"
                                    value="{{ old('email_id') }}"
                                    :required="true" />
                            </div>

                            <div class="col-lg-6">
                                <x-inputbox
                                    id="phone_no"
                                    name="phone_no"
                                    type="text"
                                    label="Phone No"
                                    placeholder="Enter Phone number"
                                    value="{{ old('phone_no') }}"
                                    :required="true" />
                            </div>

                            <div class="col-lg-12 mt-2">
                                <x-textareabox
                                    id="address"
                                    name="address"
                                    label="Address"
                                    placeholder="Enter Address"
                                    value="{{ old('address') }}" />
                            </div>

                            <div class="col-lg-12 mt-3">
                                <label>Logo</label>
                                <div class="input-images"></div>
                            </div>

                            <div class="col-lg-12">
                                <x-inputbox
                                    id="website_url"
                                    name="website_url"
                                    type="text"
                                    label="Website"
                                    placeholder="Enter Website URL"
                                    value="{{ old('website_url') }}" />
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editCollegeModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="editCollegeForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit College</h5>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <x-inputbox id="edit_org_name" name="org_name" type="text" label="College Name" placeholder="Enter College Name" value=""
                                    :required="true" />
                            </div>

                            <div class="col-lg-4">
                                <x-inputbox id="edit_city" name="city" type="text" label="City" placeholder="Enter City Name"
                                    value=""
                                    :required="true" />
                            </div>

                            <div class="col-lg-4">
                                <x-inputbox id="edit_district" name="district" type="text" label="District" placeholder="Enter District Name"
                                    value=""
                                    :required="true" />
                            </div>

                            <div class="col-lg-4">
                                <label>State</label>
                                <select id="edit_state" name="state" class="form-select" required>
                                    <option value="">-- Select state --</option>
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <x-inputbox id="edit_email_id" name="email_id" type="email" label="Email" placeholder="Enter Email Address" value=""
                                    :required="true" />
                            </div>

                            <div class="col-lg-6">
                                <x-inputbox id="edit_phone_no" name="phone_no" type="text" label="Phone No" placeholder="Enter Phone number" value=""
                                    :required="true" />
                            </div>

                            <div class="col-lg-12 mt-2">
                                <x-textareabox id="edit_address" name="address" label="Address" placeholder="Enter Address" value="" />
                            </div>

                            <div class="col-lg-12 mt-3">
                                <label>Logo</label>
                                <div class="input-images-edit"></div>
                            </div>

                            <div class="col-lg-12">
                                <x-inputbox id="edit_website_url" name="website_url" type="text" label="Website" placeholder="Enter Website URL" value="" />
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function() {

    var table = $('#collegeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('education.college.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'university_name', name: 'university_id' },
            { data: 'org_name', name: 'org_name' },
            { data: 'city', name: 'city' },
            { data: 'district', name: 'district' },
            { data: 'state', name: 'state' },
            { data: 'email_id', name: 'email_id' },
            { data: 'phone_no', name: 'phone_no' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('.input-images').imageUploader({
        multiple: false,
        imagesInputName: 'org_logo'
    });

    $('#university_id').select2({
        dropdownParent: $('#addCollegeModal'),
        width: '100%',
        placeholder: "Select University",
        ajax: {
            url: "{{ route('education.university.allUniversities') }}",
            processResults: function(data) {
                return {
                    results: data.map(u => ({ id: u.id, text: u.org_name }))
                }
            }
        }
    });

    let states = ["Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh","Delhi","Goa","Gujarat","Haryana","Himachal Pradesh","Jharkhand","Karnataka","Kerala","Madhya Pradesh","Maharashtra","Manipur","Meghalaya","Mizoram","Nagaland","Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu","Telangana","Tripura","Uttar Pradesh","Uttarakhand","West Bengal"];
    states.forEach(s => {
        $('#state').append(`<option value="${s}">${s}</option>`);
        $('#edit_state').append(`<option value="${s}">${s}</option>`);
    });

    $('#addCollegeForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('education.college.store') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function() {
                Swal.fire('Success', 'College added successfully', 'success');
                $('#addCollegeModal').modal('hide');
                table.ajax.reload();
                $('#addCollegeForm')[0].reset();
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message ?? 'Error', 'error');
            }
        });
    });

    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');

        $.ajax({
            url: "{{ url('/education/university-colleges') }}/" + id + "/edit",
            type: 'GET',
            success: function(res) {
                let cl = res.data.college;

                $('#edit_id').val(cl.id);
                $('#edit_org_name').val(cl.org_name);
                $('#edit_city').val(cl.city);
                $('#edit_district').val(cl.district);
                $('#edit_state').val(cl.state).trigger('change');
                $('#edit_email_id').val(cl.email_id);
                $('#edit_phone_no').val(cl.phone_no);
                $('#edit_address').val(cl.address);
                $('#edit_website_url').val(cl.website_url);

                $('#edit_university_id').html(`
                    <option value="${cl.university_id}">
                        ${cl.university_name ?? ''}
                    </option>
                `);

                $('.input-images-edit').html('');
                $('.input-images-edit').imageUploader({
                    multiple: false,
                    preloaded: cl.org_logo ? [{ id: 1, src: cl.org_logo }] : []
                });

                $('#editCollegeModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Unable to load data', 'error');
            }
        });
    });

    $('#editCollegeForm').submit(function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let updateURL = "{{ route('education.college.update', ':id') }}".replace(':id', id);
        let formData = new FormData(this);

        $.ajax({
            url: updateURL,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function() {
                Swal.fire('Success', 'College updated successfully', 'success');
                $('#editCollegeModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message ?? 'Error', 'error');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        let id = $(this).data('id');

        Swal.fire({
            title: "Delete?",
            text: "This record will be permanently removed.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/education/university-colleges') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        Swal.fire("Deleted", res.message, "success");
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire("Error", "Unable to delete", "error");
                    }
                });
            }
        });
    });

});
</script>
@endsection
