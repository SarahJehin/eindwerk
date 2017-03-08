<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title', 'description', 'poster', 'extra_url', 'startdate', 'deadline', 'location', 'min_participants', 'max_participants', 'helpers', 'price', 'youth_adult', 'is_visible', 'category_id', 'made_by_id', 'owner_id',
    ];
    
    protected $dates = ['deleted_at'];
    
    
    //return all the users associated with an activity
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    
    //return all the helpers (code = 2)
    public function helpers()
    {
        return $this->belongsToMany('App\User')->withPivot('helper_participant')->wherePivot('helper_participant', 2);
    }
    
    //return all the participants (code = 1)
    public function participants()
    {
        return $this->belongsToMany('App\User')->withPivot('helper_participant')->wherePivot('helper_participant', 1);
    }
    
    //return all the participants (code = 1) who have already paid (code = 1)
    public function paid_participants()
    {
        return $this->belongsToMany('App\User')->withPivot('paid', 'helper_participant')->wherePivot('helper_participant', 1)->wherePivot('paid', 1);
    }
    
    //return all the participants (code = 1) who haven't paid yet (code = 0)
    public function not_paid_participants()
    {
        return $this->belongsToMany('App\User')->withPivot('paid', 'helper_participant')->wherePivot('helper_participant', 1)->wherePivot('paid', 0);
    }
    
    //return the category of this activity
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
}
