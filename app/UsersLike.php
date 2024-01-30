<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersLike extends Model
{
    
    protected $fillable = [
        'user_id', 'like_user_id',
    ];
}
