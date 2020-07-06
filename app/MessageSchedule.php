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
        'message_id', 'time',
    ];

    public function message()
    {
        return $this->hasOne('App\Message', 'id', 'message_id');
    }
}
