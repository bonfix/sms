<?php

use Bonfix\DaliliSms\Models\SmsMenu;
use Bonfix\DaliliSms\Models\SmsMenuOption;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsMenuOptionsTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsMenuOption::TABLE_NAME;
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
            $table->integer('order');
            $table->string('value', 255);
            $table->timestamps();

            $table->unique(['menu_id', 'order']);
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
