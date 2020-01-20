<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\LeaveRequestApproved;
use App\Notifications\LeaveRequestRejected;
use App\Leave;
use App\User;
use DateTime;
use DatePeriod;
use DateInterval;

class HRController extends Controller
{
    public function update(Request $request, $leavenr)
    {
        //check if correct dates submitted
        if ($request->end_date < $request->start_date) {
            return redirect()->back()->with('error', 'End date cannot be before start date.');
        }
        $leave = Leave::where('leavenr', $leavenr)->first();

        $request->has('start_date_period') ? $start_date_period = $request->start_date_period : $start_date_period = null;
        $request->has('end_date_period') ? $end_date_period = $request->end_date_period : $end_date_period = null;
        $request->has('end_date_radio') ? $end_date_radio = $request->end_date_radio : $end_date_radio = null;

        $calcDays = $this->calcDays($request, $leave->user);
        $days = $calcDays['days'];
        $holidayList = $calcDays['holidayList'];

        //if type of leave or dates are different than the original leave request, then we need to recalculate the days allocated
        if (($request->type !== $leave->type) || ($request->start_date !== $leave->start_date) || ($request->end_date !== $leave->end_date) || ($request->start_date_radio !== $leave->start_date_radio) || ($request->end_date_radio !== $leave->end_date_radio)) {
            //calculate total days from the Form
            $toAdd = 0; //amount of days to add back to user's entitled leave if request is rejected

            //restore leave days to employee depending on the type of leave before recalulating the new request
            if ($leave->type == "Annual Leave") {
                $leave->user->entitled_days += $leave->duration;
                $leave->user->days_taken -= $leave->duration;
                $leave->user->save();
            }
            if ($leave->type == "Sick Leave") {
                $leave->user->sick_days += $leave->duration;
                $leave->user->save();
            }
            if ($leave->type == "Family Responsibility Leave") {
                $leave->user->family_days += $leave->duration;
                $leave->user->save();
            }
            if ($leave->type == "Paternity/Maternity Leave") {
                $leave->user->parental_leave -= $leave->duration;
                $leave->user->save();
            }
            if ($leave->type == "Unpaid Leave") {
                $leave->user->unpaid_leave -= $leave->duration;
                $leave->user->save();
            }

            //check if half day has changed into full day and add 0.5 days
            if (($request->start_date_radio == "fullday") && ($request->start_date_radio !== $leave->start_date_radio)) {
                $toAdd = $toAdd + 0.5;
            }
            //check if half day has changed into full day and add 0.5 days
            if (($request->end_date_radio == "fullday") && ($request->end_date_radio !== $leave->end_date_radio)) {
                $toAdd = $toAdd + 0.5;
            }

            //check if halfday is selected and minus 0.5 days
            if ($days == 1) { //if only one leave day is selected
                if ($request->start_date_radio == "halfday" || $request->end_date_radio == "halfday") {   //only choose one half day
                    $days = $days - 0.5;
                }
                //check if full day was changed to halfday and subtract 0.5
                else
                    if (($request->start_date_radio == "fullday") && ($request->start_date_radio !== $leave->start_date_radio)) {
                    $toAdd = $toAdd - 0.5;
                }
                //check if full day was changed to halfday and subtract 0.5
                if (($request->end_date_radio == "fullday") && ($request->end_date_radio !== $leave->end_date_radio)) {
                    $toAdd = $toAdd - 0.5;
                }
            } else { //deduct 0.5 days for each halfday selected
                if ($request->start_date_radio == "halfday") {
                    $days = $days - 0.5;
                }
                if ($request->end_date_radio == "halfday") {
                    $days = $days - 0.5;
                }   //minus 0.5 days if the user switches from "Fullday" leave to "Halfday" leave
                if (($request->start_date_radio == "fullday") && ($request->start_date_radio !== $leave->start_date_radio)) {
                    $toAdd = $toAdd - 0.5;
                }
                if (($request->end_date_radio == "fullday") && ($request->end_date_radio !== $leave->end_date_radio)) {
                    $toAdd = $toAdd - 0.5;
                }
            }
            $days += $toAdd;
            //recalculate the new request days and deduct it from the user
            if (stripos($request->type, 'Annual Leave') !== false) {
                //update user's days_taken and entitled_days
                $leave->user->days_taken += $days;
                $leave->user->entitled_days -= $days;
                $leave->user->save();
            } else if (stripos($request->type, 'Sick Leave') !== false) {
                //update user's sick_days
                $leave->user->sick_days -= $days;
                $leave->user->save();
            } else if (stripos($request->type, 'Family Responsibility') !== false) {
                //update leave days
                $leave->user->family_days -= $days;
                $leave->user->save();
            } else if (stripos($request->type, 'Paternity/Maternity Leave') !== false) {
                $leave->user->parental_leave += $days;
                $leave->user->save();
            } else if (stripos($request->type, 'Unpaid Leave') !== false) {
                $leave->user->unpaid_leave += $days;
                $leave->user->save();
            }
        }

        try {
            $leave->update(array_merge(
                ['end_date_radio' => $end_date_radio, 'duration' => $days, 'public_holidays' => serialize($holidayList), 'start_date_period' => $start_date_period, 'end_date_period' => $end_date_period],
                $request->except(['submit', '_token'])
            ));
        } catch (\Exception $e) {   //catch any exceptions
            return back()->withError("Operation failed! Error message:" . $e->getMessage());
        }
        return redirect()->back()->with('success', "Leave request successfully updated!");
    }


