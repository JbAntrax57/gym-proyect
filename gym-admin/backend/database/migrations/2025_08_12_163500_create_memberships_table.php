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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Semanal', 'Mensual', 'Visita']);
            $table->integer('duration');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['Activa', 'Inactiva', 'Suspendida'])->default('Activa');
            $table->text('description')->nullable();
            $table->integer('max_visits');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices para mejorar el rendimiento
            $table->index(['type', 'is_active']);
            $table->index(['status', 'is_active']);
            $table->index('price');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
