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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('tickets');
            $table->string('service_area');
            $table->foreignId('support_id')->nullable()->constrained('users')->default(null);            $table->boolean('status')->default(false);
            $table->string('service')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
