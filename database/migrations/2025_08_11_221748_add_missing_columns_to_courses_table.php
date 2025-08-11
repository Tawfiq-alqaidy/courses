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
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('duration')->default(0); // in hours
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn(['instructor_id', 'price', 'duration', 'status', 'image']);
        });
    }
};
