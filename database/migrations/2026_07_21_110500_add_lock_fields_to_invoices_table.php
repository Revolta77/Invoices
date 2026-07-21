<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('paid_payment_method');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->index(['company_profile_id', 'is_locked']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['company_profile_id', 'is_locked']);
            $table->dropColumn(['is_locked', 'locked_at']);
        });
    }
};
