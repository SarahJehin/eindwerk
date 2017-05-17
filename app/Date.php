<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Date extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date', 'winterhour_id',
    ];
    
    protected $dates = ['deleted_at'];


    public function winterhour()
    {
        return $this->belongsTo('App\Winterhour');
    }

    public function users() {
    	return $this->belongsToMany('App\User')->withPivot('available', 'assigned');
    }
}
