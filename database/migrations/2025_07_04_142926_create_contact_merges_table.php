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
        Schema::create('contact_merges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_contact_id')->constrained('contacts');
            $table->foreignId('merged_contact_id')->constrained('contacts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_merges');
    }
};
