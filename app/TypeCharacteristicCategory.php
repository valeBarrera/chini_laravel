<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeCharacteristicCategory extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description', 'has_color', 'has_img', 'color', 'extra_price', 'state', 'characteristic_categories_id', 'design_leaf'
    ];

    public function charactCategory()
    {
        return $this->belongsTo('App\CharacteristicCategory', 'characteristic_categories_id');
    }

    public function characteristicsNative()
    {
        return $this->hasMany('App\CharacteristicProductNative', 'type_characteristic_category_id');
    }
}
