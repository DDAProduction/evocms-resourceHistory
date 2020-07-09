<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteContentHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
		Schema::create('site_content_history', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('resource_id')->default(0)->index('resource_history_id');
			$table->longText('document_object')->default();
			$table->longText('post_data')->nullable();
			$table->text('notice')->nullable();
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
		Schema::drop('site_content_history');
	}

}
