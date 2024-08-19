<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
            $table->unsignedBigInteger('approval_id');
            $table->foreign('approval_id')->references('id')->on('approvals')->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->date('inspection_date')->nullable();
            $table->string('model')->nullable();
            $table->string('license_plate')->nullable();
            $table->text('comments')->nullable();
            $table->string('images')->nullable();
            $table->integer('kilometer')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('bbm')->nullable();
            $table->string('peralatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
