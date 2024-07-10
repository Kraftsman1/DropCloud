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
         * Create the 'folders' table migration.
         *
         * This migration creates the 'folders' table in the database. The table contains the following columns:
         * - id: The primary key of the folder.
         * - name: The name of the folder.
         * - parent_id: The foreign key referencing the parent folder (nullable).
         * - user_id: The foreign key referencing the user who owns the folder.
         * - created_at: The timestamp when the folder was created.
         * - updated_at: The timestamp when the folder was last updated.
         * - deleted_at: The timestamp when the folder was soft deleted.
         */
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
