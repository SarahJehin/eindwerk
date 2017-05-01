<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title', 'description', 'poster', 'extra_url', 'startdate', 'deadline', 'location', 'latitude', 'longitude', 'min_participants', 'max_participants', 'helpers', 'price', 'youth_adult', 'is_visible', 'category_id', 'made_by_id', 'owner_id',
    ];
    
    protected $dates = ['deleted_at'];
    
    
    //return all the users associated with an activity
    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('status');
    }
    
    //return all the helpers (code = 5)
    public function helpers()
    {
        return $this->belongsToMany('App\User')->withPivot('status')->wherePivot('status', 5);
    }
    
    //return all the participants (code = 1 (not paid) en 2 (paid))
    public function participants()
    {
        return $this->belongsToMany('App\User')->withPivot('status', 'signed_up_by')->wherePivotIn('status', [1, 2])->orderBy('last_name')->orderBy('first_name');
    }
    
    //return all the participants who have already paid (code = 2)
    public function paid_participants()
    {
        return $this->belongsToMany('App\User')->withPivot('status')->wherePivot('status', 2);
    }
    
    //return all the participants (code = 1)
    public function not_paid_participants()
    {
        return $this->belongsToMany('App\User')->withPivot('status')->wherePivot('status', 1);
    }
    
    //return the category of this activity
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
}
