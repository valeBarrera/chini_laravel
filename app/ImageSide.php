<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageSide extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description', 'x', 'y'
    ];

    public function imageTypes()
    {
        return $this->hasMany('App\ImageType', 'image_type_id');
    }
}
