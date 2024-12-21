<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('references', function (Blueprint $table) {
            $table->index(['owner_id', 'owned_id', 'type']);
            $table->index(['owned_id', 'owner_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('references', function (Blueprint $table) {
            $table->dropIndex(['owner_id', 'owned_id', 'type']);
            $table->dropIndex(['owned_id', 'owner_id', 'type']);
        });
    }
};
