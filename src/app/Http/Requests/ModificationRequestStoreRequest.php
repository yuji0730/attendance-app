<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class ModificationRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'rests' => 'array|nullable',
            'rests.*.start_time' => 'nullable|date_format:H:i',
            'rests.*.end_time' => 'nullable|date_format:H:i',
            'rests.new.start_time' => 'nullable|date_format:H:i',
            'rests.new.end_time' => 'nullable|date_format:H:i',
            'remarks' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_in.date_format' => '出勤時間の形式が正しくありません',
            'clock_out.date_format' => '退勤時間の形式が正しくありません',
            'rests.*.start_time.date_format' => '休憩開始時間の形式が正しくありません',
            'rests.*.end_time.date_format' => '休憩終了時間の形式が正しくありません',
            'rests.new.start_time.date_format' => '休憩開始時間の形式が正しくありません',
            'rests.new.end_time.date_format' => '休憩終了時間の形式が正しくありません',
            'remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            $clockInStr = $this->input('clock_in');
            $clockOutStr = $this->input('clock_out');

            if ($clockInStr && $clockOutStr) {
                try {
                    $clockIn = Carbon::createFromFormat('H:i', $clockInStr);
                    $clockOut = Carbon::createFromFormat('H:i', $clockOutStr);

                    if ($clockIn->gte($clockOut)) {
                        $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
                    }
                } catch (\Exception $e) {
                    // ここはrulesで補足済みなので無視
                }
            }

            // 休憩時間の範囲チェック
            $rests = $this->input('rests', []);
            foreach ($rests as $index => $rest) {
                $startStr = $rest['start_time'] ?? null;
                $endStr = $rest['end_time'] ?? null;

                if ($startStr && $endStr && $clockInStr && $clockOutStr) {
                    try {
                        $restStart = Carbon::createFromFormat('H:i', $startStr);
                        $restEnd = Carbon::createFromFormat('H:i', $endStr);
                        $clockIn = Carbon::createFromFormat('H:i', $clockInStr);
                        $clockOut = Carbon::createFromFormat('H:i', $clockOutStr);

                        if ($restStart->lt($clockIn) || $restEnd->gt($clockOut)) {
                            $validator->errors()->add("rests.$index.start_time", '休憩時間が勤務時間外です');
                        }
                    } catch (\Exception $e) {
                        // 無効なフォーマットはスキップ（rulesで処理済み）
                    }
                }
            }
        });
    }

}
