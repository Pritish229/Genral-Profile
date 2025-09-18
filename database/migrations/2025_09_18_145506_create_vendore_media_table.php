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
        Schema::create('vendore_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('vendor_id'); // FK → students.id

            $table->enum('media_usage', [
                'profile',
                'logo',
                'banner',
                'gallery',
                'kyc',
                'doc_scan',
                'avatar',
                'other'
            ])->default('profile');  // Purpose of file

            $table->enum('subject_role', [
                'self',
                'parent',
                'guardian',
                'spouse',
                'child',
                'other'
            ])->nullable();  // e.g., parent/guardian’s photo

            $table->string('subject_name', 150)->nullable();

            $table->string('file_url', 500)->nullable();   // Storage path (S3/local/etc.)
            $table->string('file_name', 200)->nullable();
            $table->string('mime_type', 100)->nullable(); // image/jpeg, video/mp4, etc.
            $table->bigInteger('size_bytes')->nullable();

            $table->unsignedInteger('width')->nullable();   // for images/videos
            $table->unsignedInteger('height')->nullable();
            $table->decimal('duration_sec', 8, 2)->nullable(); // for audio/video length

            $table->binary('checksum_hash')->nullable(); // SHA-256 for de-duplication
            $table->string('storage_provider', 40)->nullable(); // s3, gcs, local
            $table->string('storage_bucket', 120)->nullable();
            $table->string('storage_path', 500)->nullable();

            $table->string('alt_text', 255)->nullable();   // Accessibility text
            $table->string('caption', 255)->nullable();    // UI caption

            $table->json('tags')->nullable();       // arbitrary labels
            $table->json('thumbnails')->nullable(); // generated thumbnails (map of URLs)
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_public')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('meta')->nullable(); // EXIF data or provider payload

            $table->unsignedInteger('row_version')->default(0);
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendore_media');
    }
};
