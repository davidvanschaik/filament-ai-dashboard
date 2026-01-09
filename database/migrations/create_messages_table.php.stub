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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->enum('type', ['message', 'task']);
            $table->json('metadata');
            $table->bigInteger('input_tokens')->unsigned()->default(0);
            $table->bigInteger('input_cached_tokens')->unsigned()->default(0);
            $table->bigInteger('output_tokens')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
