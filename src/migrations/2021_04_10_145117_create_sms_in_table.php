<?php

use Bonfix\DaliliSms\Models\SmsIn;
use Bonfix\DaliliSms\Models\SmsMenu;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsInTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsIn::TABLE_NAME;
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
            $table->integer('user_id');
            $table->text('message');
            $table->string('option', 255)->nullable();
            $table->boolean("isInvalid")->default(0);
            $table->timestamps();

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
