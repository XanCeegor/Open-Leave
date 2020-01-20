@extends('layouts.app')

@section('content')

<head>
    <!-- Include Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js">
    </script>
    <title>
        Edit User Profile
    </title>
</head>

<body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="/hr/requests">HR</a></li>
            <li class="breadcrumb-item"><a href="/hr/users">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit User Profile</li>
        </ol>
    </nav>
    <hr><br>
    <h3>Edit User Profile</h3><br>

    <form action="{{ route('hr.users.update', ['username' => $user->username]) }}"
        style="border:2px solid #343a40; border-radius:15px; padding:20px" method="post" autocomplete="off">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="startdate_datepicker">Date of Employment</label>
                    <div class="start_date input-group mb-4">
                        <input class="form-control start_date" type="text" value="{{ $user->employment_date }}"
                            placeholder="Date of Employment" id="startdate_datepicker" name="employment_date">
                        <div class="input-group-append">
                            <span class="fa fa-calendar input-group-text start_date_calendar"
                                aria-hidden="true "></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department" readonly
                        value="{{ $user->department }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-3">
                <div class="form-group">
                    <label for="entitled_days">Accrued Leave (Read-only)</label>
                    <input type="text" class="form-control" id="entitled_days" name="entitled_days"
                        value="{{ $user->entitled_days }}" readonly>
                </div>
            </div>
            <div class="col-sm-12 col-lg-3">
                <div class="form-group">
                    <label for="days_taken">Accrued Leave Days Taken</label>
                    <input type="text" class="form-control" id="days_taken" name="days_taken"
                        value="{{ $user->days_taken }}" readonly>
                </div>
            </div>
            <div class="col-sm-12 col-lg-3">
                <div class="form-group">
                    <label for="priority">Can Carry Over Leave?</label><br>
                    <div class="form-check-inline">
                        <div class="form-check">
                            <label class="form-check-label" for="carry_yes">Yes</label>
                            @if($user->can_carry_over == true)
                            <input class="form-check-input" type="radio" name="can_carry_over" id="carry_yes"
                                value="True" checked="checked">
                            @else
                            <input class="form-check-input" type="radio" name="can_carry_over" id="carry_yes"
                                value="True">
                            @endif
                        </div>
                        <div class="form-check">
                            <label class="form-check-label" for="carry_no">No</label>
                            @if($user->can_carry_over == false)
                            <input class="form-check-input" type="radio" name="can_carry_over" id="carry_no"
                                value="False" checked="checked">
                            @else
                            <input class="form-check-input" type="radio" name="can_carry_over" id="carry_no"
                                value="False">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-3">
                <div class="form-group">
                    <label for="priority">User Has 20 Leave Days?</label><br>
                    <div class="form-check-inline">
                        <div class="form-check">
                            <label class="form-check-label" for="extra_yes">Yes</label>
                            @if($user->extra_days == true)
                            <input class="form-check-input" type="radio" name="extra_days" id="extra_yes" value="True"
                                checked="checked">
                            @else
                            <input class="form-check-input" type="radio" name="extra_days" id="extra_yes" value="True">
                            @endif
                        </div>
                        <div class="form-check">
                            <label class="form-check-label" for="extra_no">No</label>
                            @if($user->extra_days == false)
                            <input class="form-check-input" type="radio" name="extra_days" id="extra_no" value="False"
                                checked="checked">
                            @else
                            <input class="form-check-input" type="radio" name="extra_days" id="extra_no" value="False">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12  col-lg-3">
                <div class="form-group">
                    <label for="sickleave">Sick Leave Days</label>
                    <input type="text" class="form-control" id="sickleave" name="sick_days"
                        value="{{ $user->sick_days }}" required>
                </div>
            </div>
            <div class="col-sm-12  col-lg-3">
                <div class="form-group">
                    <label for="familyleave">Family Responsibility Leave Days</label>
                    <input type="text" class="form-control" id="familyleave" name="family_days"
                        value="{{ $user->family_days }}" required>
                </div>
            </div>
            <div class="col-sm-12  col-lg-3">
                <div class="form-group">
                    <label for="parentalleave">Parental Leave Days</label>
                    <input type="text" class="form-control" id="parentalleave" name="parental_leave"
                        value="{{ $user->parental_leave }}" required>
                </div>
            </div>
            <div class="col-sm-12  col-lg-3">
                <div class="form-group">
                    <label for="unpaidleave">Unpaid Leave Days Taken</label>
                    <input type="text" class="form-control" id="unpaidleave" name="unpaid_leave"
                        value="{{ $user->unpaid_leave }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="end_cycle">Sick days cycle end year</label>
                    <input type="text" class="form-control" id="end_cycle" name="end_cycle"
                        value="{{ $user->end_cycle }}" required>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="country" name="country" value="{{ $user->country }}"
                        readonly required>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="carriedOverLeave">Carried Over Leave Days</label>
                    <input type="text" class="form-control" id="carriedOverLeave" name="carried_over_leave_days"
                        value="{{ $user->carried_over_leave_days }}" @if(!$user->can_carry_over) disabled @endif>
                </div>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <button id="submit" name="submit" class="btn btn-secondary" type="submit"><i class="fa fa-save"></i>
                    Save</button>
                <a href="{{ route('hr.users.index') }}"><button type="button" id="cancelEdit" name="cancelEdit"
                        class="btn btn-danger"><i class="fa fa-times"></i> Cancel Edit</button></a>
                <br>
                <small>*Some changes take effect the next time the user logs in</small>
            </div>
        </div>
    </form>
</body>

<script>
    $("#startdate_datepicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        daysOfWeekDisabled: [0,6],
        todayHighlight: true
    });

    $("#carry_yes").on('click', function(){
        $("#carriedOverLeave").prop('disabled', false);
    });
    $("#carry_no").on('click', function(){
        $("#carriedOverLeave").prop('disabled', true);
    });
</script>
@endsection
