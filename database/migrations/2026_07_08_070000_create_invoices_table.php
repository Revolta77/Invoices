<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('number', 32);
            $table->string('partner_name');
            $table->string('partner_ico', 16)->nullable();
            $table->string('partner_street')->nullable();
            $table->string('partner_postal_code', 16)->nullable();
            $table->string('partner_city')->nullable();
            $table->string('partner_country', 2)->default('SK');
            $table->string('partner_dic', 16)->nullable();
            $table->string('partner_ic_dph', 20)->nullable();
            $table->date('issue_date');
            $table->date('delivery_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedSmallInteger('due_days')->nullable();
            $table->boolean('is_identified_person')->default(false);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('exchange_rate', 12, 4)->nullable();
            $table->string('iban', 34)->nullable();
            $table->string('bank_account')->nullable();
            $table->string('payment_method')->default('bank_transfer');
            $table->string('status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('total', 14, 2)->default(0);
            $table->boolean('signature_enabled')->default(true);
            $table->text('signature_text')->nullable();
            $table->timestamps();

            $table->unique(['company_profile_id', 'number']);
            $table->index(['company_profile_id', 'issue_date']);
            $table->index(['company_profile_id', 'status']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(1);
            $table->string('name');
            $table->decimal('quantity', 12, 3)->default(1);
            $table->string('unit', 16)->default('ks');
            $table->decimal('unit_price', 14, 4)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
