<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Winterhour extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'day', 'time', 'amount_of_courts', 'mixed_doubles', 'made_by', 'status',
    ];
    
    protected $dates = ['deleted_at'];

    public function participants()
    {
        return $this->belongsToMany('App\User')->orderBy('last_name')->orderBy('first_name');
    }

    public function dates()
    {
        return $this->hasMany('App\Date')->orderBy('date');
    }

}
