<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Permissions\HasPermissionsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use HasPermissionsTrait; //Import The Trait
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'username', 'avatar', 'image_url', 'country_id', 'province_id', 'city_id', 'gender', 'marital_status', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * 
     * @return type
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }
    
    /**
     * 
     * @return type
     */
    public function likes()
    {
        return $this->belongsToMany(UsersLike::class, 'users_likes');
    }
    
    /**
     * 
     * @return type
     */
    public function views()
    {
        return $this->belongsToMany(UsersView::class, 'users_views');
    }
    
    /**
     * 
     * @return type
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }
    
    
    /**
     * 
     * @return type
     */
    public function skills()
    {
        return $this->belongsToMany(BaseSkill::class, 'users_skills');
    }
    
    /**
     * 
     * @return type
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
    
    /**
     * 
     * @return type
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    
    /**
     * 
     * @return type
     */
    public function resume()
    {
        return $this->belongsTo(Resume::class, 'resumes');
    }
    
    /**
     * 
     * @return type
     */
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolios');
    }
}
