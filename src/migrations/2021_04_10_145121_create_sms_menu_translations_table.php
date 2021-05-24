<?php

use Bonfix\DaliliSms\Models\SmsMenu;
use Bonfix\DaliliSms\Models\SmsMenuOption;
use Bonfix\DaliliSms\Models\SmsMenuTranslation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsMenuTranslationsTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsMenuTranslation::TABLE_NAME;
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
            $table->integer('menu_id')->unsigned();
            $table->string('locale')->index();
            $table->string('description', 255);

            $table->unique(['menu_id', 'locale']);
            $table->foreign('menu_id')->references('id')->on(SmsMenu::TABLE_NAME)->onDelete('cascade');

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
