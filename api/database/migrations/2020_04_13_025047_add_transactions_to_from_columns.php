<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionsToFromColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->float('money_to');
            $table->float('money_from');
            $table->bigInteger('to_currency_id');
            $table->bigInteger('from_currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('money_to');
            $table->dropColumn('money_from');
            $table->dropColumn('to_currency_id');
            $table->dropColumn('from_currency_id');
        });
    }
}
