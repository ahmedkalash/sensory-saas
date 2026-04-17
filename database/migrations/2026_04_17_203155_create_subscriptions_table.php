<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->date('starts_at')->nullable()->comment('For yearly plans');
            $table->date('ends_at')->nullable()->comment('For yearly plans');
            $table->unsignedInteger('quota_remaining')->nullable()->comment('For quota plans');
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->text('notes')->nullable()->comment('Admin notes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
