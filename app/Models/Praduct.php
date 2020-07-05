<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Praduct extends Model
{
    protected $table = 'product';

    public function category()
    {
        return $this->hasOne('App\Models\Category',"id","cat_id");
    }
    public function brand()
    {
        return $this->hasOne('App\Models\Brand',"id","brand_id");
    }
}
