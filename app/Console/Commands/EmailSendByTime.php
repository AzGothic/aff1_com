<?php

namespace App\Console\Commands;

use App\Mail\ClientMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailSendByTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send
                            {time? : Time of messages must be send, default is current time}
                            {--info : Show debug to console}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails to the clients by messages schedule';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time = $this->argument('time') ?: date('H:i');
        $info = (bool) $this->option('info');

        if ($info) {
            $this->info($time);
            $this->line('');
        }

        $isDst = date('I');

        $tzs = DB::table('clients')
            ->leftJoin('timezones', 'timezones.id', '=', 'clients.tz_id')
            ->select(
                'clients.tz_id as timezone_id',
                'timezones.name as timezone_name',
                ($isDst ? 'timezones.utc_dst_offset as utc_offset' : 'timezones.utc_offset as utc_offset')
            )
            ->groupBy('clients.tz_id')
            ->get();

        if (!$tzs) {
            if ($info) {
                $this->info('Clients not found');
                $this->line('');
            }

            return;
        }

        $offsets = [];
        foreach ($tzs as $tz) {
            if (!isset($offsets[$tz->utc_offset])) {
                $offsets[$tz->utc_offset] = [];
            }
            $offsets[$tz->utc_offset][] = $tz->timezone_id;
        }

        $timeCarbon = Carbon::createFromFormat('H:i', $time);
        foreach ($offsets as $offset => $tz_ids) {
            $offsetParts = null;
            preg_match('~^(\-|\+)([0-9]{2})\:([0-9]{2})$~', $offset, $offsetParts);
            if (!$offsetParts) {
                continue;
            }
            $offsetInt = $offsetParts[1] . ($offsetParts[2] * 60 + $offsetParts[3]);
            $timeCarbon->utcOffset($offsetInt);
            $timeSearch = $timeCarbon->format('H:i');

            DB::table('messages_schedule')
                ->leftJoin('messages', 'messages_schedule.message_id', '=', 'messages.id')
                ->select(
                    'messages_schedule.id as id',
                    'messages_schedule.time',
                    'messages_schedule.message_id',
                    'messages.subject',
                    'messages.body'
                )
                ->where('messages_schedule.time', $timeSearch)
                ->orderBy('messages_schedule.id')
                ->chunkById(100, function ($msgs) use ($info, $tz_ids) {
                    foreach ($msgs as $msg) {
                        if ($info) {
                            $this->line($msg->id);
                            $this->line($msg->time);
                            $this->line(implode(',', $tz_ids));
                            $this->line($msg->subject);
                            $this->line($msg->body);
                        }

                        DB::table('clients')
                            ->select('id', 'name', 'email')
                            ->whereIn('tz_id', $tz_ids)
                            ->orderBy('id')
                            ->chunkById(100, function ($clients) use ($info, $msg) {
                                foreach ($clients as $client) {
                                    if ($info) {
                                        $this->info($client->name);
                                        $this->line($client->email);
                                    }

                                    /**
                                     * Send Email to the Client
                                     */
                                    Mail::send(new ClientMessage($msg, $client));

                                    if ($info) {
                                        $this->info('Sent');
                                    }
                                    // to use SMTP like Mailtrap.io
                                    sleep(1);
                                }
                            });
                    }
                }, 'messages_schedule.id', 'id');

        }
    }
}
