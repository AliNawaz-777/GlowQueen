<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\Admin;

// use Webkul\Attribute\Models\AttributeFamilyProxy;
// use Webkul\Category\Models\CategoryProxy;
// use Webkul\Attribute\Models\AttributeProxy;
// use Webkul\Inventory\Models\InventorySourceProxy;
// use Webkul\Product\Contracts\Blog as BlogContract;

class Testimonail extends Model
{
    protected $fillable = ['testimonail_title', 'short_description','name','admin_id'];

    protected $typeInstance;

    public function images(){
        return $this->hasMany(TestimonailImage::class,'testimonail_id');
    }
    public function user(){
        return $this->hasOne(Admin::class,'id','admin_id');
    }
}