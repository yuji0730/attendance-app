<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModificationRequestStoreRequest;
use App\Models\Attendance;
use App\Models\ModificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModificationRequestController extends Controller
{
    public function store(ModificationRequestStoreRequest $request, $date)
    {
        $userId = Auth::id();

        // 指定された日付の勤怠を取得（なくてもOK）
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        // 勤怠が存在するなら、attendance_id を使う
        $attendanceId = optional($attendance)->id;

        $rests = array_values($request->input('rests', [])); // 休憩時間をJSONとして保存

        ModificationRequest::create([
            'user_id' => $userId,
            'attendance_id' => $attendanceId,
            'date' => $date,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'rests' => json_encode($rests),
            'remarks' => $request->remarks,
            'status' => 'pending',
        ]);

        return redirect()->route('attendance.index', ['id' => $attendance->id ?? -1])->with('success', '修正申請を送信しました。');
    }

    public function request()
    {
        $user = auth()->user();

        if ($user->is_admin) {
            // 管理者：全ユーザーの申請一覧
            $pendingRequests = ModificationRequest::with(['attendance', 'user'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $approvedRequests = ModificationRequest::with(['attendance', 'user'])
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();

            $title = '申請一覧ページ（管理者）';
        } else {
            // 一般ユーザー：自分の申請一覧
            $pendingRequests = ModificationRequest::with('attendance')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $approvedRequests = ModificationRequest::with('attendance')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();

            $title = '申請一覧ページ（一般ユーザー）';
        }

        return view('request', [
            'pendingRequests' => $pendingRequests,
            'approvedRequests' => $approvedRequests,
            'isAdmin' => $user->is_admin,
            'title' => $title,
        ]);
    }



}
