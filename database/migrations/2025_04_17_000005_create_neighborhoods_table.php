<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('description'); // e.g. "Quiet and family-friendly area."
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('neighborhoods');
    }
};
