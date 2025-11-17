@extends('Admin.layout.app')

@section('title', 'University Master')

@section('content')
<div class="page-content">

    <x-breadcrumb title="University Master" :links="['Home' => 'Admin.Dashboard', 'University Master' => '']" />

    <div class="d-flex justify-content-end p-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUniversityModal">
            + Add University
        </button>
    </div>

    <div class="card p-2">
        <table class="table table-bordered table-striped" id="universityTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Logo</th>
                    <th>University Name</th>
                    <th>State</th>
                    <th>District</th>
                    <th>City</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="addUniversityModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="addUniversityForm" method="POST" action="{{ route('education.university.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Add University</h5>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <x-inputbox id="org_name" name="org_name" type="text" label="University / Board Name" placeholder="Enter University Name" value="" :required="true" helpertxt="" />
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="">State</label>
                                        <select id="state" name="state" class="form-select" required>
                                            <option value="">-- Select state --</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <x-inputbox id="district" name="district" type="text" label="District" placeholder="Enter District" value="" :required="true" helpertxt="" />
                                    </div>
                                    <div class="col-lg-12">
                                        <x-inputbox id="city" name="city" type="text" label="City" placeholder="Enter City" value="" :required="true" helpertxt="" />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-inputbox id="email_id" name="email_id" type="email" label="Email" placeholder="Enter Email" value="" :required="true" helpertxt="" />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-inputbox id="alt_email_id" name="alt_email_id" type="email" label="Alternate Email" placeholder="Enter Alternate Email" value="" :required="false" helpertxt="" />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-inputbox id="phone_no" name="phone_no" type="text" label="Phone No" placeholder="Enter Phone Number" value="" :required="true" helpertxt="" />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-inputbox id="alt_phone_no" name="alt_phone_no" type="text" label="Alternate Phone No" placeholder="Enter Alternate Phone" value="" :required="false" helpertxt="" />
                                    </div>

                                    <x-textareabox id="address" name="address" label="Address" placeholder="Enter Address" value="" helpertxt="" />
                                </div>
                            </div>

                            <div class="col-lg-6">

                                <div class="col-lg-12 mb-lg-5 mb-sm-2">
                                    <label>Official Logo</label>
                                    <div class="input-images"></div>
                                </div>
                                <div class="col-lg-12">
                                    <x-inputbox id="website_url" name="website_url" type="text" label="Website URL" placeholder="Enter Website URL" value="" :required="false" helpertxt="" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editUniversityModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="editUniversityForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_id">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit University</h5>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="row">

                                    <div class="col-lg-12">
                                        <x-inputbox id="edit_org_name" name="org_name" type="text" label="University / Board Name" placeholder="Enter University Name" value="" :required="true" helpertxt="" />
                                    </div>

                                    <div class="col-lg-6">
                                        <label>State</label>
                                        <select id="edit_state" name="state" class="form-select" required></select>
                                    </div>

                                    <div class="col-lg-6">
                                        <x-inputbox id="edit_district" name="district" type="text" label="District" placeholder="Enter District" value="" :required="true" helpertxt="" />
                                    </div>

                                    <div class="col-lg-12">
                                        <x-inputbox id="edit_city" name="city" type="text" label="City" placeholder="Enter City" value="" :required="true" helpertxt="" />
                                    </div>

                                    <div class="col-lg-6">
                                        <x-inputbox id="edit_email_id" name="email_id" type="email" label="Email" placeholder="Enter Email" value="" :required="true" helpertxt="" />
                                    </div>

                                    <div class="col-lg-6">
                                        <x-inputbox id="edit_alt_email_id" name="alt_email_id" type="email" label="Alternate Email" placeholder="Enter Alternate Email" value="" :required="false" helpertxt="" />
                                    </div>

                                    <div class="col-lg-6">
                                        <x-inputbox id="edit_phone_no" name="phone_no" type="text" label="Phone No" placeholder="Enter Phone Number" value="" :required="true" helpertxt="" />
                                    </div>

                                    <div class="col-lg-6">
                                        <x-inputbox id="edit_alt_phone_no" name="alt_phone_no" type="text" label="Alternate Phone No" placeholder="Enter Alternate Phone" value="" :required="false" helpertxt="" />
                                    </div>

                                    <x-textareabox id="edit_address" name="address" label="Address" placeholder="Enter Address" value="" helpertxt="" />

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="col-lg-12 mb-lg-5 mb-sm-2">
                                    <label>Official Logo</label>
                                    <div class="input-images-edit"></div>
                                </div>

                                <div class="col-lg-12">
                                    <x-inputbox id="edit_website_url" name="website_url" type="text" label="Website URL" placeholder="Enter Website URL" value="" :required="false" helpertxt="" />
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <a class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
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

        var table = $('#universityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('education.university.list') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'logo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'org_name',
                    orderable: false
                },
                {
                    data: 'state',
                    orderable: false
                },
                {
                    data: 'district',
                    orderable: false
                },
                {
                    data: 'city',
                    orderable: false
                },
                {
                    data: 'email_id'
                },
                {
                    data: 'phone_no'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('.input-images').imageUploader({
            multiple: false,
            imagesInputName: 'org_logo',
            preloadedInputName: 'preloaded',
            label: 'Click to upload logo',
        });

        $('#state').select2({
            dropdownParent: $('#addUniversityModal'),
            placeholder: "-- Select state --",
            width: "100%"
        });

        $('#edit_state').select2({
            dropdownParent: $('#editUniversityModal'),
            placeholder: "-- Select state --",
            width: "100%"
        });

        let states = [
            "Andhra Pradesh",
            "Arunachal Pradesh",
            "Assam",
            "Bihar",
            "Chhattisgarh",
            "Goa",
            "Gujarat",
            "Haryana",
            "Himachal Pradesh",
            "Jharkhand",
            "Karnataka",
            "Kerala",
            "Madhya Pradesh",
            "Maharashtra",
            "Manipur",
            "Meghalaya",
            "Mizoram",
            "Nagaland",
            "Odisha",
            "Punjab",
            "Rajasthan",
            "Sikkim",
            "Tamil Nadu",
            "Telangana",
            "Tripura",
            "Uttar Pradesh",
            "Uttarakhand",
            "West Bengal"
        ];
        states.forEach(s => {
            $('#state').append(`<option value="${s}">${s}</option>`);
            $('#edit_state').append(`<option value="${s}">${s}</option>`);
        });

        $('#addUniversityForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('education.university.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "University added successfully"
                    });
                    $('#addUniversityModal').modal('hide');
                    $('#addUniversityForm')[0].reset();
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message ?? "Something went wrong";
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: msg
                    });
                }
            });
        });

        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');

            $.ajax({
                url: "{{ url('/education/university/master/show') }}/" + id,
                type: "GET",
                success: function(res) {
                    $('#edit_id').val(res.id);
                    $('#edit_org_name').val(res.org_name);
                    $('#edit_state').val(res.state).trigger('change');
                    $('#edit_district').val(res.district);
                    $('#edit_city').val(res.city);
                    $('#edit_email_id').val(res.email_id);
                    $('#edit_alt_email_id').val(res.alt_email_id);
                    $('#edit_phone_no').val(res.phone_no);
                    $('#edit_alt_phone_no').val(res.alt_phone_no);
                    $('#edit_address').val(res.address);
                    $('#edit_website_url').val(res.website_url);

                    $('.input-images-edit').html('');
                    $('.input-images-edit').imageUploader({
                        multiple: false,
                        preloaded: res.org_logo ? [{
                            id: 1,
                            src: res.org_logo_url
                        }] : [],
                        imagesInputName: 'org_logo',
                        preloadedInputName: 'preloaded'
                    });

                    $('#editUniversityModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Unable to load record"
                    });
                }
            });
        });

        $('#editUniversityForm').submit(function(e) {
            e.preventDefault();

            let id = $('#edit_id').val();

            let updateUrl = "{{ route('education.university.update', ':id') }}";
            updateUrl = updateUrl.replace(':id', id);

            let formData = new FormData(this);

            $.ajax({
                url: updateUrl,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "University updated successfully"
                    });
                    $('#editUniversityModal').modal('hide');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message ?? "Something went wrong";
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: msg
                    });
                }
            });
        });

    });
</script>
@endsection