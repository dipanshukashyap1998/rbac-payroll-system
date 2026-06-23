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
        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('ta', 10, 2)->nullable()->after('allowances');
            $table->decimal('da', 10, 2)->nullable()->after('ta');
            $table->decimal('gross_salary', 10, 2)->nullable()->after('da');
            $table->decimal('income_tax', 10, 2)->nullable()->after('gross_salary');
            $table->decimal('professional_tax', 10, 2)->nullable()->after('income_tax');
            $table->decimal('pf_deduction', 10, 2)->nullable()->after('professional_tax');
            $table->decimal('total_deductions', 10, 2)->nullable()->after('pf_deduction');
            $table->text('tax_breakdown')->nullable()->after('total_deductions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn([
                'ta',
                'da',
                'gross_salary',
                'income_tax',
                'professional_tax',
                'pf_deduction',
                'total_deductions',
                'tax_breakdown',
            ]);
        });
    }
};
