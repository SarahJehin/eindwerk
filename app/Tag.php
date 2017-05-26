<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name',
    ];
    
    protected $dates = ['deleted_at'];

    public function exercises()
    {
        return $this->belongsToMany('App\Exercise');
    }
}
