<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\ModificationRequest;

class AdminModificationRequestController extends Controller
{
    public function showApproval(ModificationRequest $attendance_correct_request)
    {
        $modificationRequest = $attendance_correct_request;

        $attendance = new \App\Models\Attendance([
            'clock_in' => $modificationRequest->clock_in,
            'clock_out' => $modificationRequest->clock_out,
            'remarks' => $modificationRequest->remarks,
            'rests' => collect(),
            'date' => $modificationRequest->date,
        ]);

        $restsArray = is_string($modificationRequest->rests)
            ? json_decode($modificationRequest->rests, true)
            : $modificationRequest->rests;

        $restsCollection = collect();
        foreach ($restsArray as $rest) {
            $restsCollection->push(new \App\Models\Rest([
                'start_time' => $rest['start_time'] ?? null,
                'end_time' => $rest['end_time'] ?? null,
            ]));
        }
        $attendance->setRelation('rests', $restsCollection);

        $userName = $modificationRequest->user->name ?? '不明';
        $status = $modificationRequest->status;

        return view('admin.approve',  [
            'attendance' => $attendance,
            'userName' => $userName,
            'status' => $status,
            'modificationRequest' => $modificationRequest,
            'date' => $modificationRequest->date,
        ]);
    }



    public function approve($id)
    {
        $request = ModificationRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        return redirect()->route('admin.approval.show', $id)->with('success', '承認が完了しました。');
    }

}
