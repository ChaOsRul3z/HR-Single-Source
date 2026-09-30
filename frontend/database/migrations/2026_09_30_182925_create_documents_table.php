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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('document_code')->index();
            $table->string('department')->default('HR')->index();
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('active')->index(); // 'active' or 'archived'
            $table->boolean('is_restricted')->default(false)->index(); // true = HR/Admin only
            $table->date('effective_date')->nullable();
            $table->text('summary')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
