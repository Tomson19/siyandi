<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alur_sop', function (Blueprint $table) {
            $table->id();
            $table->string('judul');           // "Alur dan SOP"
            $table->string('file_path');       // path PDF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alur_sop');
    }
};
