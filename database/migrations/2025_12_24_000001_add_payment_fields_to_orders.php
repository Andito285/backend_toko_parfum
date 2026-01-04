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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'verified', 'cancelled'])->default('pending')->after('total_amount');
            $table->string('payment_proof')->nullable()->after('payment_status');
            $table->timestamp('payment_date')->nullable()->after('payment_proof');
            $table->timestamp('verified_at')->nullable()->after('payment_date');
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['payment_status', 'payment_proof', 'payment_date', 'verified_at', 'verified_by']);
        });
    }
};

