<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\Admin;

// use Webkul\Attribute\Models\AttributeFamilyProxy;
// use Webkul\Category\Models\CategoryProxy;
// use Webkul\Attribute\Models\AttributeProxy;
// use Webkul\Inventory\Models\InventorySourceProxy;
// use Webkul\Product\Contracts\Blog as BlogContract;

class Blog extends Model
{
    protected $fillable = ['blog_title', 'short_description', 'blog_description', 'category', 'tags', 'status', 'url_key','blog_date','admin_id', 'meta_title', 'meta_description', 'meta_keyword'];

    protected $typeInstance;

    public function images(){
        return $this->hasMany(BlogImage::class,'blog_id');
    }
    public function user(){
        return $this->hasOne(Admin::class,'id','admin_id');
    }
}