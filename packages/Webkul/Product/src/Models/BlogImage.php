<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Storage;
// use Webkul\Product\Contracts\ProductImage as ProductImageContract;

class BlogImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['path', 'blog_id'];

}