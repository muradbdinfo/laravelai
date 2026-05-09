<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Projects ────────────────────────────────────────────────────
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // ── Project Files ────────────────────────────────────────────────
        if (!Schema::hasTable('project_files')) {
            Schema::create('project_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('original_name');
                $table->string('stored_path');
                $table->string('mime_type')->default('text/plain');
                $table->enum('status', ['pending', 'ingested', 'failed'])->default('pending');
                $table->timestamps();
            });
        }

        // ── Add project_id to chat_sessions ──────────────────────────────
        if (Schema::hasTable('chat_sessions') && !Schema::hasColumn('chat_sessions', 'project_id')) {
            Schema::table('chat_sessions', function (Blueprint $table) {
                $table->foreignId('project_id')->nullable()->after('title')
                      ->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_sessions') && Schema::hasColumn('chat_sessions', 'project_id')) {
            Schema::table('chat_sessions', function (Blueprint $table) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            });
        }
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('projects');
    }
};
