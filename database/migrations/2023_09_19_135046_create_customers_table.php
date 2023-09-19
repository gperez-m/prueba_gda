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
        Schema::create('customers', function (Blueprint $table) {
            $table->string('dni',45)->comment('Documento de Identidad');
            $table->integer('id_reg');
            $table->integer('id_com');
            $table->string('email',120)->comment('Correo Electrónico')->unique();
            $table->string('name',45)->comment('Nombre');
            $table->string('last_name',45)->comment('Apellido');
            $table->string('address',255)->comment('Dirección');
            $table->dateTime('data_reg')->comment('Fecha y hora del registro');
            $table->enum('status',['A','I', 'trash'])->default('A')->comment('estado del registro:\nA : Activo\nI : Desactivo\ntrash : Registro eliminado');
            $table->primary(['dni', 'id_reg', 'id_com']);
            $table->index(['id_reg', 'id_com']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
