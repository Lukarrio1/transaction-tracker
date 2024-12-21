<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('small_description');
            $table->integer('node_type');
            $table->integer('node_status');
            $table->longText('properties')->nullable();
            $table->integer('authentication_level');
            $table->integer('permission_id')->default(0);
            $table->longText('uuid')->nullable();
            $table->string('tenant_id')->nullable();
            $table->longText('verbiage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
