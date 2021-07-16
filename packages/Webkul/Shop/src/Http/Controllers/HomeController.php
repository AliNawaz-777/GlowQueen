<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Core\Repositories\SliderRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Helpers\ProductImage;
use Webkul\Product\Helpers\ProductType;
use Webkul\Product\Helpers\ConfigurableOption;
use DB;
use Session;

/**
 * Home page controller
 *
 * @author    Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
 class HomeController extends Controller
{
    protected $_config;

    /**
     * SliderRepository object
     *
     * @var Object
    */
    protected $sliderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Core\Repositories\SliderRepository $sliderRepository
     * @return void
    */
    public function __construct(SliderRepository $sliderRepository)
    {
        $this->_config = request('_config');

        $this->sliderRepository = $sliderRepository;
    }

    /**
     * loads the home page for the storefront
     * 
     * @return \Illuminate\View\View 
     */
    public function index()
    {
        $currentChannel = core()->getCurrentChannel();
            
        $sliderData = $this->sliderRepository->findByField('channel_id', $currentChannel->id)->toArray();
        
        return view($this->_config['view'], compact('sliderData'));
    }
    
     public function indexDemo()
    {
        $currentChannel = core()->getCurrentChannel();
        
        $sliderData = $this->sliderRepository->findByField('channel_id', $currentChannel->id)->toArray();

        return view($this->_config['view'], compact('sliderData'));
    }
    
    /**
     * get sales products
     */
    public function getSalesProducts()
    {
        DB::enableQueryLog();

        $currency = DB::table('currencies')->where('code', Session::get('currency'))->selectRaw('symbol')->first();
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();
                $product_id = '';
                
                $qry =  $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.sale', 1)
                        ->whereRaw('
                                    IF(products.type != "configurable", 
                                    (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                                    , 
                                    ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                                ')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale);
                        if (isset($_GET['sales_exclude']) && $_GET['sales_exclude'] <> '')
                        {
                            $ids = rtrim($_GET['sales_exclude'],',');
                            $qry->whereNotIn('product_flat.id',  explode(",",$ids));
                        }
                        $qry->orderBy(DB::Raw("RAND()"));
                        return $qry;
            // })->get();
            })->paginate(4);

           // print_r($results);
            // print_r(DB::getQueryLog());
            // exit;
            
            $total_records = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();
                $product_id = '';
                

                $qry = $query->distinct()
                        ->select('product_flat.*', 'product_inventories.qty','products.sku')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.sale', 1)
                        ->whereRaw('
                                    IF(products.type != "configurable", 
                                    (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                                    , 
                                    ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                                ')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale);
                        if (isset($_GET['sales_exclude']) && $_GET['sales_exclude'] <> '')
                        {
                            $ids = rtrim($_GET['sales_exclude'],',');
                            $qry->whereNotIn('product_flat.id',  explode(",",$ids));
                        }
                        $qry->orderBy(DB::Raw("RAND()"));
                        return $qry;
            })->get();
            $total_records = count($total_records);

        $html = '';
        $not_in = '';
        if (count($results) > 0) {
            $html .= '<div class="related-products sale-products">';
            $html .= '<div class="on_sale_section">';
            foreach ($results as $result)
            {
                $not_in .= $result->id.',';
                
                $productImageHelper = new ProductImage();
                $productBaseImage = $productImageHelper->getProductBaseImage($result);
                $type = new ProductType();
                $variant_type = $type->hasVariants($result->type);
                $html .= '<div class="sale_content">';
                $html .= '<div class="product-detail box-spacing">';
                $html .= '<a href="'.route('shop.products.index', $result->url_key).'">';
                $html .= '<div class="img-wrap">';
                $html .= '<img src="'. $productBaseImage['medium_image_url'] .'" onerror="this.src='.asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png').'" alt="" class="img-fluid">';
                $html .= '<div class="sku-wrapper">'.$result->sku.'</div>';
                
                $html .= '</div>';
                $html .= '<div class="box-bottom-wrap">';
                $html .= '<div class="content-wrap"><p>'. $result->name .'</p></div>';
                $html .= '<div class="price-holder">';

                $price = $this->getProductPrice($variant_type,$currency->symbol,$result);
                $html .= $price;

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>';
                $html .= '<div class="btn-holder">';
                $html .= '<form data-generated="' . $result->product_id . '" class="addToCart" action="' .route('cart.add', $result->product_id). '" method="POST">';
                $html .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
                $html .= '<input type="hidden" name="product_id" value="'. $result->product_id. '">';
                $html .= '<input type="hidden" name="quantity" value="1">';
                if ($result->isSaleable()){
                    $disabled = '';
                }else {
                    $disabled = '';
                    // $disabled = 'disabled';
                }
                if ($result->isSaleable(1)){
                    $disabled_ = '';
                }else {
                    // $disabled_ = 'disabled';
                    $disabled_ = '';
                }
                $html .= '<button name="add_to_cart" type="submit" class="btn btn-theme-orange" '.$disabled.'><span>Add to Cart</span></button>';

                $html .= '<button id="buy-now-d" name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" '.$disabled_.'><span>Buy Now</span></button>';
               // $html .= '<a href="'.route('shop.products.index', $result->url_key).'" class="btn btn-theme-white"'. $disabled_ .'><span>Buy Now</span></a>';
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $last_id = $result->id;
            }

            $html .= '</div>';
            $html .= '</div>';
            if ($total_records > 4) {
                $html .= '<div id="saleLoader" class="load-more saleLoader"><a href="javascript:void(0);" data-id="'.$last_id.'" onClick="getSalesProducts('.$last_id.');">Load More</a></div>';
            }
            $html .= '<input type="hidden" value="'.rtrim($not_in,',').'" class="sales_exclude" />';
        }
        else
        {
            //$html .= '<div class="no-products">No Product Found</div>';
            $html .= '';
        }
        
        
        echo $html;
    }
    
    /**
     * get new products
     */
    public function getNewProducts() {
        
        $currency = DB::table('currencies')->where('code', Session::get('currency'))->selectRaw('symbol')->first();
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();
                $product_id = '';
                

                $qry = $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                          ->selectRaw('(select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending") as order_placed')
                        
                        ->selectRaw('IF(products.type != "configurable",product_inventories.qty
                                    ,
                                    (SELECT sum(prod_inv.qty) FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) ) as prod_quantity')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        // ->whereRaw('
                        //             IF(products.type != "configurable", 
                        //             (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                        //             , 
                        //             ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                        //         ')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale);

                        if (isset($_GET['newarrival_exclude']) && $_GET['newarrival_exclude'] <> '')
                        {
                            $ids = rtrim($_GET['newarrival_exclude'],',');
                            $qry->whereNotIn('product_flat.id',  explode(",",$ids));
                        }
                        $qry->orderBy(DB::Raw("RAND()"));

                        return $qry;
            // })->get();
            })->paginate(8);
            
            $total_records = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();
                $product_id = '';
                

                $qry = $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        ->where('product_flat.new', 1)
                        // ->whereRaw('
                        //             IF(products.type != "configurable", 
                        //             (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                        //             , 
                        //             ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                        //         ')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale);

                        if (isset($_GET['newarrival_exclude']) && $_GET['newarrival_exclude'] <> '')
                        {
                            $ids = rtrim($_GET['newarrival_exclude'],',');
                            $qry->whereNotIn('product_flat.id',  explode(",",$ids));
                        }
                        $qry->orderBy(DB::Raw("RAND()"));

                        return $qry;
            })->get();
            $total_records = count($total_records);

        $html = '';
        $not_in = '';
        if (count($results) > 0) {
            $html .= '<div class="related-products sale-products">';
            $html .= '<div class="new_product_section">';
            foreach ($results as $result) {
                
                $outOfStock = 0;
                if($result->prod_quantity == 0 || $result->order_placed >= $result->prod_quantity)
                {
                    $outOfStock = 1;
                } 
                
                $not_in .= $result->id.',';

                $productImageHelper = new ProductImage();
                $productBaseImage = $productImageHelper->getProductBaseImage($result);
                $type = new ProductType();
                $variant_type = $type->hasVariants($result->type);
                $html .= '<div class="new_product_content">';
                $html .= '<div class="product-detail box-spacing">';
                if(!$outOfStock)
                {
                    $html .= '<a href="'.route('shop.products.index', $result->url_key).'">';
                }
                else
                {
                    $html .= '<a class="link-disabled" href="javascript:void(0)">';
                }
                $html .= '<div class="img-wrap">';
                $html .= '<img src="'. $productBaseImage['medium_image_url'] .'" onerror="this.src='.asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png').'" alt="" class="img-fluid">';
                $html .= '<div class="sku-wrapper">'.$result->sku.'</div>';
                
                $html .= '</div>';
                $html .= '<div class="box-bottom-wrap">';
                $html .= '<div class="content-wrap"><p>'. $result->name .'</p></div>';
                $html .= '<div class="price-holder">';

                $price = $this->getProductPrice($variant_type,$currency->symbol,$result);
                
                $html .= $price;
                

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>';
                $html .= '<div class="btn-holder">';
                $html .= '<form data-generated="' . $result->product_id . '" class="addToCart" action="' .route('cart.add', $result->product_id). '" method="POST">';
                $html .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
                $html .= '<input type="hidden" name="product_id" value="'. $result->product_id. '">';
                $html .= '<input type="hidden" name="quantity" value="1">';
                if ($result->isSaleable()){
                    $disabled = '';
                }else {
                    $disabled = '';
                    // $disabled = 'disabled';
                }
                if ($result->isSaleable(1)){
                    $disabled_ = '';
                }else {
                    // $disabled_ = 'disabled';
                    $disabled_ = '';
                }
                if(!$outOfStock)
                {
                    $html .= '<button name="add_to_cart" type="submit" class="btn btn-theme-orange" '.$disabled.'><span>Add to Cart</span></button>';
                    $html .= '<button id="buy-now-d" name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" '.$disabled_.'><span>Buy Now</span></button>';   
                }
                else
                {
                    $html .= '<button type="button" class="btn btn-danger" '.$disabled_.'><span>Out Of Stock</span></button>'; 
                }
                
                //$html .= '<a href="'.route('shop.products.index', $result->url_key).'" class="btn btn-theme-white"'. $disabled_ .'><span>Buy Now</span></a>';
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $last_id = $result->id;
            }

            $html .= '</div>';
            $html .= '</div>';
            if ($total_records > 8) {
                $html .= '<div id="saleLoader" class="load-more newProductsLoader"><a href="javascript:void(0);" data-id="'.$last_id.'" onClick="getNewProducts('.$last_id.');">Load More</a></div>';
            }
            $html .= '<input type="hidden" value="'.rtrim($not_in,',').'" class="newarrival_exclude" />';
        }
        else {
            //$html .= '<div class="no-products">No Product Found</div>';
            $html .= '';
        }

        

        echo $html;
    }
    
    /**
     * getShopCategoryProducts
     */
    public function getShopCategoryProducts() {
        DB::enableQueryLog();
        $currency = DB::table('currencies')->where('code', Session::get('currency'))->selectRaw('symbol')->first();
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();
                $product_id = '';
                

                $qry = $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                          ->selectRaw('(select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending") as order_placed')
                        
                        ->selectRaw('IF(products.type != "configurable",product_inventories.qty
                                    ,
                                    (SELECT sum(prod_inv.qty) FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) ) as prod_quantity')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        // ->whereRaw('
                        //             IF(products.type != "configurable", 
                        //             (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                        //             , 
                        //             ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                        //         ')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale);
                        if (isset($_GET['shop_exclude']) && $_GET['shop_exclude'] <> '')
                        {
                            $ids = rtrim($_GET['shop_exclude'],',');
                            $qry->whereNotIn('product_flat.id',  explode(",",$ids));
                        }
                        if (isset($_GET['category_id']) && $_GET['category_id'] <> '')
                        {
                            $qry->where('product_categories.category_id', $_GET['category_id']);
                        }
                        if (isset($_GET['cat_search']) && $_GET['cat_search'] <> '')
                        {
                            $qry->where('product_flat.name', 'like', '%' . $_GET['cat_search'] . '%');
                        }
                        if (isset($_GET['price_range']) && $_GET['price_range'] <> '')
                        {
                            $price = explode(",",$_GET['price_range']);
                            $qry->whereBetween('product_flat.price', $price);
                        }
                        if (isset($_GET['sort_filter']) && $_GET['sort_filter'] <> '')
                        {
                            $sort = explode(",",$_GET['sort_filter']);
                            $qry->orderBy('product_flat.'.$sort[0].'',$sort[1]);
                        }
                        

                        return $qry;
            })->paginate(8);
            // dd(DB::getQueryLog());
            $total_records = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

                $locale = request()->get('locale') ?: app()->getLocale();
                $product_id = '';
                

                $qry = $query->distinct()
                         ->select('product_flat.*', 'product_inventories.qty')
                        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
                        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.status', 1)
                        ->where('product_flat.visible_individually', 1)
                        // ->whereRaw('
                        //             IF(products.type != "configurable", 
                        //             (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
                        //             , 
                        //             ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
                        //         ')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale);
                        if (isset($_GET['shop_exclude']) && $_GET['shop_exclude'] <> '')
                        {
                            $ids = rtrim($_GET['shop_exclude'],',');
                            $qry->whereNotIn('product_flat.id',  explode(",",$ids));
                        }
                        if (isset($_GET['category_id']) && $_GET['category_id'] <> '')
                        {
                            $qry->where('product_categories.category_id', $_GET['category_id']);
                        }
                        if (isset($_GET['cat_search']) && $_GET['cat_search'] <> '')
                        {
                            $qry->where('product_flat.name', 'like', '%' . $_GET['cat_search'] . '%');
                        }
                        if (isset($_GET['price_range']) && $_GET['price_range'] <> '')
                        {
                            $price = explode(",",$_GET['price_range']);
                            $qry->whereBetween('product_flat.price', $price);
                        }
                        if (isset($_GET['sort_filter']) && $_GET['sort_filter'] <> '')
                        {
                            $sort = explode(",",$_GET['sort_filter']);
                            $qry->orderBy('product_flat.'.$sort[0].'',$sort[1]);
                        }
                        
                        
                        return $qry;
            })->get();
            $total_records = count($total_records);

        $html = '';
        $not_in = '';
            
        if (count($results) > 0) 
        {
            $html .= '<div class="related-products sale-products">';
            $html .= '<div class="new_product_section">';
            foreach ($results as $result) {
                
                $outOfStock = 0;
                if($result->prod_quantity == 0 || $result->order_placed >= $result->prod_quantity)
                {
                    $outOfStock = 1;
                } 
                $not_in .= $result->id.',';

                $productImageHelper = new ProductImage();
                $productBaseImage = $productImageHelper->getProductBaseImage($result);
                $type = new ProductType();
                $variant_type = $type->hasVariants($result->type);
                $html .= '<div class="new_product_content">';
                $html .= '<div class="product-detail box-spacing">';
                if(!$outOfStock)
                {
                    $html .= '<a href="'.route('shop.products.index', $result->url_key).'">';
                }
                else
                {
                    $html .= '<a class="link-disabled" href="javascript:void(0)">';
                }
                $html .= '<div class="img-wrap">';
                $html .= '<img src="'. $productBaseImage['medium_image_url'] .'" onerror="this.src='.asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png').'" alt="" class="img-fluid">';
                $html .= '<div class="sku-wrapper">'.$result->sku.'</div>';
                
                $html .= '</div>';
                $html .= '<div class="box-bottom-wrap">';
                $html .= '<div class="content-wrap"><p>'. $result->name .'</p></div>';
                $html .= '<div class="price-holder">';

                $price = $this->getProductPrice($variant_type,$currency->symbol,$result);
                
                $html .= $price;
                

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>';
                $html .= '<div class="btn-holder">';
                $html .= '<form data-generated="' . $result->product_id . '" class="addToCart" action="' .route('cart.add', $result->product_id). '" method="POST">';
                $html .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
                $html .= '<input type="hidden" name="product_id" value="'. $result->product_id. '">';
                $html .= '<input type="hidden" name="quantity" value="1">';
                $disabled = '';
                $disabled_ = '';
                if(!$outOfStock)
                {
                    $html .= '<button name="add_to_cart" type="submit" class="btn btn-theme-orange" '.$disabled.'><span>Add to Cart</span></button>';
                    $html .= '<button id="buy-now-d" name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" '.$disabled_.'><span>Buy Now</span></button>';  
                }
                else
                {
                    $html .= '<button type="button" class="btn btn-danger" '.$disabled_.'><span>Out Of Stock</span></button>'; 
                }
                
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $last_id = $result->id;
            }

            $html .= '</div>';
            $html .= '</div>';
            if ($total_records > 8) 
            {
                $html .= '<span id="last-show-id" data-id="'.$last_id.'"></span>';
                $html .= '<div id="saleLoader" class="load-more newProductsLoader"><a href="javascript:void(0);" data-id="'.$last_id.'" onClick="getShopCategoryProducts('.$last_id.');">Load More</a></div>';
            }
            $html .= '<input type="hidden" value="'.rtrim($not_in,',').'" class="shop_exclude" />';
        }
        else 
        {
            $html .= '';
        }

        echo $html;
    }


    /**
     * loads the home page for the storefront
     */
    public function notFound()
    {
        abort(404);
    }
    //This function is writen for get varient minimum price to show on home page
    public function getProductPrice($type = null,$currency=null,$product)
    {
        $price = 0;
        $variants = [];
        $varient_min_price = 0;
        if($type)
        {
            foreach ($product->variants as $key => $variant) 
            {
                $discount = $variant->special_price;
                
                $start_date = ($variant->special_price_from != NULL) ? strtotime($variant->special_price_from) : 0;
                $end_date = ($variant->special_price_to != NULL) ? strtotime($variant->special_price_to) : 0;
                $now = strtotime(now());

                if($discount > 0)
                {
                    if($start_date > 0 && $end_date == 0)
                    {
                        if(($now >= $start_date))
                        {
                            $variants[$key] = $discount;
                        }
                        else
                        {
                            $variants[$key] = $variant->price;
                        }
                    }
                    elseif($start_date == 0 && $end_date > 0)
                    {
                        if(($now <= $end_date))
                        {
                            $variants[$key] = $discount;
                        }
                        else
                        {
                            $variants[$key] = $variant->price;
                        }
                    }
                    elseif($start_date == 0 && $end_date == 0)
                    {
                        $variants[$key] = $discount;
                    }
                    elseif($start_date > 0 && $end_date > 0 && ($now >= $start_date && $now <= $end_date))
                    {
                        $variants[$key] = $discount;
                    }
                    else
                    {
                        $variants[$key] = $variant->price;
                    }
                }
                else
                {
                    $variants[$key] = $variant->price;
                }
            }
            $price = round((isset($variants) && !empty($variants)) ? min($variants) : '').'</h2>';
        }
        else
        {
            $discount_price = $product->special_price;
                
            $start_date = ($product->special_price_from != NULL) ? strtotime($product->special_price_from) : 0;
            $end_date = ($product->special_price_to != NULL) ? strtotime($product->special_price_to) : 0;
            $now = strtotime(now());

            if($discount_price > 0)
            {
                $price = '';
                $disc = '';
                $discount = $product->price - $discount_price;
                $discount = ($discount / $product->price) * 100;
                $disc = '<h3><span>'. $currency .' '. round($product->price) .'</span> -' . round($discount) . '%' .'</h3>';

                if($start_date > 0 && $end_date == 0)
                {
                    if(($now >= $start_date))
                    {
                        $price = round($discount_price).'</h2>';
                    }
                    else
                    {
                        $price = round($product->price).'</h2>';
                        $disc = '';
                    }
                }
                elseif($start_date == 0 && $end_date > 0)
                {
                    if(($now <= $end_date))
                    {
                        $price = round($discount_price).'</h2>';
                    }
                    else
                    {
                        $price = round($product->price).'</h2>';
                        $disc = '';
                    }
                }
                elseif($start_date == 0 && $end_date == 0)
                {
                    $price = round($discount_price).'</h2>';
                }
                elseif($start_date > 0 && $end_date > 0 && ($now >= $start_date && $now <= $end_date))
                {
                    $price = round($discount_price).'</h2>';
                }
                else
                {
                    $price = round($product->price).'</h2>';
                    $disc = '';
                }
               
                $price = $price.$disc;
            }
            else
            {
                $price = round($product->price).'</h2>';
            }
        }
        
        return '<h2><sup>'. $currency .'</sup>'.$price;
    }
}