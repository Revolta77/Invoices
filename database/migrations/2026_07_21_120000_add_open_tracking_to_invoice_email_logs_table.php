<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_email_logs', function (Blueprint $table) {
            $table->uuid('open_token')->nullable()->unique()->after('sent_at');
            $table->timestamp('opened_at')->nullable()->after('open_token');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_email_logs', function (Blueprint $table) {
            $table->dropColumn(['open_token', 'opened_at']);
        });
    }
};
