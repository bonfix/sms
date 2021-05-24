<?php

use Bonfix\DaliliSms\Models\SmsMenu;
use Bonfix\DaliliSms\Models\SmsOut;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsOutTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsOut::TABLE_NAME;
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
            $table->text('message');
            $table->integer('user_id');
            $table->integer('page')->nullable();
            $table->string('next_menu', 255)->nullable();
            $table->json('cache')->nullable();
            $table->boolean('is_sent')->default(0);
            $table->boolean('is_delivered')->default(0);
            $table->integer('prev_item')->nullable();
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
