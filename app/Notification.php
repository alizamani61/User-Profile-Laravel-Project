<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user', 'to_user', 'note_title', 'note_text', 'create_date', 'create_time', 'view_user', 'view_date', 'view_time', 'target_url',
    ];
    
    public $timestamps = false;
}
