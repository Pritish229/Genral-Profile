@extends('Admin.layout.app')

@section('title', 'Manage Vendor')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Vendor"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Vendors' => 'vendors.List',
            'Vendor Detail' => ['vendors.viewDetails', $id],
            'Manage Vendor' => ''
        ]" />

    <!-- Page Header -->
    <div class="mt-3">
        <h4 class="mb-3">
            <i class="fas fa-user-edit"></i>
            <span id="vendorTypeTitle">Personal Vendor Management</span>
        </h4>
    </div>

    <div id="alert-box" class="mt-2"></div>

    <form id="vendorUpdateForm" autocomplete="on" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="vendor_id" name="vendor_id" value="{{ $id }}">
        <input type="hidden" id="vendor_type" name="vendor_type" value="individual">

        <div class="row g-3">
            <h5>Vendor Information</h5>
            <hr style="color:#5156be">

            <div class="col-md-4">
                <x-inputbox id="first_name" label="First Name" type="text" placeholder="Enter First Name" name="first_name"
                    value="" :required="true" helpertxt="First Name Maximum 70 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="middle_name" label="Middle Name" type="text" placeholder="Enter Middle Name" name="middle_name"
                    value="" :required="false" helpertxt="Middle Name Maximum 70 Character" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="last_name" label="Last Name" type="text" placeholder="Enter Last Name" name="last_name"
                    value="" :required="false" helpertxt="Last Name Maximum 70 Character" />
            </div>

            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label for="dob" class="mb-2 labeltxt">DOB</label>
                            <input type="text" id="dob" name="dob" class="form-control flatpickr"
                                placeholder="Select date of birth" value="">
                            <small class="mb-3 pt-1 helpertxt">Date of Birth</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label for="gender" class="mb-2 labeltxt">Gender</label>
                            <select name="gender" class="form-select" id="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="unspecified">Unspecified</option>
                            </select>
                            <small class="mb-3 pt-1 helpertxt">Select Gender Type</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select name="marital_status" class="form-select" id="marital_status">
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <x-inputbox id="occupation" label="Occupation" type="text" placeholder="Enter Occupation" name="occupation"
                            value="" :required="false" helpertxt="Enter Occupation" />
                    </div>
                    <div class="col-md-6">
                        <x-inputbox id="nationality" label="Nationality" type="text" placeholder="Enter Nationality" name="nationality"
                            value="" :required="false" helpertxt="Enter Nationality" />
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label for="profile_picture">Profile Picture</label>
                <div class="input-images"></div>
            </div>

            <h5>Administration Info</h5>
            <hr style="color:#5156be">
            <div class="col-md-4">
                <x-inputbox id="vendor_uid" label="Vendor UID" type="text" placeholder="Enter unique vendor code" name="vendor_uid"
                    value="" :required="false" helpertxt="Unique code don't use spaces" />
            </div>

            <div class="col-md-4">
                <div class="mb-2">
                    <label for="onboarding_channel" class="mb-2 labeltxt">On Boarding</label>
                    <select name="onboarding_channel" class="form-select" id="onboarding_channel">
                        <option value="web">Web</option>
                        <option value="mobile">Mobile</option>
                        <option value="partner">Partner</option>
                        <option value="import">Import</option>
                        <option value="other">Other</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select On Boarding Type</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-2">
                    <label for="status" class="mb-2 labeltxt">Status</label>
                    <select name="status" class="form-select" id="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Vendor Status</small>
                </div>
            </div>

            <div class="col-md-6">
                <x-inputbox id="preferred_language" label="Preferred Language" type="text" placeholder="Enter Preferred Language" name="preferred_language"
                    value="" :required="false" helpertxt="Max 10 Character" />
            </div>

            <div class="col-md-6">
                <x-inputbox id="preferred_currency" label="Preferred Currency" type="text" placeholder="Enter Preferred Currency" name="preferred_currency"
                    value="" :required="false" helpertxt="Enter Preferred Currency" />
            </div>

            <div class="col-md-12">
                <x-textareabox id="notes" label="Notes" placeholder="Enter internal notes" name="notes" value="" helpertxt="For Remarks" />
            </div>
        </div>

        <div class="mt-3">
            <button id="updateBtn" type="submit" class="btn btn-primary">Update Vendor</button>
            <a href="{{ url('/vendors/' . $id . '/view/Details') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    jQuery(function($) {
        let baseUrl = "{{ url('/vendors') }}";
        let vendorId = "{{ $id }}";
        let vendorType = "individual";

        // Avatar uploader
        $('.input-images').imageUploader({
            multiple: false,
            imagesInputName: 'avatar_url',
            preloadedInputName: 'preloaded',
            label: 'Click to upload profile picture',
            preloaded: []
        });

        // Date pickers
        $(".flatpickr").flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true
        });

        // Initialize Select2 for select elements
        $('#status').select2({
            minimumResultsForSearch: Infinity, // Disable search
            width: '100%'
        });

        $('#gender').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        $('#marital_status').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        $('#onboarding_channel').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        // Load vendor data
        function loadVendorData() {
            $.ajax({
                url: `${baseUrl}/${vendorId}/edit`,
                type: 'GET',
                success: function(response) {
                    console.log('Vendor data loaded:', response); // Debug log

                    if (response.success) {
                        const vendor = response.data.vendor;
                        const profile = response.data.profile;

                        // Fill vendor data
                        $('#vendor_uid').val(vendor.vendor_uid || '');
                        $('#primary_email').val(vendor.primary_email || '');
                        $('#primary_phone').val(vendor.primary_phone || '');
                        $('#onboarding_channel').val(vendor.onboarding_channel || 'web').trigger('change');

                        // Debug status loading
                        console.log('Loading status from vendor:', vendor.status);
                        $('#status').val(vendor.status || 'active').trigger('change');
                        console.log('Status field value after setting:', $('#status').val());

                        $('#notes').val(vendor.notes || '');

                        // Fill profile data
                        if (profile) {
                            $('#first_name').val(profile.first_name || '');
                            $('#middle_name').val(profile.middle_name || '');
                            $('#last_name').val(profile.last_name || '');

                            // Handle date field
                            if (profile.dob) {
                                document.querySelector("#dob")._flatpickr.setDate(profile.dob, true, "Y-m-d");
                            }

                            $('#gender').val(profile.gender || 'male').trigger('change');
                            $('#marital_status').val(profile.marital_status || 'single').trigger('change');
                            $('#occupation').val(profile.occupation || '');
                            $('#nationality').val(profile.nationality || '');
                            $('#preferred_language').val(profile.preferred_language || '');
                            $('#preferred_currency').val(profile.preferred_currency || '');

                            // Handle avatar
                            if (profile.avatar_url) {
                                $('.input-images').imageUploader({
                                    multiple: false,
                                    imagesInputName: 'avatar_url',
                                    preloadedInputName: 'preloaded',
                                    label: 'Click to upload profile picture',
                                    preloaded: [{
                                        id: 1,
                                        src: "{{ asset('storage') }}/" + profile.avatar_url
                                    }]
                                });
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load vendor data'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading vendor data:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load vendor data: ' + (xhr.responseJSON?.message || xhr.statusText)
                    });
                }
            });
        }

        // Form Submit
        $('#vendorUpdateForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#updateBtn').prop('disabled', true);
            const formData = new FormData(this);

            // Debug: Log form data including status
            console.log('Status value being sent:', $('#status').val());
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            Swal.fire({
                title: 'Updating vendor...',
                html: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                    url: `${baseUrl}/${vendorId}/update`,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false
                })
                .done(function(response) {
                    Swal.close();

                    if (response && response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Vendor updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${vendorId}/view/Details`;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: (response && response.message) ? response.message : 'Update failed.'
                        });
                    }
                })
                .fail(function(xhr) {
                    Swal.close();
                    $btn.prop('disabled', false);

                    console.log('Error response:', xhr.responseJSON); // Debug log

                    if (xhr.status === 422) {
                        const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};
                        console.log('Validation errors:', errors); // Debug log
                        let html = '<div class="alert alert-danger"><ul>';
                        Object.keys(errors).forEach(k => {
                            const v = errors[k];
                            html += '<li><strong>' + k + ':</strong> ' + (Array.isArray(v) ? v[0] : v) + '</li>';
                        });
                        html += '</ul></div>';
                        $('#alert-box').html(html);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong, please try again. Status: ' + xhr.status
                        });
                    }
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });

        // Load data on page load
        loadVendorData();
    });
</script>
@endsection