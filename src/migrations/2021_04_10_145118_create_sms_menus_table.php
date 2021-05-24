<?php

use Bonfix\DaliliSms\Models\SmsMenu;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsMenusTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsMenu::TABLE_NAME;
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
            $table->string('name', 255);
            $table->string('group', 255);
            $table->integer('order');
            $table->boolean('optionsNavigable');
            $table->boolean('allowMultiple')->default(0);
            $table->timestamps();

            $table->unique(['group', 'order']);
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
