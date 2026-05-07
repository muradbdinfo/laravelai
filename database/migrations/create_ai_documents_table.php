<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('ai.rag.table', 'ai_documents'), function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('source')->default('');
            $table->longText('embedding');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('ai.rag.table', 'ai_documents'));
    }
};