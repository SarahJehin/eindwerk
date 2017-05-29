<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'views', 'approved', 'made_by',
    ];
    
    protected $dates = ['deleted_at'];

    public function images()
    {
        return $this->hasMany('App\Image');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function user() {
    	return $this->belongsTo('App\User', 'made_by');
    }
}
