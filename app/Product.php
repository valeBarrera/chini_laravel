<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'name', 'description', 'brand_id', 'category_id', 'price', 'stock', 'is_custom', 'has_color', 'color', 'state', 'img'
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function characteristicsCustom($order_id)
    {
        return $this->hasMany('App\CharacteristicProductCustom', 'product_id')->where('order_product_id',$order_id)->get();
    }

    public function characteristicsNative()
    {
        return $this->hasMany('App\CharacteristicProductNative', 'product_id');
    }
}
