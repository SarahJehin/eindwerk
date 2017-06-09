<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

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
        'first_name', 'last_name', 'vtv_nr', 'member_since', 'email', 'gsm', 'tel', 'birth_date', 'gender', 'ranking_singles', 'ranking_doubles', 'image', 'level_id', 'tmp_password', 'password',
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
    
    public function isAdmin()
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->level == 11) {
                return true;
            }
        }
        return false;
    }

    public function isYouthChairman()
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->level == 21) {
                return true;
            }
        }
        return false;
    }

    public function isHeadtrainer()
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->level == 31) {
                return true;
            }
        }
        return false;
    }

    public function isTrainer()
    {
        foreach ($this->roles()->get() as $role) {
            if (in_array($role->level, [30, 31, 32, 33, 34, 35, 36])) {
                return true;
            }
        }
        return false;
    }
    
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
        return $this->belongsToMany('App\Activity')->orderBy('start')->withPivot('status');
    }

    public function paid_activities() {
        return $this->belongsToMany('App\Activity')->orderBy('start')->withPivot('status')->wherePivot('status', 2);
    }
    
    //return all the activities where this user is a helper (code = 2)
    public function activities_as_helper()
    {
        return $this->belongsToMany('App\Activity')->orderBy('start')->withPivot('status')->wherePivot('status', 5);
    }
    
    //return all the activities where this user is a participant (code = 1)
    public function activities_as_participant()
    {
        return $this->belongsToMany('App\Activity')->orderBy('start')->withPivot('status')->wherePivotIn('status', [1, 2]);
    }

    public function activities_as_participant_coming() {
        return $this->belongsToMany('App\Activity')->orderBy('start')->where('start', '>', date('Y-m-d').' 00:00:00')->withPivot('status')->wherePivotIn('status', [1, 2]);
    }

    public function activities_as_participant_past() {
        return $this->belongsToMany('App\Activity')->orderBy('start')->where('start', '<', date('Y-m-d').' 00:00:00')->withPivot('status')->wherePivot('status', 2);
    }

    public function adult_activities_past() {
        return $this->belongsToMany('App\Activity')
                    ->where('activities.status', 1)
                    ->orderBy('start')
                    ->where('start', '<', date('Y-m-d').' 00:00:00')
                    ->whereHas('category', function ($query) {
                        $query->where('root', 'adult');
                    })->withPivot('status')
                    ->wherePivot('status', 2);
    }

    public function youth_activities() {
        return $this->belongsToMany('App\Activity')->orderBy('start')->whereHas('category', function ($query) {
                                        $query->where('root', 'youth');
                                    })->withPivot('status')->wherePivot('status', 2);
    }

    public function youth_activities_past() {
        return $this->belongsToMany('App\Activity')
                    ->where('activities.status', 1)
                    ->orderBy('start')
                    ->where('start', '<', date('Y-m-d').' 00:00:00')
                    ->whereHas('category', function ($query) {
                        $query->where('root', 'youth');
                    })->withPivot('status')
                    ->wherePivot('status', 2);
    }

    public function total_score() {
        $user_with_activity = User::where('id', $this->id)
                                ->has('adult_activities_past')
                                ->with('adult_activities_past')
                                ->first();
        if($user_with_activity) {
            $activities_count = $user_with_activity->adult_activities_past
                                                    ->count();

            $bonus_points = DB::table('activity_user')
                            ->where('user_id', $this->id)
                            ->where('activity_user.status', 2)
                            ->join('activities', 'activities.id', '=', 'activity_user.activity_id')
                            ->where('activities.start', '<', date('Y-m-d') . ' 00:00:00')
                            ->join('categories', 'categories.id', '=', 'activities.category_id')
                            ->where('categories.root', '=', 'adult')
                            ->sum('activity_user.extra_points');
            $total_score = $activities_count + $bonus_points;
        }
        else {
            $total_score = 0;
        }
        
        return $total_score;
    }

    public function total_youth_score() {
        $user_with_activity = User::where('id', $this->id)
                                ->has('youth_activities_past')
                                ->with('youth_activities_past')
                                ->first();
        if($user_with_activity) {
            $activities_count = $user_with_activity->youth_activities_past
                                                    ->count();

            $bonus_points = DB::table('activity_user')
                            ->where('user_id', $this->id)
                            ->where('activity_user.status', 2)
                            ->join('activities', 'activities.id', '=', 'activity_user.activity_id')
                            ->where('activities.start', '<', date('Y-m-d') . ' 00:00:00')
                            ->join('categories', 'categories.id', '=', 'activities.category_id')
                            ->where('categories.root', '=', 'youth')
                            ->sum('activity_user.extra_points');
            $total_score = $activities_count + $bonus_points;
        }
        else {
            $total_score = 0;
        }
        
        return $total_score;
    }


    //winterhours
    public function winterhours()
    {
        return $this->belongsToMany('App\Winterhour');
    }

    public function dates() 
    {
        return $this->belongsToMany('App\Date')->withPivot('available', 'assigned');
    }

    //exercises
    public function exercises()
    {
        return $this->hasMany('App\Exercise');
    }
    
}
