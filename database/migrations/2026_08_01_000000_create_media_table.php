<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->nullableMorphs('mediable');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider')->default('cloudinary');
            $table->string('public_id')->unique();
            $table->string('resource_type', 20);
            $table->string('media_type', 20);
            $table->string('purpose', 50);
            $table->string('original_name');
            $table->string('format', 20)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->decimal('duration', 10, 2)->nullable();
            $table->text('secure_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->string('status', 30)->default('pending');
            $table->string('visibility', 20)->default('public');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'status']);
            $table->index(['uploaded_by', 'created_at']);
            $table->index(['media_type', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
