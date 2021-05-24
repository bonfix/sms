<?php
namespace Bonfix\DaliliSms\seeds;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SmsMenuSeeder::class);
    }
}
