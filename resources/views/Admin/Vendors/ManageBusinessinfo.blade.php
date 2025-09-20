@extends('Admin.layout.app')

@section('title', 'Home | Dashboard')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Update Basic info"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Students' => 'students.Studentlist',
            'Student Detail' => ['students.Studentlist.studentDetailsPage', $id],
            'Update Basic info' => ''
        ]" 
    />

    <form id="studentForm" enctype="multipart/form-data">
        <div class="row align-items-center">
            <!-- Profile Picture on the left-middle -->
            <div class="col-md-3 mb-3 text-center">
                <label for="avatar" class="form-label">Profile Picture</label>
                <div class="input-images"></div>
                <small class="helpertxt d-block mt-1">Upload a profile picture (jpg, png)</small>
            </div>

            <!-- Name Fields -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4">
                        <x-inputbox id="first_name" label="First Name" type="text" placeholder="Enter First Name" name="first_name"
                            value="{{ old('first_name') }}" :required="true" helpertxt="First Name Maximum 70 Character" />
                    </div>
                    <div class="col-md-4">
                        <x-inputbox id="middle_name" label="Middle Name" type="text" placeholder="Enter Middle Name" name="middle_name"
                            value="{{ old('middle_name') }}" :required="false" helpertxt="Middle Name Maximum 70 Character" />
                    </div>
                    <div class="col-md-4">
                        <x-inputbox id="last_name" label="Last Name" type="text" placeholder="Enter Last Name" name="last_name"
                            value="{{ old('last_name') }}" :required="false" helpertxt="Last Name Maximum 70 Character" />
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dob" class="mb-1">DOB</label>
                            <input type="text" id="dob" name="dob" class="form-control flatpickr"
                                placeholder="Select date of birth" value="{{ old('dob') }}">
                            <small class="helpertxt">Date of Birth</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gender" class="mb-1">Gender</label>
                            <select name="gender" class="form-select" id="gender">
                                <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender')=='other' ? 'selected' : '' }}>Other</option>
                                <option value="unspecified" {{ old('gender')=='unspecified' ? 'selected' : '' }}>Unspecified</option>
                            </select>
                            <small class="helpertxt">Select Gender Type</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian Info -->
        <div class="row mt-3">
            <div class="col-md-6">
                <x-inputbox id="guardian_name" label="Guardian Name" type="text" placeholder="Enter Guardian Name" name="guardian_name"
                    value="{{ old('guardian_name') }}" :required="false" helpertxt="Guardian Name" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="guardian_relation" label="Guardian Relation" type="text" placeholder="Enter Guardian Relation" name="guardian_relation"
                    value="{{ old('guardian_relation') }}" :required="false" helpertxt="Guardian Relation" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="guardian_occupation" label="Guardian Occupation" type="text" placeholder="Enter Guardian Occupation" name="guardian_occupation"
                    value="{{ old('guardian_occupation') }}" :required="false" helpertxt="Guardian Occupation" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="guardian_phone" label="Guardian Phone no" type="text" placeholder="Enter Guardian Phone no" name="guardian_phone"
                    value="{{ old('guardian_phone') }}" :required="false" helpertxt="Phone number must be 10 Digits" />
            </div>
            <div class="col-md-6">
                <x-inputbox id="guardian_email" label="Guardian Email" type="email" placeholder="Enter Guardian Email" name="guardian_email"
                    value="{{ old('guardian_email') }}" :required="false" helpertxt="Guardian email must be valid email" />
            </div>
        </div>

        <!-- Other Info -->
        <div class="row mt-3">
            <div class="col-md-6">
                <x-inputbox id="parent_income" label="Annual Income" type="text" placeholder="Enter Annual Income" name="parent_income"
                    value="{{ old('parent_income') }}" :required="false" helpertxt="Annual income Must Be Number" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="nationality" label="Nationality" type="text" placeholder="Enter Nationality" name="nationality"
                    value="{{ old('nationality') }}" :required="false" helpertxt="Maximum 70 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="mother_tongue" label="Mother Tongue" type="text" placeholder="Enter Mother Tongue" name="mother_tongue"
                    value="{{ old('mother_tongue') }}" :required="false" helpertxt="Maximum 70 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="blood_group" label="Blood Group" type="text" placeholder="Enter Blood Group" name="blood_group"
                    value="{{ old('blood_group') }}" :required="false" helpertxt="Maximum 5 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="current_class" label="Current Class" type="text" placeholder="Enter Current Class" name="current_class"
                    value="{{ old('current_class') }}" :required="false" helpertxt="Maximum 40 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="section" label="Section" type="text" placeholder="Enter Section" name="section"
                    value="{{ old('section') }}" :required="false" helpertxt="Maximum 10 Character" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="rollno" label="Roll No" type="text" placeholder="Enter Roll No" name="rollno"
                    value="{{ old('rollno') }}" :required="false" helpertxt="Maximum 30 Character" />
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
$(function(){

    const studentId = "{{ $id }}";

    // Initialize image uploader
    let avatarUploader = $('.input-images').imageUploader({
        multiple: false,
        imagesInputName: 'avatar_url',
        preloadedInputName: 'preloaded',
        label: 'Click to upload profile picture',
        preloaded: []
    });

    // Initialize date picker
    $(".flatpickr").flatpickr({
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        allowInput: true
    });

    // Fetch existing details
     $.get("{{ url('students') }}/" + studentId + "/Basicinfo/Details", function(response){
        if(response.success){
            let data = response.data;
            let primary = response.primary_details;

            $('#first_name').val(data.first_name || primary.first_name);
            $('#middle_name').val(data.middle_name || primary.middle_name);
            $('#last_name').val(data.last_name || primary.last_name);
            
            // ✅ Use Flatpickr API instead of .val()
            if (data.dob) {
                let dobPicker = document.querySelector("#dob")._flatpickr;
                if (dobPicker) {
                    dobPicker.setDate(data.dob, true);
                }
            }

            $('#gender').val(data.gender || primary.gender);
            $('#blood_group').val(data.blood_group);
            $('#religion').val(data.religion);
            $('#caste').val(data.caste);
            $('#nationality').val(data.nationality);
            $('#mother_tongue').val(data.mother_tongue);
            $('#guardian_name').val(data.guardian_name);
            $('#guardian_relation').val(data.guardian_relation);
            $('#guardian_phone').val(data.guardian_phone);
            $('#guardian_email').val(data.guardian_email);
            $('#guardian_occupation').val(data.guardian_occupation);
            $('#parent_income').val(data.parent_income);
            $('#current_class').val(data.current_class);
            $('#section').val(data.section);
            $('#rollno').val(data.roll_no);

            if(data.avatar_url){
                avatarUploader[0].imageUploader.setPreloaded([{
                    id: 1,
                    src: data.avatar_url
                }]);
            }
        }
    });

    // Submit form via AJAX
    $('#studentForm').submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "{{ url('students') }}/" + studentId + "/Basicinfo/Update",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    confirmButtonText: 'OK'
                });
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errorList = '';
                    $.each(errors, function(key, messages){
                        messages.forEach(function(msg){
                            errorList += msg + '\n';
                        });
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorList
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                }
            }
        });
    });

});
</script>
@endsection


