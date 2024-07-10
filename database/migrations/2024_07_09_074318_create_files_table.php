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
        /**
         * Create the 'files' table migration.
         *
         * This migration creates the 'files' table in the database with the following columns:
         * - id: The primary key of the table.
         * - name: The name of the file.
         * - size: The size of the file.
         * - mime_type: The MIME type of the file.
         * - path: The path of the file.
         * - user_id: The foreign key referencing the 'users' table.
         * - folder_id: The foreign key referencing the 'folders' table.
         * - created_at: The timestamp when the record was created.
         * - updated_at: The timestamp when the record was last updated.
         * - deleted_at: The timestamp when the record was soft deleted.
         *
         * @return void
         */
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('size');
            $table->string('mime_type');
            $table->string('path');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
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
