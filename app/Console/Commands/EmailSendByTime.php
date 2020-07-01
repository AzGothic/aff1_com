<?php

namespace App\Console\Commands;

use App\Mail\ClientMessage;
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

        DB::table('messages_schedule')
            ->leftJoin('clients', 'messages_schedule.client_id', '=', 'clients.id')
            ->leftJoin('messages', 'messages_schedule.message_id', '=', 'messages.id')
            ->select(
                'messages_schedule.id as id',
                'messages_schedule.time',
                'messages_schedule.client_id',
                'clients.name as client_name',
                'clients.email as client_email',
                'messages_schedule.message_id',
                'messages.subject as message_subject',
                'messages.body as message_body'
            )
            ->where('messages_schedule.time', $time)
            ->chunkById(100, function ($msgs) use ($info) {
                foreach ($msgs as $msg) {
                    if ($info) {
                        $this->line($msg->id);
                        $this->line($msg->time);
                        $this->line($msg->client_name);
                        $this->line($msg->client_email);
                        $this->line($msg->message_subject);
                        $this->line($msg->message_body);
                    }

                    /**
                     * Send Email to the Client
                     */
                    Mail::send(new ClientMessage($msg));

                    if ($info) {
                        $this->info('Sent');
                        $this->line('');
                    }
                }
            }, 'messages_schedule.id', 'id');
    }
}
