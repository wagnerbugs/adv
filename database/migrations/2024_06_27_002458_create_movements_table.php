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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->constrained();
            $table->string('organ')->nullable();
            $table->string('process')->nullable();
            $table->text('judicial_class')->nullable();
            $table->text('judicial_event')->nullable();
            $table->text('authors')->nullable();
            $table->text('defendants')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
