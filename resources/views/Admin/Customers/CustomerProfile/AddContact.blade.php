@extends('Admin.layout.app')

@section('title', 'Home | Customers | Contact')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Contact"
        :links="['Home' => 'Admin.Dashboard', 'Customers' => 'customers.List', 'Contact' => '']" />

    <div class="mt-2">
        <div class="card">
            <div class="p-1" id="customer-details">
                Loading details...
            </div>
        </div>
    </div>

    <h5>Add Contact</h5>
    <hr style="color:#5156be">

    <div id="alert-box" class="mt-2"></div>

    <!-- Progress bar -->
    <div class="progress mb-3" style="height: 25px; display:none;" id="progressContainer">
        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="progressBar">0%</div>
    </div>

    <form id="customerContactForm">
        <div class="row">
            <div class="col-md-3">
                <label for="contact_type" class="form-label">Contact Type</label>
                <select id="contact_type" name="contact_type" class="form-select">
                    <option value="">-- Select Type --</option>
                    <option value="phone">Phone</option>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telegram">Telegram</option>
                </select>
            </div>
            <div class="col-md-3">
                <x-inputbox id="value" label="Value" type="text" placeholder="Enter Value" name="value"
                    value="{{ old('value') }}" :required="false" helpertxt="Enter phone, email, etc." />
            </div>
            <div class="col-md-3">
                <x-inputbox id="country_code" label="Country Code" type="text" placeholder="Enter country code" name="country_code"
                    value="{{ old('country_code') }}" :required="false" helpertxt="Ex: +91, +1, IN" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Optional label (e.g. Father's Phone)" />
            </div>
            
            <div class="col-lg-12 mt-3">
                <button type="submit" class="btn btn-primary">Save & Continue</button>
                <button type="button" class="btn btn-secondary" id="skipBtn">Skip</button>
            </div>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
    let baseUrl = "{{ url('/customers') }}";
    let customer_id = "{{ $id }}";

    function fetchDetails() {
        $.ajax({
            type: "GET",
            url: `${baseUrl}/${customer_id}/Details`,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    let imgSrc = `storage/${response.data.avatar_url}`;
                    let manageBankUrl = `/customers/${response.data.id}/manageBank`;
                    let manageDocUrl = `/customers/${response.data.id}/manageDocument`;
                    let managemediaUrl = `/customers/${response.data.id}/Media/manage`;

                    $("#customer-details").html(`
                <div class="d-flex align-items-start justify-content-between">
                    <!-- Profile + Info -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 160px;">
                            <img src="{{asset('${imgSrc}')}}" class="img-thumbnail w-100" alt="Profile picture">
                        </div>
                        <div class="flex-grow-1">
                                <div class="flex-grow-1">
                                <p><strong>UID:</strong> ${response.primary_details.customer_uid}</p>
                                <p><strong>Name:</strong> ${response.data.full_name}</p>
                                <p><strong>Gender:</strong> ${response.data.gender}</p>
                                <p><strong>Occupation:</strong> ${response.data.occupation}</p>
                                <p><strong>Email:</strong> ${response.primary_details.primary_email}</p>
                            </div>
                        </div>
                    </div>

                   
               
            `);

                } else {
                    $("#customer-details").html(`<p class="text-danger">${response.errors}</p>`);
                }
            },
            error: function(xhr) {
                $("#customer-details").html(`<p class="text-danger">Something went wrong.</p>`);
                console.error(xhr.responseText);
            }
        });
    }

    function updateProgress(value) {
        $("#progressContainer").show();
        $("#progressBar").css("width", value + "%").text(value + "%");
    }

    $(document).ready(function() {
        fetchDetails();

        // Show progress at 40% for Contact step
        updateProgress(40);

        $("#customerContactForm").on("submit", function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the contact.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                type: "POST",
                url: `${baseUrl}/${customer_id}/Manage/Contacts/Add`,
                data: $(this).serialize(),
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        $("#alert-box").html("");
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: "Contact saved successfully",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = `${baseUrl}/${customer_id}/Bank`;
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let html = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(key, value) {
                            html += '<li>' + value[0] + '</li>';
                        });
                        html += '</ul></div>';
                        $("#alert-box").html(html);
                    } else {
                        Swal.fire("Error", "Something went wrong.", "error");
                        console.error(xhr.responseText);
                    }
                }
            });
        });

        // Skip button
        $("#skipBtn").on("click", function() {
            Swal.fire({
                icon: 'info',
                title: 'Skipped',
                text: 'You skipped this step.',
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `${baseUrl}/${customer_id}/Bank`;
            });
        });
    });
</script>
@endsection