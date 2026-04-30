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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('plan_expiry_at')->nullable()->after('plan');
            $table->string('plan_status')->default('active')->after('plan_expiry_at'); // active, pending, expired
            $table->string('payment_id')->nullable()->after('plan_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan_expiry_at', 'plan_status', 'payment_id']);
        });
    }
};
