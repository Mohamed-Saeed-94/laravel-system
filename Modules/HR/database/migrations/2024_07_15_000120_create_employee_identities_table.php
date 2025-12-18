<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['iqama', 'saudi_national_id', 'passport']);
            $table->string('number', 30);
            $table->string('sponsor_name', 200)->nullable();
            $table->string('sponsor_id_number', 30)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('number');
            $table->index('sponsor_id_number');
            $table->index('expiry_date');
            $table->index('is_primary');
            $table->unique(['type', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_identities');
    }
};
