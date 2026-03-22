<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name')->unique();
            $table->unsignedBigInteger('account_number')->unique();
            $table->text('account_description')->nullable();
            $table->enum('normal_side', ['debit', 'credit']);
            $table->enum('account_category', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('account_subcategory');
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->enum('statement', ['IS', 'BS', 'RE'])->comment('IS=Income Statement, BS=Balance Sheet, RE=Retained Earnings');
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
