<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('leave_deduction_days', 5, 2)->nullable()->after('pf_deduction');
            $table->decimal('leave_deduction', 10, 2)->nullable()->after('leave_deduction_days');
            $table->json('leave_breakdown')->nullable()->after('leave_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn([
                'leave_deduction_days',
                'leave_deduction',
                'leave_breakdown',
            ]);
        });
    }
};
