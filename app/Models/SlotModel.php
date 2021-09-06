<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SlotModel extends Model
{


    protected $table = "meeting_slot";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['appointment_start', 'appointment_end','status','user_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password',
    // ];

}