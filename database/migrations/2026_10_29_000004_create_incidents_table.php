<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->nullable()->unique();
            $table->string('titre');
            $table->text('description');
            $table->string('severite')->index();
            $table->string('statut')->default('ouvert')->index();
            $table->string('categorie');
            $table->foreignId('site_id')->constrained()->restrictOnDelete();
            $table->foreignId('equipement_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('liaison_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('declare_par_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigne_a_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('debut_at')->index();
            $table->dateTime('resolu_at')->nullable();
            $table->text('impact')->nullable();
            $table->text('cause_racine')->nullable();
            $table->text('solution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
