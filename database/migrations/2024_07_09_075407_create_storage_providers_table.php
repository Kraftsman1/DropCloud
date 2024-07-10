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
         * Create the storage_providers table.
         *
         * This migration creates the storage_providers table in the database. The table
         * stores information about different storage providers that can be used in the
         * application. Each storage provider has a unique ID, a name, configuration
         * details, and timestamps for creation and updates. The table also supports
         * soft deletes, allowing records to be marked as deleted without actually
         * removing them from the database.
         */
        Schema::create('storage_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('configuration');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_providers');
    }
};
