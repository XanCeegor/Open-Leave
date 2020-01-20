@extends('layouts.app')
@section('content')

<head>
    <!-- Include Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js">
    </script>
    <!-- for autocomplete search -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js">
    </script>
    <title>
        Create leave request
    </title>
</head>

<body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.index') }}">HR</a></li>
            <li class="breadcrumb-item active">New Leave Request</li>
        </ol>
    </nav>
    <hr><br>

    <h3>Create Leave Request</h3><br>
    <form action="{{ route('hr.store') }}" enctype="multipart/form-data"
        style="border:2px solid #343a40; border-radius:15px; padding:20px" method="post" id="leaveForm"
        autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <label for="name">Requested on behalf of</label>
                <input class="typeahead form-control" id="name" type="text" name="name" required>
                <div class="mt-3">
                    <div class="form-group">
                        <label for="type" name="type_label">Type of leave</label>
                        <select class="form-control" id="type" name="type">
                            <option>Annual Leave</option>
                            <option>Sick Leave</option>
                            <option>Family Responsibility Leave</option>
                            <option>Paternity/Maternity Leave</option>
                            <option>Unpaid Leave</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="startdate_datepicker">Start Date</label>
                <div class="start_date input-group mb-4">
                    <input class="form-control start_date" required type="text" placeholder="Start date"
                        id="startdate_datepicker" name="start_date" disabled>
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text start_date_calendar" aria-hidden="true "></span>
                    </div>
                </div>
                <div class="form-check-inline">
                    <div class="form-check">
                        <label class="form-check-label" for="fullday_start">Full-day</label>
                        <input class="form-check-input" type="radio" name="start_date_radio" id="fullday_start"
                            value="fullday" checked="checked" disabled>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label" for="halfday_start">Half-day</label>
                        <input class="form-check-input" type="radio" name="start_date_radio" id="halfday_start"
                            value="halfday" disabled>
                    </div>
                </div>
                <div class="form-check-inline mt-3 mb-3">
                    <div class="form-check">
                        <label class="form-check-label" for="start_morning" id="start_morning_label"
                            hidden>Morning</label>
                        <input class="form-check-input" type="radio" name="start_date_period" id="start_morning"
                            value="morning" checked="checked" hidden disabled>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label" for="start_afternoon" id="start_afternoon_label"
                            hidden>Afternoon</label>
                        <input class="form-check-input" type="radio" name="start_date_period" id="start_afternoon"
                            value="afternoon" hidden disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="enddate_datepicker">End Date</label>
                <div class="end_date input-group mb-4">
                    <input class="form-control end_date" required type="text" placeholder="End date"
                        id="enddate_datepicker" name="end_date" disabled>
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text end_date_calendar" aria-hidden="true "></span>
                    </div>
                </div>
                <div class="form-check-inline">
                    <div class="form-check">
                        <label class="form-check-label" for="fullday_end" id="fullday_end_label" hidden>Full-day</label>
                        <input class="form-check-input" type="radio" name="end_date_radio" id="fullday_end"
                            value="fullday" checked="checked" hidden disabled>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label" for="halfday_end" id="halfday_end_label" hidden>Half-day</label>
                        <input class="form-check-input" type="radio" name="end_date_radio" id="halfday_end"
                            value="halfday" hidden disabled>
                    </div>
                </div>
                <div class="form-check-inline mt-3 mb-3">
                    <div class="form-check">
                        <label class="form-check-label" for="end_morning" id="end_morning_label" hidden>Morning</label>
                        <input class="form-check-input" type="radio" name="end_date_period" id="end_morning"
                            value="morning" checked="checked" hidden disabled>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label" for="end_afternoon" id="end_afternoon_label"
                            hidden>Afternoon</label>
                        <input class="form-check-input" type="radio" name="end_date_period" id="end_afternoon"
                            value="afternoon" hidden disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-check-inline">
                    <div id="duration" style="padding-left:0">
                        <!-- Display duration of days here -->
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="department">Justification (If any)</label>
                    <textarea class="form-control z-depth-1" id="department" rows="3"
                        placeholder="Please provide a reason for taking leave..." name="reason"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="file">Upload any supporting documents (PDF Only)</label>
                    <input type="file" name="file" accept="application/pdf">
                </div>
            </div>
        </div>
        <!-- Show public holidays -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <div id="days"></div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <button id="submit" name="submit" class="btn btn-success" type="submit"><i class="fa fa-check"></i>
                    Submit</button>
                <button id="submitAndApprove" name="submitAndApprove" class="btn btn-info" type="submit"><i
                        class="fa fa-thumbs-up"></i> Submit And Approve</button>
            </div>
        </div>
    </form>
