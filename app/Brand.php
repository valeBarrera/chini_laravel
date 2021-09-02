<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description'
    ];

    public function products()
    {
        return $this->hasMany('App\Product', 'brand_id');
    }

}
