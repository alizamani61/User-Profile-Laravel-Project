<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseSkill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'group', 
    ];
    
    public function skills() {

        return $this->belongsToMany(Skill::class,'users_skills');

    }
}
