<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['private', 'motorcycle', 'public_transport', 'other']);
            $table->string('number', 30);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->index('expiry_date');
            $table->index('is_primary');
            $table->unique(['employee_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_licenses');
    }
};
