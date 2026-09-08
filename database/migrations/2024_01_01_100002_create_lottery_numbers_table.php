<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_purchase_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('lottery_id')->constrained()->onDelete('cascade');
            $table->string('number', 50);
            $table->timestamp('generated_at');
            $table->enum('status', ['active', 'used', 'void'])->default('active');
            $table->timestamps();

            // Unique number per lottery — prevents duplicate lottery numbers
            $table->unique(['lottery_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_numbers');
    }
};
