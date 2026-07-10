<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('paid_amount', 14, 2)->nullable()->after('paid_at');
            $table->string('paid_payment_method')->nullable()->after('paid_amount');
        });

        foreach (DB::table('invoices')->whereNotNull('paid_at')->get(['id', 'total', 'payment_method']) as $invoice) {
            DB::table('invoices')->where('id', $invoice->id)->update([
                'paid_amount' => $invoice->total,
                'paid_payment_method' => $invoice->payment_method,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'paid_payment_method']);
        });
    }
};
