<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingDetails extends Model
{
    use HasFactory;

    protected $table = "meeting_details";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'company_name', 'subject', 'meeting_channel_id', 'status', 'slot_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}