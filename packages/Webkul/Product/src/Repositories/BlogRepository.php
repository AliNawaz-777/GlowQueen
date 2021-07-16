<?php

namespace Webkul\Product\Repositories;

use DB;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Models\Blog;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Category\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Storage;
use Webkul\Category\Models\CategoryTranslation;


/**
 * Product Repository
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class BlogRepository extends Repository
{
    /**
     * AttributeRepository object
     *
     * @var array
     */
    protected $attributeRepository;


    /**
     * categoryRepositry object
     *
     * @var array
     */
    protected $categoryRepositry;

    public function __construct(
        AttributeRepository $attributeRepository,
        CategoryRepository $categoryRepositry,
        App $app
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->categoryRepositry = $categoryRepositry;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Product\Models\Blog';
    }
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $record = $this->model->create($data);
        return $record;
    }
    /**
     * @param $id
     * @return mixed
     */
    public function findBlog($id)
    {

        $blog = $this->model->find($id);
        $blog_categories_ids = explode(',', $blog['category']);
        $categories = '';
        $c_ids = array();
        $i = 0;
        $categories_ids = $blog['category'];
        foreach ($blog_categories_ids as $category) {
            if ($category != "") {
                // $cat = $this->categoryRepositry->findCategory($category);
                $cat = CategoryTranslation::where('id', '=', $category)->get();
                $categories .= $cat[0]['name'].',';
                $c_ids[$i] = (int)$category;
            }
        }
        $blog['category'] = $categories;
        // $blog['c_ids'] = $categories_ids;
        $blog['c_ids'] = $c_ids;
        return $blog;
    }
    
    public function update(array $data, $id, $attribute = "id")
    {
        $blog = $this->model->where('id', $id)->update($data);

        return $blog;
    }
    
    /**
     * @param $slug
     * @return mixed
     */
    public function findBySlug($slug)
    {
        $result = $this->model->where('url_key','=',$slug)->get();
        return $result;
    }
    /**
     * @param $id
     * @param $status
     * @return mixed 
     */
    public function updateStatus($id, $status)
    {
        return $this->model->where('id', '=', $id)->update(['status'=>$status]);
    }
    /**
     * @param $id
     * @return mixed
     */
    public function deleteBlog($id)
    {
        return $this->model->where('id', '=', $id)->delete();
    }

    /**
     * @param integer $categoryId
     * @return Collection
     */
    public function getAll()
    {
        $results = Blog::get();
        return $results;
    }

    public function getPost($url_key)
    {
        $results = Blog::with('images')
            ->with('user')
            ->where('url_key','=',$url_key)->first();
        return $results;
    }

    public function getRecentPosts()
    {
        $results = Blog::orderBy('id', 'desc')->take(10)->get();
        return $results;
    }

    public function relatedCategories($category,$currentId){
        $results = Blog::with('images')
            ->with('user')
            ->where('id','<>',$currentId)
            ->where('category', 'like', '%' . $category . '%')->inRandomOrder()->limit(5)->get();
        return $results;
    }

    public function getPreviousPost($id)
    {
        $results = Blog::where('id', '<', $id)->orderBy('id','desc')->first();
        return $results;
    }
    /**
     * Retrive product from slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlugOrFail($slug, $columns = null)
    {
        $product = app('Webkul\Product\Repositories\ProductFlatRepository')->findOneWhere([
                'url_key' => $slug,
                'locale' => app()->getLocale(),
                'channel' => core()->getCurrentChannelCode(),
            ]);

        if (! $product) {
            throw (new ModelNotFoundException)->setModel(
                get_class($this->model), $slug
            );
        }

        return $product;
    }

    /**
     * Returns newly added product
     *
     * @return Collection
     */
    public function getNewProducts()
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                        ->addSelect('product_flat.*')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->paginate(3);

        return $results;
    }

    /**
     * Returns latest products on home page
     *
     * @return Collection
     *

     */
    public function getHomeLatestProducts()
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                        ->addSelect('product_flat.*')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->get();

        return $results;
    }

    /**
     * Returns featured product
     *
     * @return Collection
     */
    public function getFeaturedProducts()
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                        ->addSelect('product_flat.*')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.featured', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->paginate(4);

        return $results;
    }

    /**
     * Search Product by Attribute
     *
     * @return Collection
     */
    public function searchProductByAttribute($term)
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use($term) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                        ->addSelect('product_flat.*')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->whereNotNull('product_flat.url_key')
                        ->where('product_flat.name', 'like', '%' . urldecode($term) . '%')
                        ->orderBy('product_id', 'desc');
            })->paginate(20);

        return $results;
    }

    /**
     * Returns product's super attribute with options
     *
     * @param Product $product
     * @return Collection
     */
    public function getSuperAttributes($product)
    {
        $superAttrbutes = [];

        foreach ($product->super_attributes as $key => $attribute) {
            $superAttrbutes[$key] = $attribute->toArray();

            foreach ($attribute->options as $option) {
                $superAttrbutes[$key]['options'][] = [
                    'id' => $option->id,
                    'admin_name' => $option->admin_name,
                    'sort_order' => $option->sort_order,
                    'swatch_value' => $option->swatch_value,
                ];
            }
        }

        return $superAttrbutes;
    }

    /**
     * Search simple products for grouped product association
     *
     * @param string $term
     * @return \Illuminate\Support\Collection
     */
    public function searchSimpleProducts($term)
    {
        return app(ProductFlatRepository::class)->scopeQuery(function($query) use($term) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                    ->addSelect('product_flat.*')
                    ->addSelect('product_flat.product_id as id')
                    ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                    ->where('products.type', 'simple')
                    ->where('product_flat.channel', $channel)
                    ->where('product_flat.locale', $locale)
                    ->where('product_flat.name', 'like', '%' . urldecode($term) . '%')
                    ->orderBy('product_id', 'desc');
        })->get();
    }
}