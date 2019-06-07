<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('numeroTienda')->nullable();
            $table->string('nombreTienda')->nullable();
            $table->string('emailTienda')->nullable();
            $table->bigInteger('telefono')->nullable();
            $table->bigInteger('movil')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('numeroTienda')->nullable();
            $table->dropColumn('nombreTienda')->nullable();
            $table->dropColumn('emailTienda')->nullable();
            $table->dropColumn('telefono')->nullable();
            $table->dropColumn('movil')->nullable();
        });
    }
}
