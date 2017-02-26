<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'level',
    ];
    
    protected $dates = ['deleted_at'];
    
    //return all the users associated with a role
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    
}
