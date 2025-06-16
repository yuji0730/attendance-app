<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString()); // 指定がなければ今日

        $users = User::where('is_admin', false)->get();

        $attendances = Attendance::with('rests')
        ->where('date', $date)
        ->get()
        ->keyBy('user_id');


        return view('admin.index', compact('users', 'attendances', 'date'));
    }

    public function staff()
    {
        return view('admin.staff');
    }
}
