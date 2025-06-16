<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Attendance::create([
            'user_id' => 1, // 一般ユーザーID
            'date' => '2025-05-22',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
    }
}