</body>

<script>
    $.noConflict(); //prevents TypeError: $(...).datepicker is not a function
jQuery(document).ready(function ($) {
    //user search auto-complete
    var path = "{{ route('autocomplete') }}";
    $('input.typeahead').typeahead({
        source:  function (query, process) {
        return $.get(path, { query: query }, function (data) {
                return process(data);
            });
        }
    });

    $("#startdate_datepicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        daysOfWeekDisabled: [0,6],
        todayHighlight: true
    });

    $("#enddate_datepicker").datepicker({
        onSelect: function() {
            return $(this).trigger('change');
        },
        format: 'yyyy-mm-dd',
        autoclose: true,
        daysOfWeekDisabled: [0,6],
        todayHighlight: true
    });

    //display "morning" or "afternoon" options if half-day leave is selected
    $("#halfday_start").click(function() {
        showStartDatePeriod();
        getHolidays();
    });
    //hide options
    $("#fullday_start").click(function() {
        hideStartDatePeriod();
        getHolidays();
    });
    //display "morning" or "afternoon" options if half-day leave is selected
    $("#halfday_end").click(function() {
        showEndDatePeriod();
        getHolidays();
    });
    //hide options
    $("#fullday_end").click(function() {
        hideEndDatePeriod();
        getHolidays();
    });
    //check if leave request falls over public holidays
    $("#startdate_datepicker").on("change", function(){
        if(!$("#name").val()){
            alert("Input a user's name first!");
            $("#startdate_datepicker").val("");
            $("#name").focus();
            return;
        }
        processDates();
    });
    $("#enddate_datepicker").on("change", function(){
        processDates();
    });

    $("#name").on("change", function(){
        getUserCountry();
    });

    //get country of the user entered in the autocomplete searchbox
    function getUserCountry(){
        $.ajax({
            url: '/getcountry',
            type: "get",
            data: {
                name: $("#name").val()
            },
            success: function(response){
                $("#enddate_datepicker").val("");
                $("#enddate_datepicker").prop("disabled", true);
                hideEndDatePeriod();
                //change disabled days depending on user's region
                var countries = ["Egypt", "United Arab Emirates"];
                if(jQuery.inArray(response.country, countries) !== -1){
                    var disabledDays = [5,6]; //if user is in UAE or Egypt, disable Fridays and Saturdays
                    $("#startdate_datepicker").datepicker('setDaysOfWeekDisabled', disabledDays);
                    $("#enddate_datepicker").datepicker('setDaysOfWeekDisabled', disabledDays);
                    $("#startdate_datepicker").prop("disabled", false);
                    $("#enddate_datepicker").prop("disabled", false);
                    $("#fullday_start").prop("disabled", false);
                    $("#halfday_start").prop("disabled", false);
                }
                else{
                    var disabledDays = [0,6]; //else disable Saturdays and Sundays
                    $("#startdate_datepicker").datepicker('setDaysOfWeekDisabled', disabledDays);
                    $("#enddate_datepicker").datepicker('setDaysOfWeekDisabled', disabledDays);
                    $("#startdate_datepicker").prop("disabled", false);
                    $("#enddate_datepicker").prop("disabled", false);
                    $("#fullday_start").prop("disabled", false);
                    $("#halfday_start").prop("disabled", false);
                }
            },
                error: function(response){
                    console.error(response);
            }
        });
    }
    function processDates(){
        //check if start_date is empty
        if(!$("#startdate_datepicker").val()){
            alert("Select a start date first!");
            $("#enddate_datepicker").val("");
            $("#startdate_datepicker").focus();
            return;
        }
        //if dates aren't the same
        if($("#startdate_datepicker").val() !== $("#enddate_datepicker").val() && $("#enddate_datepicker").val() !== ""){
            $("#fullday_end_label").prop("hidden", false);
            $("#halfday_end_label").prop("hidden", false);
            $("#fullday_end").prop("hidden", false);
            $("#fullday_end").prop("disabled", false);
            $("#halfday_end").prop("hidden", false);
            $("#halfday_end").prop("disabled", false);
            if($("#halfday_end").is(":checked")){
                showEndDatePeriod();
            }
        }
        else{
            $("#fullday_end_label").prop("hidden", true);
            $("#halfday_end_label").prop("hidden", true);
            $("#fullday_end").prop("hidden", true);
            $("#fullday_end").prop("disabled", true);
            $("#halfday_end").prop("hidden", true);
            $("#halfday_end").prop("disabled", true);
            hideEndDatePeriod();
        }
        getHolidays();
    }
    function getHolidays(){
        $.ajax({
            url: '/checkHolidays',
            type: "get",
            data: {
                start_date: $("#startdate_datepicker").val(),
                end_date: $("#enddate_datepicker").val(),
                name: $("#name").val()
            },
            success: function(response){
                var days = response.days;
                $("#duration").empty();
                if(days > 0 && $("#enddate_datepicker").val() !== ""){
                    if($("#enddate_datepicker").val() < $("#startdate_datepicker").val()){
                        return $("#duration").append('<h3><span class="badge badge-dark">Invalid date range!</span</h3>');
                    }
                    if(days == 1){
                        if($('#halfday_start').is(':checked') || $("#halfday_end").is(':checked')){
                            days -= 0.5;
                        }
                    }
                    else{
                        if($('#halfday_start').is(':checked')){
                        days = days - 0.5;
                        }
                        if($("#halfday_end").is(':checked')){
                            days = days - 0.5;
                        }
                    }
                    $("#duration").append('<h3>Duration (days): <span class="badge badge-dark">'+days+'</span></h3>');
                }
                else if(days <= 0){
                    return $("#duration").append('<h3><span class="badge badge-dark mt-3">Invalid date range!</span</h3>');
                }
                $("#days").empty();
                if($.trim(response.holidayList)){   //only display if there is something to display
                    $("#days").append('<hr><label for="reason">Falls over '+response.country+' Public Holidays</label><br>');
                    $.each(response.holidayList,function(index,value){
                        $("#days").append('<h3><span class="badge badge-dark"> '+value+'</span></h3>');
                    });
                }
            },
            error: function(response){
                alert('Error'+response);
            }
        });
    }
    function showStartDatePeriod(){
        $("#start_morning_label").prop('hidden', false);
        $("#start_afternoon_label").prop('hidden', false);
        $("#start_morning").prop('hidden', false);
        $("#start_morning").prop('disabled', false);
        $("#start_afternoon").prop('hidden', false);
        $("#start_afternoon").prop('disabled', false);
    }
    function hideStartDatePeriod(){
        $("#start_morning_label").prop('hidden', true);
        $("#start_afternoon_label").prop('hidden', true);
        $("#start_morning").prop('hidden', true);
        $("#start_morning").prop('disabled', true);
        $("#start_afternoon").prop('hidden', true);
        $("#start_afternoon").prop('disabled', true);
    }
    function showEndDatePeriod() {
        $("#end_morning_label").prop('hidden', false);
        $("#end_afternoon_label").prop('hidden', false);
        $("#end_morning").prop('hidden', false);
        $("#end_morning").prop('disabled', false);
        $("#end_afternoon").prop('hidden', false);
        $("#end_afternoon").prop('disabled', false);
    }
    function hideEndDatePeriod(){
        $("#end_morning_label").prop('hidden', true);
        $("#end_afternoon_label").prop('hidden', true);
        $("#end_morning").prop('hidden', true);
        $("#end_morning").prop('disabled', true);
        $("#end_afternoon").prop('hidden', true);
        $("#end_afternoon").prop('disabled', true);
    }
});
</script>
@endsection
