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
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            
            // Student Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->text('address');
            
            // Educational Background
            $table->string('education_level'); // high school, bachelor, master, etc.
            $table->string('field_of_study')->nullable();
            $table->string('current_occupation')->nullable();
            $table->integer('experience_years')->default(0);
            
            // Course Related
            $table->text('motivation'); // Why do you want to join this course?
            $table->text('expectations'); // What do you expect to learn?
            $table->text('previous_experience')->nullable(); // Previous experience in this field
            $table->json('available_times')->nullable(); // When are you available?
            
            // Application Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            // Payment Information (if course has fee)
            $table->decimal('amount_to_pay', 8, 2)->default(0);
            $table->boolean('payment_completed')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            
            $table->timestamps();
            
            // Add foreign key for reviewer
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Prevent duplicate applications
            $table->unique(['course_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};
