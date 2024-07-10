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
         * Create the users table migration.
         *
         * This migration creates the "users" table in the database with the following columns:
         * - id: The primary key for the table.
         * - name: The name of the user.
         * - email: The email address of the user (unique).
         * - email_verified_at: The timestamp when the user's email was verified (nullable).
         * - password: The user's password.
         * - remember_token: The remember token for the "remember me" functionality.
         * - created_at: The timestamp for when the user was created.
         * - updated_at: The timestamp for when the user was last updated.
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
