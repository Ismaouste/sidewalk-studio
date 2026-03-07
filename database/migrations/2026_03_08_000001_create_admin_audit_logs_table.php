<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('actor_type');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('actor_email')->nullable();
            $table->string('action');
            $table->string('subject');
            $table->json('summary')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
            $table->index(['subject', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
