<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rest;

class RestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rest::create([
            'attendance_id' => 1, // 出勤レコードのID
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ]);
    }
}
