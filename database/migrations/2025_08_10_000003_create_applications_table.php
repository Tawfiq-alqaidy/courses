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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('student_email')->unique();
            $table->string('student_phone');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->json('selected_courses'); // Array of course IDs
            $table->string('unique_student_code')->unique();
            $table->enum('status', ['unregistered', 'registered', 'waiting'])->default('unregistered');
            $table->timestamps();

            // Index for performance
            $table->index('student_email');
            $table->index('unique_student_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
