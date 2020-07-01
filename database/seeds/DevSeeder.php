<?php

use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    /**
     * Seed the dev application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ClientsSeeder::class,
            MessagesSeeder::class,
            MessagesScheduleSeeder::class,
        ]);
    }
}
