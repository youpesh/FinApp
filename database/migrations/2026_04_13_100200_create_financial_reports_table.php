<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'trial_balance',
                'income_statement',
                'balance_sheet',
                'retained_earnings',
            ]);
            $table->string('title');
            $table->json('parameters'); // {date_from, date_to, as_of}
            $table->json('payload');    // computed rows + totals
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['type', 'generated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
