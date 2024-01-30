<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersView extends Model
{
    
    protected $fillable = [
        'user_id', 'view_user_id',
    ];
}
