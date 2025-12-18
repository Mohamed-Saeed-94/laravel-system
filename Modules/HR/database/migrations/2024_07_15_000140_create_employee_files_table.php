<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable');
            $table->enum('category', ['employee_photo', 'identity_photo', 'license_photo', 'other']);
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->enum('side', ['front', 'back', 'other'])->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('side');
            $table->index('is_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_files');
    }
};
