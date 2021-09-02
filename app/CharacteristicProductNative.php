<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharacteristicProductNative extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function typeCharactCategory()
    {
        return $this->belongsTo('App\TypeCharacteristicCategory', 'type_characteristic_category_id');
    }
}
