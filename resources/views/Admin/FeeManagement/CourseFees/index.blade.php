@extends('Admin.layout.app')

@section('title', 'College Course Fees')

@section('content')

<div class="page-content">

    <x-breadcrumb title="College Course Fees"
        :links="['Home' => 'Admin.Dashboard', 'College Course Fees' => '']" />

    <div class="p-1">

        <div class="row">

            <div class="col-md-3 mb-3">
                <label class="form-label">Select University</label>
                <select id="university_id" class="form-control select2"></select>
                <small>Select University</small>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Select College</label>
                <select id="college_id" class="form-control select2"></select>
                <small>Select College</small>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Select Parent Course</label>
                <select id="parent_course_id" class="form-control select2"></select>
                <small>Select Parent Course</small>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Select Child Course</label>
                <select id="child_course_id" class="form-control select2" disabled></select>
                <small>Select Child Course</small>
            </div>

        </div>

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="form-label">Session Name</label>
                <input type="text" id="session_name" class="form-control" placeholder="Enter Session Name">
                <small>Ex 2024-2025</small>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Session Start Date</label>
                <input type="text" id="session_start_date" class="form-control flatpickr" placeholder="Enter Date">
                <small>Select Start Date</small>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Session End Date</label>
                <input type="text" id="session_end_date" class="form-control flatpickr" placeholder="Enter Date">
                <small>Select End Date</small>
            </div>

        </div>

        <div id="no_fee_view" class="text-center p-5 border rounded bg-light" style="display:block;">
            <h4 class="fw-bold mb-2 text-secondary">No Fee Structure Loaded</h4>
            <p class="mb-3 text-muted">Please select university, college and course to load fee details.</p>
            <a href="{{route('coursefee.manage')}}" class="btn btn-outline-primary px-4">View Fee Structure</a>
        </div>

        <div id="fee_table"></div>

        <div class="text-end mt-3" id="assignFeeBtnWrapper" style="display:none;">
            <button id="assignFeeBtn" class="btn btn-primary px-4">Assign Fee</button>
        </div>

    </div>

</div>

@endsection


