<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'color', 'image', 'root'
    ];
    
    protected $dates = ['deleted_at'];
    
    //return the activities associated with this category
    public function activities()
    {
        return $this->hasMany('App\Activity');
    }
    
}
