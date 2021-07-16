<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Storage;
// use Webkul\Product\Contracts\ProductImage as ProductImageContract;

class Comment extends Model
{
    public $timestamps = false;

    protected $fillable = ['post_id', 'full_name', 'email', 'comment', 'comment_date'];
}