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
        'day', 'time', 'vtv_nr', 'amount_of_courts', 'mixed_doubles', 'made_by',
    ];
    
    protected $dates = ['deleted_at'];
}
