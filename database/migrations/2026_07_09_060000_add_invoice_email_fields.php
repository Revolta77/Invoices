<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('emailed_at')->nullable()->after('paid_at');
        });

        Schema::create('invoice_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('partner_ico', 16)->nullable();
            $table->string('partner_name');
            $table->string('to_email');
            $table->string('cc_email')->nullable();
            $table->string('from_email');
            $table->string('subject');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['company_profile_id', 'partner_ico']);
            $table->index(['company_profile_id', 'partner_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_email_logs');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('emailed_at');
        });
    }
};