    public function show($leavenr)
    {
        $leavereqs = Leave::where('leavenr', $leavenr)->first();

        //if Request can't be found with the leavenr, then the ID was passed, so look for it using the ID
        if (!$leavereqs) {
            try {
                $leavereqs = Leave::where('id', $leavenr)->first();
            } catch (\Exception $e) {   //catch any exceptions
                abort(404);
            }
        }
        //return 404 if it doesn't exist
        if (!$leavereqs) {
            abort(404);
        }
        //if user account is disabled or cannot be found
        if (!$leavereqs->user) {
            return redirect()->back()->with('error', 'Cannot view request. User account on leave request does not match the account from AD!');
        }
        $holidayList = unserialize($leavereqs->public_holidays);
        if ($leavereqs->seen == false) {
            $leavereqs->seen = true;
            $leavereqs->save();
        }
        return view('hr.show', compact('leavereqs', 'holidayList'));
    }


    public function edit($leavenr)
    {
        $leavereqs = Leave::where('leavenr', $leavenr)->first();
        if (!$leavereqs) {    //handle non-existing request URLs
            abort(404);
        }
        //if user account is disabled or cannot be found
        if ($leavereqs->user == null) {
            return redirect()->back()->with('error', 'Cannot edit request. User account is disabled.');
        }
        if ($leavereqs->status !== "Pending") {   //prevent a request from being changed once it has been approved or rejected
            abort(403);
        }
        return view('hr.edit', compact('leavereqs'));
    }


    public function create()
    {
        return view('hr.create');
    }


    public function store(Request $request)
    {
        //check if correct dates submitted
        if ($request->end_date < $request->start_date) {
            return redirect()->back()->with('error', 'End date cannot be before start date.');
        }

        $this->validate($request, [
            'file.*' => 'mimes:pdf|size:2000'
        ]);

        if ($request->has('submit')) {
            $status = "Pending";
        }
        if ($request->has("submitAndApprove")) {
            $status = "Approved";
        }
        $user = User::where('name', $request->name)->first();
        $leavenr = 'LEAVE-' . strtoupper((substr(md5(rand()), 0, 7)));

        $request->has('start_date_period') ? $start_date_period = $request->start_date_period : $start_date_period = null;
        $request->has('end_date_period') ? $end_date_period = $request->end_date_period : $end_date_period = null;

        $calcDays = $this->calcDays($request, $user);
        $days = $calcDays['days'];
        $holidayList = $calcDays['holidayList'];

        if ($days == 1) { //if only one leave day is selected, deduct only half a day regardless of which halfday is chosen
            if ($request->start_date_radio == "halfday" || $request->end_date_radio == "halfday") {
                $days = $days - 0.5;
            }
        } else {
            if ($request->start_date_radio == "halfday") {
                $days = $days - 0.5;
            }
            if ($request->end_date_radio == "halfday") {
                $days = $days - 0.5;
            }
        }

        try {
            if ($request->hasfile('file')) {
                $filename = $leavenr . '_' . $request->file('file')->getClientOriginalName();
                $path = '/uploads/' . $user->name . '/';
                $request->file->move(storage_path() . $path, $filename);
            } else {
                $filename = null;
                $path = null;
            }

            //update user's leave days depending on leave type chosen
            if (stripos($request->type, 'Annual Leave') !== false) {
                $user->days_taken += $days;
                $user->entitled_days -= $days;
                $user->save();
            } else if (stripos($request->type, 'Sick Leave') !== false) {
                $user->sick_days -= $days;
                $user->save();
            } else if (stripos($request->type, 'Family Responsibility Leave') !== false) {
                $user->family_days -= $days;
                $user->save();
            } else if (stripos($request->type, 'Paternity/Maternity Leave') !== false) {
                $user->parental_leave += $days;
                $user->save();
            } else if (stripos($request->type, 'Unpaid Leave') !== false) {
                $user->unpaid_leave += $days;
                $user->save();
            }

            //create the leave request
            Leave::create(array_merge(
                [
                    'leavenr' => $leavenr, 'username' => $user->username, 'requested_by' => $user->name, 'status' => $status, 'duration' => $days, 'public_holidays' => serialize($holidayList),
                    'file_path' => $path, 'file_name' => $filename, 'start_date_period' => $start_date_period, 'end_date_period' => $end_date_period
                ],
                $request->except(['submit', '_token', 'name', 'submitAndApprove', 'file'])
            ));
        } catch (\Exception $e) {   //catch any exceptions
            return back()->withError("Operation failed! Error message:" . $e->getMessage());
        }
        return redirect('/hr/requests')->with('success', 'Leave request successfully created!');
    }


