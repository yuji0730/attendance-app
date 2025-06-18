<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModificationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modification_requests', function (Blueprint $table) {
            // $table->id();
            // $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            // $table->time('requested_clock_in')->nullable();
            // $table->time('requested_clock_out')->nullable();
            // $table->json('requested_breaks')->nullable();
            // $table->text('note');
            // $table->enum('status', ['承認待ち', '承認済み'])->default('承認待ち');
            // $table->timestamps();
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->date('date')->nullable();
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->json('rests')->nullable(); // JSON形式で休憩時間を保持
            $table->text('remarks')->nullable();
            $table->string('status')->default('pending'); // pending, approved
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modification_requests');
    }
}
