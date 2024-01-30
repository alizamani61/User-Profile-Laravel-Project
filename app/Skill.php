<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_skills';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'skill_id', 
    ];
    
    
    /**
     * 
     * @return type
     */
    public function users() {

        return $this->belongsToMany(User::class,'users_skills');

    }
}
