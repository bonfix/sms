<?php

use Bonfix\DaliliSms\Models\SmsMenuOption;
use Bonfix\DaliliSms\Models\SmsMenuOptionTranslation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsMenuOptionTranslationsTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsMenuOptionTranslation::TABLE_NAME;
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_option_id')->unsigned();
            $table->string('locale')->index();
            $table->string('description', 255);

            $table->unique(['menu_option_id', 'locale']);
            $table->foreign('menu_option_id')->references('id')->on(SmsMenuOption::TABLE_NAME)->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
