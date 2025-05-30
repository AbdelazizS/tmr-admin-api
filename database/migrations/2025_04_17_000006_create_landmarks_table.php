<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('landmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('neighborhood_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. "Al Wasl Hospital", "Burj Khalifa"
            $table->string('distance'); // e.g. "Al Wasl Hospital", "Burj Khalifa"
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('landmarks');
    }
};
