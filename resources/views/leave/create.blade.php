@extends('layouts.app')
@section('content')

<head>
    <!-- Include Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js">
    </script>
    <title>Create Leave Request</title>
</head>

<body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="/leave">My Leave Requests</a></li>
            <li class="breadcrumb-item active" aria-current="page">New Leave Request</li>
        </ol>
    </nav>
    <hr><br>

    <h3>Create a new leave request</h3><br>
    <form action="{{ route('leave.store') }}" enctype="multipart/form-data" method="post" id="reqForm"
        style="border:2px solid #343a40; border-radius:15px; padding:20px" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <label for="startdate_datepicker">Start Date</label>
                <div class="start_date input-group mb-4">
                    <input class="form-control start_date" required type="text" placeholder="Start date"
                        id="startdate_datepicker" name="start_date">
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text start_date_calendar" aria-hidden="true "></span>
                    </div>
                </div>
                <div class="form-check-inline">
                    <div class="form-check">
                        <label class="form-check-label" for="fullday_start">Full-day</label>
                        <input class="form-check-input" type="radio" name="start_date_radio" id="fullday_start"
                            value="fullday" checked="checked">
                    </div>
                    <div class="form-check">
                        <label class="form-check-label" for="halfday_start">Half-day</label>
                        <input class="form-check-input" type="radio" name="start_date_radio" id="halfday_start"
                            value="halfday">
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
                        id="enddate_datepicker" name="end_date">
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
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="type" name="type_label">Type of leave</label>
                    <select class="form-control" id="type" name="type">
                        <option>Annual Leave</option>
                        <option>Sick Leave</option>
                        @if(Auth::user()->country != "United Kingdom" &&
                        Auth::user()->country != "Egypt" &&
                        Auth::user()->country != "United States")
                        <option>Family Responsibility Leave</option>
                        @endif
                    </select>
                </div>
                <div class="form-check-inline mt-3 mb-3">
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
                    <label for="reason">Justification (If any)</label>
                    <textarea class="form-control z-depth-1" id="reason" rows="3"
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
            <div class="col-sm-12 col-lg-3">
                Days Remaining:
            </div>
            <div class="col-sm-12 col-lg-3">
                Annual Leave <span class="badge badge-pill badge-dark">{{ Auth::user()->entitled_days }}</span>
            </div>
            <div class="col-sm-12 col-lg-3">
                Sick Leave <span class="badge badge-pill badge-dark">{{ Auth::user()->sick_days }}</span>
            </div>
            <div class="col-sm-12 col-lg-3">
                Family Responsibility Leave <span
                    class="badge badge-pill badge-dark">{{ Auth::user()->family_days }}</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <button id="submit" name="submit" class="btn btn-success" type="submit"><i class="fa fa-check"></i>
                    Submit</button>
            </div>
        </div>
    </form>


</body>

<script>
    //change disabled days depending on region
    var countries = ["Egypt", "United Arab Emirates"];
    if(jQuery.inArray("{{ Auth::user()->country }}", countries) !== -1){
        var disabledDays = [5,6]; //if user is in UAE or Egypt, disable Fridays and Saturdays
    }
    else{
        var disabledDays = [0,6]; //else disable Saturdays and Sundays
    }

    $("#startdate_datepicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        daysOfWeekDisabled: disabledDays,
        todayHighlight: true
    });

    $("#enddate_datepicker").datepicker({
        onSelect: function() {
            return $(this).trigger('change');
        },
        format: 'yyyy-mm-dd',
        autoclose: true,
        daysOfWeekDisabled: disabledDays,
        todayHighlight: true
    });

    //display "morning" or "afternoon" options if half-day leave is selected
    $("#halfday_start").click(function() {
        showStartDatePeriod();
        getHolidaysAndDuration();
    });
    //hide options
    $("#fullday_start").click(function() {
        hideStartDatePeriod();
        getHolidaysAndDuration();
    });
    //display "morning" or "afternoon" options if half-day leave is selected
    $("#halfday_end").click(function() {
        showEndDatePeriod();
        getHolidaysAndDuration();
    });
    //hide options
    $("#fullday_end").click(function() {
        hideEndDatePeriod();
        getHolidaysAndDuration();
    });
    //check if leave request falls over public holidays
    $("#startdate_datepicker").on("change", function(){
        processDates();
    });
    $("#enddate_datepicker").on("change", function(){
        processDates();
    });

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
        else{ //else if dates are the same
            $("#fullday_end_label").prop("hidden", true);
            $("#halfday_end_label").prop("hidden", true);
            $("#fullday_end").prop("hidden", true);
            $("#fullday_end").prop("disabled", true);
            $("#halfday_end").prop("hidden", true);
            $("#halfday_end").prop("disabled", true);
            hideEndDatePeriod();
        }
        getHolidaysAndDuration();   //calculate new date range duration
    }
    function getHolidaysAndDuration(){
        $.ajax({
            url: '/checkHolidays',
            type: "get",
            data: {
                start_date:$("#startdate_datepicker").val(),
                end_date:$("#enddate_datepicker").val()
            },
            success: function(response){ //what to do if we succeed
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
                        days -= 0.5;
                        }
                        if($("#halfday_end").is(':checked')){
                            days -= 0.5;
                        }
                    }
                    $("#duration").append('<h3>Duration (days): <span class="badge badge-dark">'+days+'</span></h3>');
                }
                else if(days <= 0){
                    $("#duration").append('<h3><span class="badge badge-dark">Duration can\'t be 0 or negative!</span</h3>');
                }
                $("#days").empty();
                if($.trim(response.holidayList)){   //only display if there is something to display
                    $("#days").append('<hr><label>Falls over '+response.country+' Public Holidays</label><br>');
                    $.each(response.holidayList, function(index, value){
                        $("#days").append('<h3><span class="badge badge-dark"> '+value+'</span></h3>');
                    });
                }
            },
            error: function(response){
                alert('Error ' + response);
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
</script>
@endsection