    //handle approval of leave
    public function approve($leavenr)
    {
        $leave = Leave::where('leavenr', $leavenr)->first();
        if (!$leave) {
            abort(404);
        }
        $leave->status = "Approved";
        $leave->save();
        $leave->user->notify(new LeaveRequestApproved($leavenr));    //notify the user of the status change
        return redirect('/hr/requests')->with('success', "User leave request has been approved.");
    }


    //handle rejection of leave
    public function reject(Request $request, $leavenr)
    {
        $leave = Leave::where('leavenr', $leavenr)->first();

        if (!$leave) {
            abort(404);
        }
        try {
            //restore leave days
            if (stripos($request->type, 'Annual Leave') !== false) {
                $leave->user->days_taken -= $leave->user->days_taken - $leave->duration;
                $leave->user->entitled_days += $leave->duration;
                $leave->user->save();
            } else if (stripos($request->type, 'Sick Leave') !== false) {
                $leave->user->sick_days += $leave->duration;
                $leave->user->save();
            } else if (stripos($request->type, 'Family Responsibility') !== false) {
                $leave->user->family_days += $leave->duration;
                $leave->user->save();
            } else if (stripos($request->type, 'Paternity/Maternity Leave') !== false) {
                $leave->user->parental_leave -= $leave->duration;
                $leave->user->save();
            } else if (stripos($request->type, 'Unpaid Leave') !== false) {
                $leave->user->unpaid_leave -= $leave->duration;
                $leave->user->save();
            }

            $leave->status = "Rejected";
            $leave->comment = $request->reject_reason;
            $leave->save();
        } catch (\Exception $e) {   //catch any exceptions
            return back()->withError("Operation failed! Error message:" . $e->getMessage());
        }
        $leave->user->notify(new LeaveRequestRejected($leavenr));    //notify the user of the status change
        return redirect()->back()->with('success', "User leave request has been rejected.");
    }


