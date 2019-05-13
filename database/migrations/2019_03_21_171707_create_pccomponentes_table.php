<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePccomponentesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pccomponentes', function(Blueprint $table)
		{
			$table->string('codigo', 150)->primary();
			$table->string('nombre', 150);
			$table->string('familia', 150)->nullable();
			$table->float('precio', 10, 0)->nullable();
			$table->string('referencia_fabricante', 150)->nullable();
			$table->string('marca', 150)->nullable();
			$table->string('enlace', 150)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pccomponentes');
	}

}
