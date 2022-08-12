<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->unique();
            $table->string('author');
            $table->string('language');
            $table->string('cover');
            $table->string('genre');
            $table->unsignedInteger('price');
            $table->date('publication_date');
            $table->string('publisher');
            $table->text("description");
            $table->unsignedInteger('stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
