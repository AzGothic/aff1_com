<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MessageSchedule extends Model
{
    protected $table = 'messages_schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'message_id', 'time',
    ];

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }

    public function message()
    {
        return $this->hasOne('App\Message', 'id', 'message_id');
    }

    /**
     * Retrieve all Messages for Clients for defined/current time
     *
     * @param string|null $time - time criteria for retrieve records, example, '01:59', '12:00', '13:02' etc. Default is current time
     * @return \Illuminate\Support\Collection
     *  messages_schedule_id
     *  time
     *  client_id
     *  client_name
     *  client_email
     *  message_id
     *  message_subject
     *  message_body
     */
    public static function getClientsMessagesByTime($time = null)
    {
        $time = $time ?: date('H:i');

        return DB::table('messages_schedule')
            ->leftJoin('clients', 'messages_schedule.client_id', '=', 'clients.id')
            ->leftJoin('messages', 'messages_schedule.message_id', '=', 'messages.id')
            ->select(
                'messages_schedule.id as messages_schedule_id',
                'messages_schedule.time',
                'messages_schedule.client_id',
                'clients.name as client_name',
                'clients.email as client_email',
                'messages_schedule.message_id',
                'messages.subject as message_subject',
                'messages.body as message_body'
            )
            ->where('messages_schedule.time', $time)
            ->get();
    }
}
