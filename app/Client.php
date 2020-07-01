<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'tz_id',
    ];

    public function timezone()
    {
        return $this->hasOne('App\Timezone', 'id', 'tz_id');
    }
}
