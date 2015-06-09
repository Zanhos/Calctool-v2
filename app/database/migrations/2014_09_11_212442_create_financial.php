<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Financial migration
 *
 * Color: Orange
 */
class CreateFinancial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('deliver_time', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('delivertime_name', 10)->unique();
		});

		Schema::create('valid', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('valid_name', 10)->unique();
		});

		Schema::create('offer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('description')->nullable();
			$table->text('closure')->nullable();
			$table->boolean('downpayment')->default('N');
			$table->boolean('auto_email_reminder')->default('Y');
			$table->nullableTimestamps();
			$table->date('offer_finish')->nullable();
			$table->integer('deliver_id')->unsigned();
			$table->foreign('deliver_id')->references('id')->on('deliver_time')->onUpdate('cascade')->onDelete('restrict');
			$table->integer('valid_id')->unsigned();
			$table->foreign('valid_id')->references('id')->on('valid')->onUpdate('cascade')->onDelete('restrict');
			$table->integer('project_id')->unsigned();
			$table->foreign('project_id')->references('id')->on('project')->onUpdate('cascade')->onDelete('cascade');
		});

		Schema::create('invoice', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('invoice_close')->default('N');
			$table->text('description')->nullable();
			$table->string('reference', 30)->index();
			$table->string('invoice_code', 50)->index();
			$table->string('book_code', 30)->index();
			$table->decimal('amount', 9, 2);
			$table->integer('payment_condition')->unsigned();
			$table->nullableTimestamps();
			$table->date('payment_date');
			$table->boolean('display_tax')->default('Y');
			$table->text('closure')->nullable();
			$table->boolean('auto_email_reminder')->default('Y');
			$table->integer('offer_id')->unsigned();
			$table->foreign('offer_id')->references('id')->on('offer')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('invoice', function(Blueprint $table)
		{
			Schema::drop('invoice');
		});

		Schema::table('offer', function(Blueprint $table)
		{
			Schema::drop('offer');
		});

		Schema::table('valid', function(Blueprint $table)
		{
			Schema::drop('valid');
		});

		Schema::table('deliver_time', function(Blueprint $table)
		{
			Schema::drop('deliver_time');
		});
	}

}

