<?php

use Bonfix\DaliliSms\Models\SmsUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsUsersTable extends Migration
{
    protected $tableName;
    public function __construct()
    {
        $this->tableName = SmsUser::TABLE_NAME;
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
            $table->string('phone', 20);
            $table->string('language_code', 2)->nullable();
            $table->string('country_code', 2);
            $table->string('username', 255)->nullable();
            $table->integer('region_id')->nullable();
            $table->integer('village_id')->nullable();
            $table->timestamp('last_request_time');
            $table->boolean('isActive')->default(1);
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
        Schema::dropIfExists($this->tableName);
    }
}
