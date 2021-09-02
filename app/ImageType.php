<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageType extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description', 'extra_price', 'image_side_id'
    ];


    public function side()
    {
        return $this->belongsTo('App\ImageSide');
    }

}
