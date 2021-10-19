<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->charset = "utf8";
            $table->collation = "utf8_spanish2_ci";

            $table->id();
            $table->string("name", 95)->nullable(false);
            $table->string("email", 45)->nullable(false);
            $table->date("birthday");
            $table->timestamp('email_verified_at')->nullable();
            $table->string("password", 250)->nullable(false);
            $table->rememberToken();
            $table->bigInteger('created_by_id')->nullable(false)->unsigned()->index();
            $table->bigInteger('updated_by_id')->nullable(false)->unsigned()->index();
            $table->boolean("active")->nullable(false)->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