@section('script')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
$(document).ready(function() {

    $('.flatpickr').flatpickr({ dateFormat:"d-m-Y" });
    $('#child_course_id').prop('disabled', true);

    $('#university_id').select2({
        placeholder: 'Select University',
        ajax: {
            url: '{{ route("education.university.allUniversities") }}',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term }),
            processResults: data => ({
                results: data.map(item => ({ id: item.id, text: item.org_name }))
            })
        }
    });

    $('#college_id').select2({ placeholder:'Select College' });
    $('#parent_course_id').select2({ placeholder:'Select Parent Course' });
    $('#child_course_id').select2({ placeholder:'Select Child Course' });

    $('#university_id').on('change', function() {

        $('#college_id, #parent_course_id, #child_course_id')
            .empty().trigger('change');

        $('#child_course_id').prop('disabled', true);

        $('#fee_table').empty();
        $('#assignFeeBtnWrapper').hide();
        $('#no_fee_view').show();

        $.ajax({
            url: '{{ route("education.college.universitycolleges", ":id") }}'
                .replace(':id', $(this).val()),
            type: 'GET',
            success: function(data) {
                data.data.forEach(col => {
                    $('#college_id').append(
                        `<option value="${col.id}">${col.org_name}</option>`
                    );
                });
                $('#college_id').trigger('change');
            }
        });
    });

    $('#college_id').on('change', function() {

        $('#parent_course_id').empty().trigger('change');
        $('#child_course_id').empty().trigger('change').prop('disabled', true);

        $('#fee_table').empty();
        $('#assignFeeBtnWrapper').hide();
        $('#no_fee_view').show();

        $.ajax({
            url: '{{ route("education.collegecourse.parentCourses", ":id") }}'
                .replace(':id', $(this).val()),
            type: 'GET',
            success: function(data) {
                data.data.forEach(parent => {
                    $('#parent_course_id').append(
                        `<option value="${parent.course_id}">${parent.course_name}</option>`
                    );
                });
                $('#parent_course_id').trigger('change');
            }
        });
    });

    $('#parent_course_id').on('change', function() {

        let parent = $(this).val();
        let college = $('#college_id').val();

        $('#fee_table').empty();
        $('#assignFeeBtnWrapper').hide();
        $('#no_fee_view').show();

        if(!parent || !college){
            $('#child_course_id').prop('disabled', true).empty().trigger('change');
            return;
        }

        $('#child_course_id').empty().prop('disabled', false);

        let url = '{{ route("education.collegecourse.childCourses", ["college"=>":collegeId","id"=>":parentId"]) }}'
            .replace(':collegeId', college)
            .replace(':parentId', parent);

        $.ajax({
            url: url,
            type:'GET',
            success:function(data){
                data.data.forEach(child => {
                    $('#child_course_id').append(
                        `<option value="${child.course_id}"
                                 data-duration="${child.duration_in_years}"
                                 data-code="${child.course_code}">
                                 ${child.course_name}
                         </option>`
                    );
                });
                $('#child_course_id').trigger('change');
            }
        });

    });

    $('#child_course_id').on('change', function() {

        $('#fee_table').empty();
        $('#assignFeeBtnWrapper').hide();
        $('#no_fee_view').show();

        if(!$('#university_id').val() ||
           !$('#college_id').val() ||
           !$('#parent_course_id').val() ||
           !$('#child_course_id').val()) return;

        $.ajax({
            url:'{{ route("fee.feemaster.list") }}',
            data:{ fee_type:1 },
            type:'GET',
            success:function(res){
                generateFeeTable(res.data);
            }
        });

    });

    function generateFeeTable(data){

        $('#no_fee_view').hide();

        let html = `
        <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Select</th>
                    <th>Fee Name</th>
                    <th>Collection Type</th>
                    <th>Amount</th>
                    <th>Times in Year</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
        `;

        data.forEach(row => {
            html += `
            <tr>
                <td><input type="checkbox" class="fee_check"></td>
                <td data-fee-id="${row.id}">${row.fee_name}</td>
                <td>
                    <select class="form-select collection_type">
                        <option value="mandatory">Mandatory</option>
                        <option value="optional">Optional</option>
                    </select>
                </td>
                <td><input type="number" class="form-control amount" value="0"></td>
                <td><input type="number" class="form-control years" value="1"></td>
                <td><input type="number" class="form-control total" value="0" readonly></td>
            </tr>`;
        });

        html += `
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Final Amount:</th>
                    <th><input type="number" id="final_amount" class="form-control" readonly></th>
                </tr>
            </tfoot>
        </table>
        </div>`;

        $('#fee_table').html(html);
        $('#assignFeeBtnWrapper').show();

        bindCalculationEvents();
    }

    function bindCalculationEvents() {

        $(document).on('input','.amount, .years', function(){
            let row = $(this).closest('tr');
            let amt = parseFloat(row.find('.amount').val()) || 0;
            let yrs = parseFloat(row.find('.years').val()) || 1;
            row.find('.total').val(amt * yrs);
            calculateFinalAmount();
        });

        $(document).on('change','.fee_check', calculateFinalAmount);
    }

    function calculateFinalAmount(){
        let total = 0;

        $('#fee_table tbody tr').each(function(){
            if($(this).find('.fee_check').is(':checked')){
                total += parseFloat($(this).find('.total').val()) || 0;
            }
        });

        $('#final_amount').val(total);
    }

    $(document).on('click', '#assignFeeBtn', function() {

        let btn = $(this).prop('disabled', true).text('Processing...');
        $('#fee_table').css('opacity','.4')
                       .find('input, select')
                       .prop('disabled', true);

        let fees = [];
        $('#fee_table tbody tr').each(function(){
            if($(this).find('.fee_check').is(':checked')){
                fees.push({
                    fee_id: $(this).find('td:eq(1)').data('fee-id'),
                    fee_head: $(this).find('td:eq(1)').text().trim(),
                    collection_type: $(this).find('.collection_type').val(),
                    amount: $(this).find('.amount').val(),
                    times_in_year: $(this).find('.years').val(),
                    total_amount: $(this).find('.total').val()
                });
            }
        });

        if(fees.length === 0){
            swal.fire({
                title:'No Fee Selected',
                text:'Please select at least one fee.',
                icon:'warning'
            });
            btn.prop('disabled', false).text('Assign Fee');
            $('#fee_table').css('opacity','1')
                           .find('input, select')
                           .prop('disabled', false);
            return;
        }

        let duration = $('#child_course_id option:selected').data('duration');
        let courseCode = $('#child_course_id option:selected').data('code');

        let payload = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            college_id: $('#college_id').val(),
            course_id: $('#child_course_id').val(),
            course_name: $('#child_course_id option:selected').text(),
            course_code: courseCode,
            duration_in_years: duration,
            session_name: $('#session_name').val(),
            session_start_date: $('#session_start_date').val(),
            session_end_date: $('#session_end_date').val(),
            fees: fees
        };

        $.ajax({
            url:"{{ route('coursefee.store') }}",
            type:"POST",
            data: payload,
            success:function(){

                btn.prop('disabled', false).text('Assign Fee');
                $('#fee_table').css('opacity','1')
                               .find('input, select')
                               .prop('disabled', false);

                $('#fee_table').empty();
                $('#assignFeeBtnWrapper').hide();
                $('#no_fee_view').show();

                $('#session_name,#session_start_date,#session_end_date').val('');
                $('#university_id').val(null).trigger('change');

                swal.fire({
                    title:'Success',
                    text: 'Fees Assigned Successfully',
                    icon:'success'
                });

            },
            error:function(e){
                btn.prop('disabled', false).text('Assign Fee');
                $('#fee_table').css('opacity','1')
                               .find('input, select')
                               .prop('disabled', false);
                swal.fire({
                    title:'Error',
                    text: ``+ (e.responseJSON?.message || 'An error occurred while assigning fees.'),
                    icon:'error'
                });
            }
        });
    });
});
</script>

@endsection
