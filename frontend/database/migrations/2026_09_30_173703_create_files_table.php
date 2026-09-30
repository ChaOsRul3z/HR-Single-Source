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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name'); // e.g., "invoice.pdf"
            $table->string('storage_path');  // e.g., "uploads/hash_name.pdf"
            $table->string('mime_type');     // e.g., "application/pdf"
            $table->bigInteger('size');      // File size in bytes
            $table->string('file_hash', 64)->index(); // SHA-256 hash with an index for fast lookups
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
