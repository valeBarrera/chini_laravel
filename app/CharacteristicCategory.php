<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharacteristicCategory extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description', 'is_custom', 'is_optional', 'is_image', 'is_design', 'is_text', 'price', 'category_id', 'state', 'image_type_id'
    ];

    public function category(){
        return $this->belongsTo('App\Category');
    }

    public function imageType()
    {
        return $this->belongsTo('App\ImageType');
    }

    public function typeCharactCategorys()
    {
        return $this->hasMany('App\TypeCharacteristicCategory', 'characteristic_categories_id');
    }
}
