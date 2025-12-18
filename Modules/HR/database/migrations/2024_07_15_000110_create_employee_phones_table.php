<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('phone', 30);
            $table->enum('type', ['personal', 'work', 'emergency'])->default('personal');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('phone');
            $table->index('is_primary');
            $table->unique(['employee_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_phones');
    }
};
