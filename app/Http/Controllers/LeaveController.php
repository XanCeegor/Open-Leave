<?php

namespace App\Http\Controllers;

use App\Leave;
use App\User;
use Illuminate\Http\Request;
use DateTime;
use DatePeriod;
use DateInterval;
use Auth;
use App;
use Session;
use App\Notifications\LeaveRequestCreated;

use function Opis\Closure\unserialize;

class LeaveController extends Controller
{
    public function create()
    {
        //prevent HR account from accessing user leave request creation page (prevents the user from manually typing in the URL)
        if (Auth()->user()->username == "cld") {
            return back()->with('error', 'Please select "HR Control Panel → Leave Requests → Create New" to create a leave request for a user.');
        }
        return view('leave.create');
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

        $leavenr = 'LEAVE-' . strtoupper((substr(md5(rand()), 0, 7)));
        $user = Auth::user();

        $request->has('start_date_period') ? $start_date_period = $request->start_date_period : $start_date_period = null;
        $request->has('end_date_period') ? $end_date_period = $request->end_date_period : $end_date_period = null;

        $calcDays = $this->calcDays($request);
        $days = $calcDays['days'];
        $holidayList = $calcDays['holidayList'];

        if ($days == 1) { //if only one leave day is selected
            if ($request->start_date_radio == "halfday" || $request->end_date_radio == "halfday") {   //if either day is chosen as halfday, the leave request is halfday
                $days = $days - 0.5;
            }
        } else { //else deduct half-day depending on the option chosen
            if ($request->start_date_radio == "halfday") {
                $days = $days - 0.5;
            }
            if ($request->end_date_radio == "halfday") {
                $days = $days - 0.5;
            }
        }

        if ($days <= 0) {
            return back()->withError("Leave request duration cannot be zero or negative.");
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

            //adjust leave days depending on type of leave chosen
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
            }

            //create the leave request
            Leave::create(array_merge(
                [
                    'leavenr' => $leavenr, 'username' => Auth::user()->username, 'requested_by' => Auth::user()->name, 'status' => 'Pending', 'duration' => $days, 'start_date_period' => $start_date_period, 'end_date_period' => $end_date_period,
                    'file_path' => $path, 'file_name' => $filename, 'department' => $user->department, 'public_holidays' => serialize($holidayList)
                ],
                $request->except(['submit', '_token', 'file'])
            ));
        } catch (\Exception $e) {   //catch any exceptions
            return back()->withError("Operation failed! Error message:" . $e->getMessage());
        }

        //notify the leave department when a new request has been created
        User::where('username', env('LEAVE_DEPARTMENT_LDAP_USERNAME'))->first()->notify(new LeaveRequestCreated($leavenr));
        return redirect('/leave')->with('success', 'Leave request successfully created!');
    }


    public function show($leavenr)
    {
        $leavereqs = Leave::where('leavenr', $leavenr)->first();
        //if Request can't be found with the leavenr, then the ID was passed, so look for it using the ID
        //this is to counter the return view bug which doesn't refresh the navbar, so we redirect instead
        if (!$leavereqs) {
            try {
                $leavereqs = Leave::where('id', $leavenr)->first();
            } catch (\Exception $e) {   //catch any exceptions
                abort(404);
            }
        }
        if (!$leavereqs) {    //check if request exists else redirect
            abort(404);
        }
        $holidayList = unserialize($leavereqs->public_holidays);

        if (Auth()->user()->username !== 'cld') { //only HR can see other people's leave requests
            if (Auth()->user()->username !== $leavereqs->username) {  //if it's not HR, redirect to error page
                abort(403);
            }
        }
        return view('leave.show', compact('leavereqs', 'holidayList'));
    }

    private function calcDays(Request $request)
    {
        //if request has 'name', then it's an AJAX call from HR creating a new request, so let's get that user
        if ($request->has('name')) {
            $user = User::where('name', $request->name)->first();
        } else {
            $user = Auth::user();
        }

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
            '2020-12-25'    //Christmas Day
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
        switch ($user->country) {
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
            'holidayList' => $holidayList,
            'country' => $user->country
        ];
    }

    public function cancel($leavenr)
    {
        $leavereq = Leave::where('leavenr', $leavenr)->first();
        try {
            //restore leave days
            if (stripos($leavereq->type, 'Annual Leave') !== false) {
                $leavereq->user->days_taken -= $leavereq->duration;
                $leavereq->user->entitled_days += $leavereq->duration;
                $leavereq->user->save();
            } else if (stripos($leavereq->type, 'Sick Leave') !== false) {
                $leavereq->user->sick_days += $leavereq->duration;
                $leavereq->user->save();
            } else if (stripos($leavereq->type, 'Family Responsibility') !== false) {
                $leavereq->user->family_days += $leavereq->duration;
                $leavereq->user->save();
            } else if (stripos($leavereq->type, 'Paternity/Maternity Leave') !== false) {
                $leavereq->user->parental_leave -= $leavereq->duration;
                $leavereq->user->save();
            } else if (stripos($leavereq->type, 'Unpaid Leave') !== false) {
                $leavereq->user->unpaid_leave -= $leavereq->duration;
                $leavereq->user->save();
            }
            $leavereq->status = "Cancelled";
            $leavereq->save();
        } catch (\Exception $e) {   //catch any exceptions
            return back()->withError("Operation failed! Error message:" . $e->getMessage());
        }
        return redirect()->back()->with('success', 'Leave requests successfully cancelled.');
    }

    public function getFile($leavenr)
    {
        $leavereq = Leave::where('leavenr', $leavenr)->first();
        //only the user who the file belongs to or Admins can view the file
        if ($leavereq->username !== Auth::user()->username) {
            if (Session::get('access') < 2) {
                return back()->withErrors('You are not allowed to view this file.');
            } else {
                try {
                    return response()->file(storage_path() . $leavereq->file_path . $leavereq->file_name);
                } catch (\Exception $e) {
                    return back()->withErrors("Operation failed! Error message:" . $e->getMessage());
                }
            }
        } else {
            try {
                return response()->file(storage_path() . $leavereq->file_path . $leavereq->file_name);
            } catch (\Exception $e) {
                return back()->withErrors("Operation failed! Error message:" . $e->getMessage());
            }
        }
    }

    public function checkHolidays(Request $request)
    {
        $calcDays = $this->calcDays($request);
        return response()->json(['holidayList' => $calcDays['holidayList'], 'days' => $calcDays['days'], 'country' => $calcDays['country']]);
    }
}