    private function calcDays(Request $request, User $user)
    {
        //get duration of leave
        $start = new DateTime($request->start_date);
        $end = new DateTime($request->end_date);
        // otherwise the  end date is excluded (bug?)
        $end->modify('+1 day');
        $interval = $end->diff($start);
        // total days
        $days = $interval->days;
        // create an iterateable period of date (P1D equates to 1 day)
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        $SA = [ //SA national holidays
            '2019-01-01',   //New years Day
            '2019-03-21',   //Human Rights Day
            '2019-04-19',   //Good Friday
            '2019-04-22',   //Family Day / Easter Monday
            '2019-05-01',   //Worker's Day
            '2019-06-17',   //Youth Day Public Holiday Observed
            '2019-08-09',   //National Women's Day
            '2019-09-24',   //Heritage Day
            '2019-12-16',   //Day of Reconciliation
            '2019-12-25',   //Christmas Day
            '2019-12-26',    //Day of Goodwill
            '2020-01-01',   //New years Day
            '2020-04-10',   //Good Friday
            '2020-04-13',   //Family Day
            '2020-04-27',   //Freedom Day
            '2020-05-01',   //Worker's Day
            '2020-06-16',   //Youth Day
            '2020-08-10',   //National Women's Day Observed
            '2020-09-24',   //Heritage Day
            '2020-12-16',   //Day of Reconciliation
            '2020-12-25'   //Christmas Day
        ];
        $USA = [ //USA national holidays
            '2019-01-01',   //New years Day
            '2019-01-21',   //Martin Luther King Jr Day
            '2019-02-18',   //President's Day
            '2019-05-27',   //Memorial Day
            '2019-07-04',   //Independence Day
            '2019-09-02',   //Labour Day
            '2019-10-14',   //Columbus Day
            '2019-11-11',   //Veterans Day
            '2019-11-28',   //Thanksgiving Day
            '2019-12-25',   //Christmas Day
            '2020-01-01',   //New years Day
            '2020-01-20',   //Martin Luther King Jr Day
            '2020-02-17',   //President's Day
            '2020-05-25',   //Memorial Day
            '2020-07-03',   //Independence Day (Observed)
            '2020-09-07',   //Labour Day
            '2020-10-12',   //Columbus Day
            '2020-11-11',   //Veterans Day
            '2020-11-26',   //Thanksgiving Day
            '2020-12-25'    //Christmas Day
        ];
        $Nigeria = [ //Nigerian national holidays
            '2019-01-01',   //New years Day
            '2019-04-19',   //Good Friday
            '2019-04-22',   //Easter Monday
            '2019-05-01',   //Labour Day
            '2019-06-04',   //Id el Fitri (End of Ramadan)
            '2019-06-05',   //Id el Fitri Holiday
            '2019-06-12',   //Democracy Day
            '2019-08-12',   //Id el Kabir Holiday
            '2019-10-01',   //National Day
            '2019-12-25',   //Christmas Day
            '2019-12-26',   //Boxing Day
            '2020-01-01',   //New Years Day
            '2020-04-10',   //Good Friday
            '2020-04-13',   //Easter Monday
            '2020-05-01',   //Worker's Day
            '2020-05-24',   //Eid-el-fitri Sallah
            '2020-06-12',   //Democracy Day
            '2020-07-31',   //Id el Kabir
            '2020-10-01',   //Independence Day
            '2020-12-25',   //Christmas Day
            '2020-12-26'    //Day of goodwill
        ];
        $Australia = [ //Australian national holidays
            '2019-01-01',   //New years Day
            '2019-01-26',   //Australia Day
            '2019-03-11',   //Canberra Day
            '2019-04-19',   //Good Friday
            '2019-04-22',   //Easter Monday
            '2019-04-25',   //Anzac Day
            '2019-05-27',   //Reconciliation Day
            '2019-06-10',   //Queen's Birthday
            '2019-10-07',   //Labour Day
            '2019-12-25',   //Christmas Day
            '2019-12-26',   //Boxing Day
            '2020-01-01',   //New years day
            '2020-01-27',   //Australia Day
            '2020-04-10',   //Good Friday
            '2020-04-13',   //Easter Monday
            '2020-04-27',   //Anzac Day
            '2020-12-25'    //Christmas Day
        ];
        $Angola = [ //Angolan national holidays
            '2019-01-01',   //New years Day
            '2019-02-04',   //Liberation Day
            '2019-03-05',   //Carnival
            '2019-03-08',   //International Women's Day
            '2019-04-04',   //Angolan Peace
            '2019-04-19',   //Good Friday
            '2019-05-01',   //Labour Day
            '2019-09-17',   //National Heroes' Day
            '2019-11-02',   //All Soul's Day
            '2019-11-11',   //Independence Day
            '2019-12-25',   //Christmas Day
            '2020-01-01',   //New years day
            '2020-02-04',   //Liberation Day
            '2020-02-25',   //Carnival Tuesday
            '2020-03-09',   //Women's Day
            '2020-04-10',   //Good Friday
            '2020-05-01',   //Labour Day
            '2020-09-17',   //National Heroes Day
            '2020-11-02',   //All Soul's Day
            '2020-11-11',   //Independence Day
            '2020-12-25'    //Christmas Day
        ];
        $Zambia = [ //Zambian national holidays
            '2019-01-01',   //New years Day
            '2019-03-08',   //International Women's Day
            '2019-03-12',   //Youth Day
            '2019-04-19',   //Good Friday
            '2019-04-22',   //Easter Monday
            '2019-05-01',   //Labour Day
            '2019-07-01',   //Heroes' Day
            '2019-07-02',   //Unity Day
            '2019-08-05',   //Farmer's Day
            '2019-10-18',   //National Day of Prayer
            '2019-10-24',   //Independence Day
            '2019-12-25',   //Christmas Day
            '2020-01-01',   //New Years Day
            '2020-03-09',   //Women's Day
            '2020-03-12',   //Youth Day
            '2020-04-10',   //Good Friday
            '2020-04-13',   //Good Monday
            '2020-05-01',   //Labour Day
            '2020-05-25',   //African Unity Day
            '2020-07-06',   //Heroes' Day
            '2020-07-07',   //Unity Day
            '2020-08-03',   //Farmer's Day
            '2020-10-19',   //National Prayer Day
            '2020-12-25'    //Christmas Day
        ];
        $Mozambique = [ //Moz national holidays
            '2019-01-01',   //New years Day
            '2019-02-04',   //Mozambique's Heroes' Day Holiday
            '2019-04-08',   //Mozambique's Women's Day Holiday
            '2019-05-01',   //Worker's Day
            '2019-06-25',   //Independence Day
            '2019-09-25',   //Armed Forces Day
            '2019-10-04',   //Peace and national Reconciliation day
            '2019-12-25',   //Christmas Day
            '2020-01-01',   //New years Day
            '2020-02-03',   //Mozambique's Heroes' Day Holiday
            '2020-04-07',   //Mozambique's Women's Day Holiday
            '2020-05-01',   //Worker's Day
            '2020-06-25',   //Independence Day
            '2020-09-07',   //Victory Day
            '2020-09-25',   //Armed Forces Day
            '2020-10-05',   //Peace and national Reconciliation day
            '2020-12-25'   //Christmas Day
        ];
        $UK = [ //UK national holidays
            '2019-01-01',   //New years Day
            '2019-04-19',   //Good Friday
            '2019-04-22',   //Easter Monday
            '2019-05-06',   //May day Bank Holiday
            '2019-05-27',   //Spring Bank Holiday
            '2019-08-26',   //Summer Bank Holiday
            '2019-12-25',   //Christmas Day
            '2019-12-26',   //Boxing Day
            '2020-01-01',   //New years Day
            '2020-04-10',   //Good Friday
            '2020-04-13',   //Easter Monday
            '2020-05-08',   //May day Bank Holiday
            '2020-05-25',   //Spring Bank Holiday
            '2020-08-31',   //Summer Bank Holiday
            '2020-12-25',   //Christmas Day
            '2020-12-28'    //Boxing Day
        ];
        $UAE = [ //UAE National holidays
            '2019-01-01',   //New years Day
            '2019-06-03',   //Eid al Fitr Holiday
            '2019-06-04',   //Eid al Fitr (End of Ramadan)
            '2019-06-05',   //Eid al Fitr Holiday
            '2019-06-06',   //Eid al Fitr Holiday
            '2019-08-11',   //Eid al Adha Holiday
            '2019-08-12',   //Eid al Adha Holiday
            '2019-08-13',   //Eud al Adha Holiday
            '2019-09-01',   //Hijri New Year's Day
            '2019-12-01',   //Commemoration Day
            '2019-12-02',   //National Day
            '2019-12-03',   //National Day Holiday
            '2020-01-01',   //New years Day
            '2020-05-24',   //Eid al Fitr Holiday
            '2020-05-25',   //Eid al Fitr (End of Ramadan)
            '2020-05-26',   //Eid al Fitr Holiday
            '2020-07-30',   //Afrah Day
            '2020-08-02',   //Eid Al Adha Holiday
            '2020-08-23',   //Hijri New Year's Day
            '2020-12-01',   //Commemoration Day
            '2020-12-02',   //National Day
            '2020-12-03'   //National Day Holiday
        ];
        $Egypt = [  //Egyptian public holidays
            '2019-01-07',   //Coptic Christmas
            '2019-01-24',   //Revolution Day January 25
            '2019-04-25',   //Sinai Liberation Day
            '2019-04-28',   //Coptic Easter
            '2019-04-29',   //Sham El Nessim
            '2019-05-01',   //Worker's day
            '2019-06-05',   //End of ramadan
            '2019-06-06',   //End of ramadan
            '2019-06-30',   //Revolution Day June 30
            '2019-07-23',   //Revolution Day July 23
            '2019-08-12',   //Eid Al Adha
            '2019-08-13',   //Eid Al Adha Holiday
            '2019-08-14',   //Eid Al Adha Holiday
            '2019-09-01',   //El Hijra
            '2019-10-06',   //Armed Forces Day
            '2019-11-10',   //Al-Mouled Al-Nabawy
            '2020-01-07',   //Coptic Christmas
            '2020-04-19',   //Coptic Easter Sunday
            '2020-04-20',   //Sham El Nessim
            '2020-05-24',   //End of ramadan
            '2020-05-25',   //End of ramadan
            '2020-05-26',   //End of Ramadan
            '2020-06-30',   //Revolution Day June 30
            '2020-07-23',   //Revolution Day Jule 23
            '2020-08-02',   //Eid Al Adha Holiday
            '2020-08-20',   //El Hijra
            '2020-10-06',   //Armed Forces Day
            '2020-10-29'   //Al-Mouled Al-Nabawy
        ];
        $Peru = [
            '2019-01-01',   //New years day
            '2019-04-18',   //Maundy Thursday
            '2019-04-19',   //Good Friday
            '2019-05-01',   //Labour Day
            '2019-06-29',   //Saint Peter and Saint Paul day
            '2019-07-29',   //Independence Day
            '2019-08-30',   //Santa Rose de Lima
            '2019-10-08',   //Battle of Angamos
            '2019-11-01',   //All Saints day
            '2019-12-08',   //Immaculate Conception Day
            '2019-12-25',   //Christmas Day
            '2020-01-01',   //New years day
            '2020-04-09',   //Maundy Thursday
            '2020-04-10',   //Good Friday
            '2020-05-01',   //Labour Day
            '2020-06-29',   //Saint Peter and Saint Paul day
            '2020-07-27',   //Independence Day Holiday (Bridge Day)
            '2020-07-28',   //Independence Day
            '2020-10-08',   //Battle of Angamos
            '2020-12-08',   //Immaculate Conception Day
            '2020-12-25'   //Christmas Day
        ];

        //set relevant holidays for user's region
        $holidays = array();
        switch (Auth()->user()->country) {
            case "South Africa":
                $holidays = $SA;
                break;
            case "United States":
                $holidays = $USA;
                break;
            case "Nigeria":
                $holidays = $Nigeria;
                break;
            case "Australia":
                $holidays = $Australia;
                break;
            case "Angola":
                $holidays = $Angola;
                break;
            case "Zambia":
                $holidays = $Zambia;
                break;
            case "Mozambique":
                $holidays = $Mozambique;
                break;
            case "United Kingdom":
                $holidays = $UK;
                break;
            case "United Arab Emirates":
                $holidays = $UAE;
                break;
            case "Egypt":
                $holidays = $Egypt;
                break;
            case "Peru":
                $holidays = $Peru;
                break;
        }

        //array to store the holiday dates that the leave request falls over
        $holidayList = array();
        foreach ($period as $dt) {
            $curr = $dt->format('D');
            if ($user->country == "Egypt" || $user->country == "United Arab Emirates") {    //weekends fall on Friday and Saturday
                //substract if Saturday or Sunday
                if ($curr == 'Fri' || $curr == 'Sat') {
                    $days--;
                }
                //handle holidays
                else if (in_array($dt->format('Y-m-d'), $holidays)) {
                    $days--;
                    $holidayList[] = $dt->format('Y-m-d');
                }
            } else {
                //substract if Saturday or Sunday
                if ($curr == 'Sat' || $curr == 'Sun') {
                    $days--;
                }
                //handle holidays
                else if (in_array($dt->format('Y-m-d'), $holidays)) {
                    $days--;
                    $holidayList[] = $dt->format('Y-m-d');
                }
            }
        }
        return [
            'days' => $days,
            'holidayList' => $holidayList
        ];
    }

    public function UserSearchAutoComplete(Request $request)
    {
        $data = User::select("name")
            ->where("name", "LIKE", "%{$request->input('query')}%")
            ->get();
        return response()->json($data);
    }

    public function getUserCountry(Request $request)
    {
        $user = User::where('name', $request->name)->first();
        return response()->json(['country' => $user->country]);
    }
}
