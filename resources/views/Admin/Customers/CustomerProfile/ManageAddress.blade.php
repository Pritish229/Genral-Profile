@extends('Admin.layout.app')

@section('title', 'Home | Manage Address')

@section('content')
<div class="page-content">
    <x-breadcrumb
        title="Manage Address"
        :links="[
            'Home' => 'Admin.Dashboard',
            'Customers' => 'customers.List',
            'Customer Details' => ['customers.viewDetails', ['id' => $id]],
            'Manage Address' => ''
        ]" />

    <form id="customerAddressForm" data-profile-type="individual">
        @csrf
        <input type="hidden" name="profile_type" value="individual">
        <div class="row">
            <div class="col-md-3">
                <x-inputbox id="state" label="State" type="text" placeholder="Enter State Name" name="state"
                    value="{{ old('state') }}" :required="true" helpertxt="Max 120 characters" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="district" label="District" type="text" placeholder="Enter District Name" name="district"
                    value="{{ old('district') }}" :required="true" helpertxt="Max 120 characters" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="city" label="City" type="text" placeholder="Enter City Name" name="city"
                    value="{{ old('city') }}" :required="true" helpertxt="Max 120 characters" />
            </div>
            <div class="col-md-3">
                <x-inputbox id="pincode" label="Pincode" type="text" placeholder="Enter Pincode" name="pincode"
                    value="{{ old('pincode') }}" :required="true" helpertxt="6 digits only" />
            </div>

            <div class="col-md-4">
                <x-inputbox id="line1" label="Line 1" type="text" placeholder="Enter Line 1" name="line1"
                    value="{{ old('line1') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="line2" label="Line 2" type="text" placeholder="Enter Line 2" name="line2"
                    value="{{ old('line2') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="landmark" label="Landmark" type="text" placeholder="Enter Landmark" name="landmark"
                    value="{{ old('landmark') }}" :required="false" />
            </div>

            <div class="col-md-4">
                <x-inputbox id="label" label="Label" type="text" placeholder="Enter Label" name="label"
                    value="{{ old('label') }}" :required="false" helpertxt="Ex: Home, Office" />
            </div>

            <div class="col-md-4">
                <div class="mb-2">
                    <label for="address_type" class="mb-2 labeltxt">Address Type</label>
                    <select name="address_type" class="form-select" id="address_type" required>
                        <option value="permanent">Permanent</option>
                        <option value="office">Office</option>
                        <option value="other">Other</option>
                    </select>
                    <small class="mb-3 pt-1 helpertxt">Select Address Type</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                    <label class="form-check-label" for="is_primary">Set as Primary Address</label>
                </div>
            </div>

            <div class="col-md-4">
                <x-inputbox id="longitude" label="Longitude (Optional)" type="text" placeholder="Enter Longitude"
                    name="longitude" value="{{ old('longitude') }}" :required="false" />
            </div>
            <div class="col-md-4">
                <x-inputbox id="latitude" label="Latitude (Optional)" type="text" placeholder="Enter Latitude"
                    name="latitude" value="{{ old('latitude') }}" :required="false" />
            </div>

            <div class="col-lg-12 mt-2">
                <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
            </div>
        </div>
    </form>

    <div class="mt-4">
        <table class="table table-bordered" id="addressesTable">
            <thead>
                <tr>
                    <th>#</th><th>State</th><th>District</th><th>City</th><th>Pincode</th>
                    <th>Label</th><th>Type</th><th>Primary</th><th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function(){
    if(typeof $.fn.select2!=='undefined'){
        $('#address_type').select2({minimumResultsForSearch:Infinity,width:'100%'}).on('change',function(){
            $(this).val($(this).val()).trigger('change.select2');
        });
    }

    $.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

    const baseUrl = "{{ url('customers') }}";
    const customerId = "{{ $id }}";
    const profileType = "individual";
    let editId = null;

    function loadAddresses(){
        const url = `${baseUrl}/${customerId}/Get/Address/List`;
        $.get(url,function(res){
            if(res.success){
                let rows='',idx=1;
                res.data.forEach(a=>{
                    const badge = a.is_primary
                        ? `<span class="badge bg-success rounded-pill">Primary</span>`
                        : `<span class="text-muted">—</span>`;
                    rows+=`<tr data-id="${a.id}">
                        <td>${idx++}</td>
                        <td>${a.state||'-'}</td>
                        <td>${a.district||'-'}</td>
                        <td>${a.city||'-'}</td>
                        <td>${a.pincode||'-'}</td>
                        <td>${a.label||'-'}</td>
                        <td>${a.address_type||'-'}</td>
                        <td>${badge}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editBtn">Edit</button>
                            ${a.is_primary?'':`<button class="btn btn-sm btn-danger deleteBtn">Delete</button>`}
                        </td>
                    </tr>`;
                });
                $("#addressesTable tbody").html(rows);
            }else{
                $("#addressesTable tbody").html('<tr><td colspan="9">No addresses found.</td></tr>');
            }
        }).fail(function(xhr){
            $("#addressesTable tbody").html('<tr><td colspan="9">Failed to load addresses.</td></tr>');
        });
    }

    function resetForm(){
        $("#customerAddressForm")[0].reset();
        $("#save-btn").text("Save");
        $("#is_primary").prop('checked',false);
        editId=null;
        if(typeof $.fn.select2!=='undefined'){
            $('#address_type').val('permanent').trigger('change.select2');
        }else{
            $('#address_type').val('permanent');
        }
    }

    $("#customerAddressForm").on("submit",function(e){
        e.preventDefault();
        const required = ['state','district','city','pincode','address_type'];
        let missing = required.filter(f=>!$(`#${f}`).val().trim());
        if(missing.length){
            Swal.fire({icon:'error',title:'Validation Error',
                html:'Please fill all required fields: '+missing.join(', ')+'.'});
            return;
        }

        const formData = new FormData(this);
        formData.append('profile_type',profileType);

        const url = editId
            ? `${baseUrl}/${customerId}/individual/addresses/${editId}`
            : `${baseUrl}/${customerId}/Manage/Addresses`;

        $.ajax({
            url, type:'POST', data:formData,
            processData:false, contentType:false,
            success:function(res){
                if(res.success){
                    Swal.fire({icon:'success',title:'Success!',text:res.message,timer:2000,showConfirmButton:false});
                    loadAddresses(); resetForm();
                }else{
                    Swal.fire({icon:'error',title:'Error!',text:res.message||'Error saving address'});
                }
            },
            error:function(xhr){
                let html='';
                if(xhr.status===422){
                    $.each(xhr.responseJSON.errors,function(k,v){html+=v[0]+'<br>';});
                    Swal.fire({icon:'error',title:'Validation Error',html});
                }else{
                    Swal.fire({icon:'error',title:'Error',text:'Something went wrong.'});
                }
            }
        });
    });

    $("#cancel-btn").on("click",resetForm);

    $(document).on("click",".editBtn",function(){
        const tr=$(this).closest("tr");
        editId=tr.data("id");
        const url=`${baseUrl}/${customerId}/addresses/${editId}?profile_type=${profileType}`;
        $.get(url,function(res){
            if(res.success){
                const a=res.data;
                $("#state").val(a.state);
                $("#district").val(a.district);
                $("#city").val(a.city);
                $("#pincode").val(a.pincode);
                $("#line1").val(a.line1||'');
                $("#line2").val(a.line2||'');
                $("#landmark").val(a.landmark||'');
                $("#label").val(a.label||'');
                $("#address_type").val(a.address_type).trigger('change.select2');
                $("#longitude").val(a.longitude||'');
                $("#latitude").val(a.latitude||'');
                $("#is_primary").prop('checked',!!a.is_primary);
                $("#save-btn").text("Update");
            }else{
                Swal.fire({icon:'error',title:'Error!',text:res.message||'Failed to load address.'});
            }
        }).fail(function(){
            Swal.fire({icon:'error',title:'Error',text:'Failed to load address details.'});
        });
    });

    $(document).on("click",".deleteBtn",function(){
        const addressId=$(this).closest("tr").data("id");
        const url=`${baseUrl}/${customerId}/individual/addresses/${addressId}`;
        Swal.fire({
            title:"Are you sure?",text:"This address will be deleted!",icon:"warning",
            showCancelButton:true,confirmButtonColor:"#d33",cancelButtonColor:"#3085d6",
            confirmButtonText:"Yes, delete it!"
        }).then(result=>{
            if(result.isConfirmed){
                $.ajax({url,type:"DELETE",success:function(res){
                    if(res.success){
                        Swal.fire("Deleted!",res.message,"success"); loadAddresses();
                    }else{
                        Swal.fire("Error!",res.message,"error");
                    }
                },error:function(){
                    Swal.fire("Error!","Something went wrong.","error");
                }});
            }
        });
    });

    loadAddresses();
});
</script>
@endsection