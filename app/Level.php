<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'number', 'name',
    ];
    
    protected $dates = ['deleted_at'];
    
    //return all the users associated with this level
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    
}
