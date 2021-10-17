<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->charset = "utf8";
            $table->collation = "utf8_spanish2_ci";

            $table->id();
            $table->string("name", 30)->nullable(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
