<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description'
    ];

    public function charactCategorys(){
        return $this->hasMany('App\CharacteristicCategory', 'category_id');
    }

    public function products()
    {
        return $this->hasMany('App\Product', 'category_id');
    }

}
