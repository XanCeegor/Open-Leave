<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use App\Leave;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    // if not added in a previous step
    public function username()
    {
        return config('ldap_auth.usernames.eloquent');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string|regex:/^\w+$/',
            'password' => 'required|string',
        ]);
    }

    protected function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = $request->only($this->username(), 'password');
        $username = $credentials[$this->username()];
        $password = $credentials['password'];
        $userdn = $username . env('LDAP_DOMAIN');
        $user = User::where($this->username(), $username)->first();
        try {
            if (Adldap::auth()->attempt($userdn, $password)) {
                // the user exists in the LDAP server, with the provided password
                if (!$user) {
                    // the user doesn't exist in the local database, so we have to create one
                    $user = new User();
                    $user->username = $username;
                    $user->password = '';
                }
                //check if user has access rights
                if ($this->hasRights($username) == -1) {
                    return back()->with('error', 'Insufficient access rights');
                }
                Session::put('access', $this->hasRights($username));    //store user access level in session
                Session::put('login', 1);
                //get user department from AD
                $user->department = Adldap::search()->where("sAMAccountName", '=', $username)->first()->department[0];
                //get user region from AD
                $user->country = Adldap::search()->where("sAMAccountName", '=', $username)->first()->co[0];
                //get user's date of employment - Used to check when the acrued leave calculation should begin from.
                //Starts from January if employment year !== current year, else starts from the month the user was employed
                if ($user->employment_date == null) {
                    //user employment date doesn't exist, so get it from the date the AD account was created at
                    $user->employment_date = Carbon::createFromFormat('YmdHis.0Z', Adldap::search()->where("sAMAccountName", '=', $username)->first()->whencreated[0])->format("Y-m-d");   //parse the date from AD
                }
                //check if user has 15 or 20 leave days | 0 = 15, 1 = 20
                //we store this value in a free text field in the AD account
                if (Adldap::search()->where("sAMAccountName", '=', $username)->first()->physicaldeliveryofficename[0] == null) {
                    $user->extra_days = 0;
                } else {
                    $user->extra_days = 1;
                }
                //get user's manager from AD
                //$user->manager = Adldap::search()->where("sAMAccountName", '=', $username)->first()->manager[0];

                //calculate the user's leave
                $this->calcLeave($user);
                $this->guard()->login($user, false);
                return redirect()->intended('dashboard');
            }
        } catch (\Exception $e) {   //catch any exceptions
            return back()->withError("Login failed: " . $e->getMessage());
        }
        // the user doesn't exist in the LDAP server or the password is wrong
        return back()->with('error', 'Incorrect credentials or account is disabled.');
    }

    protected function hasRights($username)
    {
        $ldapuser = Adldap::search()->where("sAMAccountName", '=', $username)->first();     //Look for the user by username in LDAP.
        $memberof = $ldapuser['memberof'];  //get the groups that the account is a member of

        //check if the account is in one of the appropriate groups
        foreach ($memberof as $member) {
            if (stripos($member, 'leaveappuser') !== false) {
                return 1;   //user
            }
            if (stripos($member, 'leaveapphr') !== false) {
                return 2;   //hr
            }
            if (stripos($member, 'leaveappadmin') !== false) {
                return 3;   //admin
            }
        }
        return -1;  //no permissions
    }


    //calc accrued leave
    private function calcLeave($user)
    {
        $user->sick_days = 30;
        $user->entitled_days = 0;
        $user->days_taken = 0;
        $user->family_days = 3;
        $user->unpaid_leave = 0;

        //if end_cycle is less than the current year, then the cycle has expired and needs to be updated
        if ($user->end_cycle < Carbon::now()->year) {
            $user->end_cycle = $user->end_cycle + 3;    //update the new end_cycle value | ex: old; 2019 (2017,2018,2019), new; 2022 (2020,2021,2022)
            $user->sick_days = 30;  //reset sick_days to 30 as per law
            $user->save();
        }

        //get all the sick leave requests for the user's sick leave cycle. 'end_cycle' represents the last year of the cycle. Subtract 2 to get the beginning year of the cycle. Ex: 2022-2=2020. So cycle=(2020,2021,2022)
        $sick_leave = Leave::whereBetween('start_date', [Carbon::create($user->end_cycle - 2)->format('Y-m-d'), Carbon::createFromDate($user->end_cycle, 12, 31)->format('Y-m-d')])->where('username', $user->username)->where('type', 'Sick Leave')->get(); //say end_cycle 2020, then between 2018-2020 (both inclusive)
        $sick_days = 0;
        foreach ($sick_leave as $leave) {    //add the amount of sick days taken in the cycle
            if ($leave->status == "Approved" || $leave->status == "Pending") {
                $sick_days += $leave->duration;
            }
        }
        $user->sick_days -= $sick_days;   //adjust the user's sick days available
        $user->save();

        //calculate all leave on login
        $userleave = Leave::where('username', $user->username)->get();
        $days_taken = 0;
        $family_responsibility = 0;
        $unpaid_leave = 0;
        $parental_leave = 0;
        foreach ($userleave as $leave) {
            if ($leave->status == "Approved" && $leave->type == "Annual Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $days_taken += $leave->duration;
            }
            if ($leave->status == "Pending" && $leave->type == "Annual Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $days_taken += $leave->duration;
            }
            if ($leave->status == "Approved" && $leave->type == "Family Responsibility Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $family_responsibility += $leave->duration;
            }
            if ($leave->status == "Pending" && $leave->type == "Family Responsibility Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $family_responsibility += $leave->duration;
            }
            if ($leave->status == "Approved" && $leave->type == "Paternity/Maternity Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $parental_leave += $leave->duration;
            }
            if ($leave->status == "Pending" && $leave->type == "Paternity/Maternity Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $parental_leave += $leave->duration;
            }
            if ($leave->status == "Approved" && $leave->type == "Unpaid Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $unpaid_leave += $leave->duration;
            }
            if ($leave->status == "Pending" && $leave->type == "Unpaid Leave" && date('Y', strtotime($leave->start_date)) == Carbon::now()->year) {
                $unpaid_leave += $leave->duration;
            }
        }
        $user->days_taken += $days_taken;
        $user->family_days -= $family_responsibility;
        $user->parental_leave += $parental_leave;
        $user->unpaid_leave += $unpaid_leave;

        if ($user->can_carry_over) {  //if the user can carry over leave, subtract the leave from days already taken
            $user->days_taken -= $user->carried_over_leave_days;
        }
        $user->save();

        //calculate the accrued leave
        $employmentYear = Carbon::createFromFormat('Y-m-d', $user->employment_date)->year;  //check if user started working at company in the current year
        if (Carbon::now()->year !== $employmentYear) {    //user did not start working at company during the current year, so calculate leave as normal from beginning of the year
            $yearStart = Carbon::now()->startOfYear();  //get 1 Jan {{year}}
            $currentDate = Carbon::now();   //get current date
            $monthsSinceJan = $currentDate->diffInMonths($yearStart); //get month difference since 1 Jan

            if ($user->extra_days == true) {
                $accrued_leave = $monthsSinceJan * 1.67;
            } else {
                $accrued_leave = $monthsSinceJan * 1.25;
            }
            $user->entitled_days = $accrued_leave - $user->days_taken;
            $user->save();
        } else {   //user has started working at company this year, start calculating leave from the date they started
            $monthStart = Carbon::createFromFormat('Y-m-d', $user->employment_date)->month; //get month the user started at company
            $currentDate = Carbon::now()->month;   //get current date
            $monthsSinceJan = $currentDate - $monthStart; //get month difference since 1 Jan

            if ($user->extra_days == true) {
                $accrued_leave = $monthsSinceJan * 1.67;
            } else {
                $accrued_leave = $monthsSinceJan * 1.25;
            }
            $user->entitled_days = $accrued_leave - $user->days_taken;
            $user->save();
        }
    }
}
