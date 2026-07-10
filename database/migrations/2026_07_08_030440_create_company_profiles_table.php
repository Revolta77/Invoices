<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('street')->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('SK');
            $table->string('ico', 16)->nullable();
            $table->string('dic', 16)->nullable();
            $table->string('taxpayer_type')->default('neplatitel_dph');
            $table->string('ic_dph', 20)->nullable();
            $table->text('registry')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('web')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
