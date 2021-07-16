<?php

namespace Webkul\Product\Repositories;

use DB;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Models\ProductAttributeValue;

/**
 * Product Repository
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class ProductRepository extends Repository
{
    /**
     * AttributeRepository object
     *
     * @var array
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Attribute\Repositories\AttributeRepository $attributeRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        App $app
    )
    {
        $this->attributeRepository = $attributeRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Product\Contracts\Product';
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        Event::fire('catalog.product.create.before');

        $typeInstance = app(config('product_types.' . $data['type'] . '.class'));

        $product = $typeInstance->create($data);

        Event::fire('catalog.product.create.after', $product);

        return $product;
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        Event::fire('catalog.product.update.before', $id);

        $product = $this->find($id);

        $product = $product->getTypeInstance()->update($data, $id, $attribute);

        if (isset($data['channels']))
            $product['channels'] = $data['channels'];

        Event::fire('catalog.product.update.after', $product);

        return $product;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        Event::fire('catalog.product.delete.before', $id);

        parent::delete($id);

        Event::fire('catalog.product.delete.after', $id);
    }

    /**
     * @param integer $categoryId
     * @return Collection
     */
    public function getAll($categoryId = null)
    {
        DB::enableQueryLog();

        $params = request()->input();
        
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use($params, $categoryId) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());
                
                $locale = request()->get('locale') ?: app()->getLocale();

                $qb = $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->whereRaw('
                                    IF(products.type != "configurable", 
                                    (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                                    , 
                                    ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                                ')
                        ->whereNotNull('product_flat.url_key');
                        
                if ($categoryId)
                    $qb->where('product_categories.category_id', $categoryId);

                if (is_null(request()->input('status')))
                    $qb->where('product_flat.status', 1);

                if (is_null(request()->input('visible_individually')))
                    $qb->where('product_flat.visible_individually', 1);

                $queryBuilder = $qb->leftJoin('product_flat as flat_variants', function($qb) use($channel, $locale) {
                    $qb->on('product_flat.id', '=', 'flat_variants.parent_id')
                        ->where('flat_variants.channel', $channel)
                        ->where('flat_variants.locale', $locale);
                });

                if (isset($params['search']))
                    $qb->where('product_flat.name', 'like', '%' . urldecode($params['search']) . '%');
                
                if (isset($params['category']))
                    $qb->where('product_categories.category_id', $params['category']);

                if (isset($params['sort'])) {
                    $attribute = $this->attributeRepository->findOneByField('code', $params['sort']);

                    if ($params['sort'] == 'price') {
                        if ($attribute->code == 'price') {
                            $qb->orderBy('min_price', $params['order']);
                        } else {
                            $qb->orderBy($attribute->code, $params['order']);
                        }
                    } else {
                        $qb->orderBy($params['sort'] == 'created_at' ? 'product_flat.created_at' : $attribute->code, $params['order']);
                    }
                }
                else
                {
                    $qb->orderBy('product_flat.created_at', 'desc');
                }

                $qb = $qb->leftJoin('products as variants', 'products.id', '=', 'variants.parent_id');

                $qb = $qb->where(function($query1) use($qb) {
                    $aliases = [
                            'products' => 'filter_',
                            'variants' => 'variant_filter_'
                        ];

                    foreach($aliases as $table => $alias) {
                        $query1 = $query1->orWhere(function($query2) use ($qb, $table, $alias) {

                            foreach ($this->attributeRepository->getProductDefaultAttributes(array_keys(request()->input())) as $code => $attribute) {
                                $aliasTemp = $alias . $attribute->code;

                                $qb = $qb->leftJoin('product_attribute_values as ' . $aliasTemp, $table . '.id', '=', $aliasTemp . '.product_id');

                                $column = ProductAttributeValue::$attributeTypeFields[$attribute->type];

                                $temp = explode(',', request()->get($attribute->code));

                                if ($attribute->type != 'price') {
                                    $query2 = $query2->where($aliasTemp . '.attribute_id', $attribute->id);

                                    $query2 = $query2->where(function($query3) use($aliasTemp, $column, $temp) {
                                        foreach($temp as $code => $filterValue) {
                                            if (! is_numeric($filterValue))
                                                continue;

                                            $columns = $aliasTemp . '.' . $column;
                                            $query3 = $query3->orwhereRaw("find_in_set($filterValue, $columns)");
                                        }
                                    });
                                } else {
                                    $query2->where('product_flat.min_price', '>=', core()->convertToBasePrice(current($temp)))
                                        ->where('product_flat.min_price', '<=', core()->convertToBasePrice(end($temp)));
                                }
                            }
                        });
                    }
                });

                $qb = $qb->orderBy('product_inventories.qty', 'desc');
                return $qb->groupBy('product_flat.id');
            })->paginate(isset($params['limit']) ? $params['limit'] : 12);
            
            $i = 0;
            $j = 0;
            $return_results = array();
            foreach($results as $result){
                if ($result->parent_id == null && $result->qty == 0) {
                    $check_variant_inventories = DB::table("products")
                                                    ->where("products.id", $result->id)
                                                    ->leftjoin("product_flat", "product_flat.parent_id", "products.id")
                                                    ->leftjoin("product_inventories", "product_inventories.product_id", "product_flat.id")
                                                    ->selectRaw("SUM(product_inventories.qty) as v_qty")
                                                    ->groupBy("product_inventories.product_id")
                                                    ->get();
                    if (count($check_variant_inventories) > 0 && $check_variant_inventories[0]->v_qty != 0) {
                        $results[$i]->v_qty = $check_variant_inventories[0]->v_qty;
                    }else {
                        $results[$i]->v_qty = NULL;
                    }
                }
                
                $i++;
            }
            
            // dd($return_results, $results);
            return $results;
        // return $return_results;
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
        
        $i = 0;
        if(!empty($product->variants))
        {
            $child_ids = '';
            $var_ids = [];
            foreach($product->variants as $val)
            {
                $var_ids[] = $val->id;
            }
            $child_ids = implode(',',$var_ids);
        }

        $category = DB::table("products")
                        ->where("products.id", $product['id'])
                        ->leftjoin("product_categories", "product_categories.product_id", "products.id")
                        ->leftjoin("category_translations", "category_translations.id", "product_categories.category_id")
                        ->selectRaw("category_translations.name")
                        ->selectRaw("category_translations.slug")
                        ->first();
        
        if (isset($category)) {
            $product['category_slug'] = $category->slug;
            $product['category_name'] = $category->name;
        }  
        else {
            $product['category_slug'] = "";
            $product['category_name'] = "No Category";
        }
        
        $total_sales = DB::table("products")
                            ->leftjoin("order_items", "order_items.sku", "products.sku")
                            ->selectRaw("SUM(order_items.qty_ordered) as total_sales")
                            ->where("products.id", $product['id'])
                            ->where("order_items.qty_canceled",'=', 0)
                            ->groupBy("order_items.sku")
                            ->get();
                            
        if (isset($total_sales[0]->total_sales)) {
            $product['total_sales'] = $total_sales[0]->total_sales;
        } else {
            $product['total_sales'] = 0;
        }
        
        $currency = DB::table("channels")
                        ->where("channels.code", core()->getCurrentChannelCode())
                        ->leftjoin('channel_currencies', 'channel_currencies.channel_id', 'channels.id')
                        ->leftjoin('currencies', 'currencies.id', 'channel_currencies.currency_id')
                        ->selectRaw('currencies.symbol')
                        ->first();
                        
        if(isset($currency->symbol))
        {
            $product['symbol'] = $currency->symbol;
        }
        else {
            $product['symbol'] = '$';
        }
        
        if($product->product->type != 'configurable')
        {
            $check_product_inventories = DB::table("products")
                ->where("products.id", $product['id'])
                ->leftjoin("product_inventories", "product_inventories.product_id", "products.id")
                ->selectRaw("SUM(product_inventories.qty) as p_qty")
                ->selectRaw("(select COALESCE(SUM(order_items.qty_ordered),0) from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status='pending') as product_orders")
                ->get();
        }
        else
        {
            $check_product_inventories = DB::table("products")
                ->where("products.parent_id", $product['id'])
                ->leftjoin("product_inventories", "product_inventories.product_id", "products.id")
                ->selectRaw("SUM(product_inventories.qty) as p_qty")
                ->selectRaw("GROUP_CONCAT(products.id) as child_ids")
                // ->selectRaw("(SELECT COALESCE(SUM(orders.total_qty_ordered),0) FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id IN (".$child_ids.") AND orders.status = 'pending') as product_orders")
                ->selectRaw("(SELECT COALESCE(SUM(order_items.qty_ordered),0) FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = '".$product['id']."' AND orders.status = 'pending') as product_orders")
                ->get();
                // ->selectRaw("(SELECT COALESCE(SUM(orders.total_qty_ordered),0) FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = 'pending') as product_orders")
                // ->get();
        }

        // dd($check_product_inventories);

        if (count($check_product_inventories) > 0) 
        {
            $product['p_qty'] = $check_product_inventories[0]->p_qty - $check_product_inventories[0]->product_orders;
            $product['total_quantity'] = $check_product_inventories[0]->p_qty;
            $product['total_order'] = $check_product_inventories[0]->product_orders;
        }
        else 
        {
            $product['p_qty'] = NULL;
        }
        // dd($product['p_qty']);
        $brand = DB::table("product_attribute_values")
                    ->where("product_attribute_values.product_id", $product['id'])
                    ->leftjoin('attributes', 'attributes.id', 'product_attribute_values.attribute_id')
                    ->leftjoin('attribute_options', 'attribute_options.attribute_id', 'attributes.id')
                    ->leftjoin("attribute_option_translations", "attribute_option_translations.attribute_option_id", "attribute_options.id")
                    ->where("attributes.code", "brand")
                    ->selectRaw('attribute_option_translations.label')
                    ->get();
                    
        if (isset($brand[0]->label)) {
            $product['brand'] = $brand[0]->label;
        }
        else {
            $product['brand'] = 'No Brand';
        }

        
        // if ($product['parent_id'] == null) {
        //     $check_variant_inventories = DB::table("products")
        //                                     ->where("products.id", $product['id'])
        //                                     ->leftjoin("product_flat", "product_flat.parent_id", "products.id")
        //                                     ->leftjoin("product_inventories", "product_inventories.product_id", "product_flat.id")
        //                                     ->selectRaw("SUM(product_inventories.qty) as v_qty")
        //                                     ->groupBy("product_flat.parent_id")
        //                                     ->get();
            
        //     if (count($check_variant_inventories) > 0) {
        //         $product['v_qty'] = $check_variant_inventories[0]->v_qty;
        //     }else {
        //         $product['v_qty'] = NULL;
        //     }
        // }
          
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
     
    public function getOnSaleProducts() {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            // })->get();
            })->paginate(4);

        return $results;
    }
    public function getNewProducts()
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->paginate(3);

        return $results;
    }

    public function Kidsproducts()
    {
         $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.kids', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->paginate(10);

        return $results;
    }

   public function Deal()
   {
     $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.deal', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->paginate(10);

        return $results;
   }

   public function Toprated()
   {
    $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();

                return $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->whereRaw('
                                    IF(products.type != "configurable", 
                                    (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                                    , 
                                    ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                                ')
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.TopRated', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->orderBy('product_id', 'desc');
            })->get(8);

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
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->whereRaw('
                                    IF(products.type != "configurable", 
                                    (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                                    , 
                                    ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                                ')
                        ->orderBy('product_id', 'desc');
            })->paginate(3);

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
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.featured', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->where('product_inventories.qty', '!=', 0)
                        ->orderBy('product_id', 'desc');
            })->paginate(8);

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
                         ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->whereNotNull('product_flat.url_key')
                        ->where('product_flat.name', 'like', '%' . urldecode($term) . '%')
                        ->whereRaw('
                                    IF(products.type != "configurable", 
                                    (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                                    , 
                                    ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                                ')
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
                    ->select('product_flat.*', 'product_inventories.qty')
                        // ->Select('product_inventories.*')
                    ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                    ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
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