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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
            $table->unsignedBigInteger('inspection_id');
            $table->foreign('inspection_id')->references('id')->on('inspections')->onDelete('cascade');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->date('post_inspection_date')->nullable();
            $table->string('post_license_plate')->nullable();
            $table->string('post_model')->nullable();
            $table->integer('post_kilometer')->nullable();
            $table->time('post_jam_keluar')->nullable();
            $table->string('post_bbm')->nullable();
            $table->text('post_comments')->nullable();
            $table->string('post_images')->nullable();
            $table->string('post_peralatan')->nullable();
            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
