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
        Schema::create('address_lists', function (Blueprint $table) {
            $table->id();
            $table->string('studio_name');
            $table->string('studio_address');
            $table->timestamps();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->unsignedBigInteger('address_id')->after('price')->nullable();
            $table->foreign('address_id')->references('id')->on('address_lists')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
            $table->string('address');
        });

        Schema::dropIfExists('address_lists');
    }
};
