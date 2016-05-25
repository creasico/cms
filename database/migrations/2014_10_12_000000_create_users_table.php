<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password', 63);
            $table->string('display')->default('Anonymous');

            $table->boolean('active')->default(false);
            $table->integer('login_count')->default(0);
            $table->dateTime('current_login_at')->nullable();
            $table->string('current_login_ip')->default('');
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip')->default('');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
