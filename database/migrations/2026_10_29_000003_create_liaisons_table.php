<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liaisons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->foreignId('site_a_id')->constrained('sites')->restrictOnDelete();
            $table->foreignId('site_b_id')->constrained('sites')->restrictOnDelete();
            $table->string('type'); // Fibre, FH, Satellite...
            $table->string('capacite', 20); // 10G, 100G...
            $table->string('operateur')->nullable();
            $table->string('statut')->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liaisons');
    }
};
