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
        Schema::create('professional_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('state');
            $table->decimal('income_from', 12, 2);
            $table->decimal('income_to', 12, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['state', 'income_from', 'income_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_taxes');
    }
};
