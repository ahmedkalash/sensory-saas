<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false)->after('quota_remaining');
        });

        // Migrate existing data if possible (status column might still exist)
        if (Schema::hasColumn('subscriptions', 'status')) {
            DB::table('subscriptions')->where('status', 'suspended')->update(['is_suspended' => true]);
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status')->default('active')->after('quota_remaining');
        });

        DB::table('subscriptions')->where('is_suspended', true)->update(['status' => 'suspended']);

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('is_suspended');
        });
    }
};
