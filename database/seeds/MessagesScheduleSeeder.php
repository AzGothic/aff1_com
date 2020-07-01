<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\MessageSchedule::class, 100)->create();
    }
}
