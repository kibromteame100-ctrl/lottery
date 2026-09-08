<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotteries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('ticket_price', 10, 2);
            $table->dateTime('draw_date');
            $table->enum('status', ['active', 'inactive', 'completed', 'cancelled'])->default('active');
            $table->string('number_prefix', 20)->default('LOT');
            $table->unsignedTinyInteger('number_length')->default(6);
            $table->unsignedInteger('max_tickets')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotteries');
    }
};
