<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Storage;
// use Webkul\Product\Contracts\ProductImage as ProductImageContract;

class TestimonailImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['path', 'testimonail_id'];

}