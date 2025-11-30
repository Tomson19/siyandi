<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kbs_pohon', function (Blueprint $table) {
            $table->id();

            // Relasi ke pemilik
            $table->foreignId('kbs_pemilik_id')
                  ->constrained('kbs_pemilik')
                  ->onDelete('cascade');

            $table->integer('no_pohon')->nullable();          
            $table->string('nomor_pohon_induk')->nullable();  
            $table->decimal('latitude', 10, 6)->nullable();   
            $table->decimal('longitude', 10, 6)->nullable();  

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbs_pohon');
    }
};
