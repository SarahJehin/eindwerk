<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'vtv_nr', 'email', 'gsm', 'birth_date', 'gender', 'ranking', 'image', 'level_id', 'password',
    ];
    
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    //return all the roles associated with this user
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
    
    //return the level associated with this user
    public function level()
    {
        return $this->belongsTo('App\Level');
    }
    
    //return all the activities associated with this user
    public function activities()
    {
        return $this->belongsToMany('App\Activity')->withPivot('status');
    }

    public function paid_activities() {
        return $this->belongsToMany('App\Activity')->withPivot('status')->wherePivot('status', 2);
    }
    
    //return all the activities where this user was a helper (code = 2)
    public function activities_as_helper()
    {
        return $this->belongsToMany('App\Activity')->withPivot('status')->wherePivot('status', 5);
    }
    
    //return all the activities where this user was a participant (code = 1)
    public function activities_as_participant()
    {
        return $this->belongsToMany('App\Activity')->withPivot('status')->wherePivot('status', 1);
    }
    
}
