<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // Relacionado a um serviço
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade'); // Relacionado a um freelancer
            $table->text('message')->nullable(); // Mensagem do freelancer
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending'); // Status da aplicação
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applications');
    }
};
