<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
     use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'path', 'order', 'exercise_id',
    ];
    
    protected $dates = ['deleted_at'];

    public function exercise()
    {
        return $this->belongsTo('App\Exercise');
    }
}
