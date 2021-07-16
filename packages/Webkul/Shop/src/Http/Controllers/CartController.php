<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Product\Repositories\ProductRepository;
use Cart;
use DB;
use Session;
use Webkul\Product\Helpers\ProductImage;

/**
 * Cart controller for the customer and guest users for adding and
 * removing the products in the cart.
 *
 * @author  Arhamsoft (info@arhamsoft.com)
 * @author  Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class CartController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * WishlistRepository Repository object
     *
     * @var Object
     */
    protected $wishlistRepository;

    /**
     * ProductRepository object
     *
     * @var Object
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CartItemRepository $wishlistRepository
     * @param  \Webkul\Product\Repositories\ProductRepository   $productRepository
     * @return void
     */
    public function __construct(
        WishlistRepository $wishlistRepository,
        ProductRepository $productRepository
    )
    {
        $this->middleware('customer')->only(['moveToWishlist']);
        $this->wishlistRepository = $wishlistRepository;
        $this->productRepository = $productRepository;
        $this->_config = request('_config');
    }

    /**
     * Method to populate the cart page which will be populated before the checkout process.
     *
     * @return \Illuminate\View\View 
     */
    public function index()
    {
        if(isset(Cart::getCart()->items) && !empty(Cart::getCart()->items))
        {
            foreach(Cart::getCart()->items as $key => $item)
            {
                $result = $this->checkActiveProductCart($item->product_id);
                if(empty($result))
                {
                    $this->remove($item->id);
                }
            }    
        }

        Cart::collectTotals();

        return view($this->_config['view'])->with('cart', Cart::getCart());
    }


    public function checkActiveProductCart($product_id) {
      
        $return  = \DB::table('product_flat')
        ->select('product_flat.*', 'product_inventories.qty')
        
        ->leftJoin('products', 'products.id', '=', 'product_flat.product_id')
        ->leftJoin('product_inventories', 'product_inventories.product_id', '=', 'product_flat.product_id')
        ->where('product_flat.status', 1)
        ->where('products.id', $product_id)
        ->whereRaw('
            IF(products.type != "configurable", 
            (`product_inventories`.`qty` != 0 and `product_flat`.`price` != 0  AND product_inventories.qty > (select COALESCE(SUM(order_items.qty_ordered),0) as order_count from orders INNER JOIN order_items ON orders.id = order_items.order_id where order_items.product_id = products.id AND orders.status="pending"))
            , 
            ( (SELECT sum(prod_inv.qty) as prod_sum FROM product_flat prod_flat INNER JOIN products prods ON prod_flat.product_id=prods.id left JOIN product_inventories prod_inv ON prod_inv.product_id=prods.id WHERE prod_flat.parent_id = products.id) > (SELECT COALESCE(SUM(order_items.qty_ordered),0) as order_count FROM orders INNER JOIN order_items ON order_items.order_id=orders.id WHERE order_items.product_id = products.id AND orders.status = "pending") ) )
        ')
        ->get()->toArray();
       
        return $return;
    }    

    /**
     * Function for guests user to add the product in the cart.
     *
     * @return Mixed
     */
    public function add_backup($id)
    {
        try {
            $result = Cart::addProduct($id, request()->all());

            if ($result) {
                session()->flash('success', trans('shop::app.checkout.cart.item.success'));

                if ($customer = auth()->guard('customer')->user())
                    $this->wishlistRepository->deleteWhere(['product_id' => $id, 'customer_id' => $customer->id]);

                if (request()->get('is_buy_now'))
                    return redirect()->route('shop.checkout.onepage.index');
            } else {
                session()->flash('warning', trans('shop::app.checkout.cart.item.error-add'));
            }
        } catch(\Exception $e) {
            session()->flash('error', trans($e->getMessage()));

            $product = $this->productRepository->find($id);

            return redirect()->route('shop.products.index', ['slug' => $product->url_key]);
        }

        return redirect()->back();
    }
    
    public function add($id)
    {
        $status = '';
        $ses_type = '';
        $message = '';
        $url = '';
        $cart_data = NULL;
        $product = $this->productRepository->find($id);
        $currency = DB::table('currencies')->where('code', Session::get('currency'))->selectRaw('symbol')->first();
        $product_image_helper = new ProductImage();
        $images = array();
        $i = 0;
        try 
        {
            $result = Cart::addProduct($id, request()->all());    
            if ($result) 
            {
                if(request()->all()['quantity'] > 1)
                {
                    $ses_type = 'success';
                    $message = trans('shop::app.checkout.cart.item.success_multiple');
                }
                else
                {
                    $ses_type = 'success';
                    $message = trans('shop::app.checkout.cart.item.success_single');
                }
                $status = 'success';
                $message = trans('shop::app.checkout.cart.item.success');
                $cart_data = $result;
                
                foreach($cart_data->items as $key => $cart) 
                {
                    $cart_data->items[$key]->url_key = core()->getProductSlug($cart->product_id);
                    $cart_product = $this->productRepository->find($cart->product_id);
                    $images[$i] = $product_image_helper->getProductBaseImage($cart_product);
                    // $images[$i] = $product_image_helper->getBaseImage($cart);
                    $i++;
                }

                if ($customer = auth()->guard('customer')->user())
                    $this->wishlistRepository->deleteWhere(['product_id' => $id, 'customer_id' => $customer->id]);

                if (request()->get('is_buy_now'))
                    if (request()->ajax()) 
                    {
                        $url = route('shop.checkout.onepage.index');
                    }
                    else 
                    {
                        return redirect()->route('shop.checkout.onepage.index');
                    }
            } 
            else 
            {
                $status = 'fail';
                $ses_type = 'warning';
                $message = trans('shop::app.checkout.cart.item.error-add');
            }
        } 
        catch(\Exception $e) 
        {
            $status = 'fail';
            $ses_type = 'error';
            $message = $e->getMessage();
            $url = route('shop.products.index', ['slug' => $product->url_key]);
        }
        
        if (request()->ajax()) 
        {
            echo json_encode(['status' => $status, 'message' => $message, 'return_url' => $url, 'cart_data' => $cart_data, 'currency' => $currency->symbol, 'images' => $images]);
        }
        else 
        {
            session()->flash($ses_type, $message);
            return redirect()->back();
        }
    }

    /**
     * Removes the item from the cart if it exists
     *
     * @param integer $itemId
     * @return Response
     */
    public function remove($itemId)
    {
        $result = Cart::removeItem($itemId);

        if ($result)
            session()->flash('success', trans('shop::app.checkout.cart.item.success-remove'));

        return redirect()->back();
    }

    /**
     * Updates the quantity of the items present in the cart.
     *
     * @return Response
     */
    public function updateBeforeCheckout()
    {
        try {
            $result = Cart::updateItems(request()->all());

            if ($result)
                session()->flash('success', trans('shop::app.checkout.cart.quantity.success'));
        } catch(\Exception $e) {
            session()->flash('error', trans($e->getMessage()));
        }

        return redirect()->back();
    }

    /**
     * Function to move a already added product to wishlist will run only on customer authentication.
     *
     * @param integer $id
     * @return Response
     */
    public function moveToWishlist($id)
    {
        $result = Cart::moveToWishlist($id);
        $response = [];
        if ($result) {
            $response['status'] = 'succsess';
            $response['msg'] = trans('shop::app.checkout.cart.move-to-wishlist-success');
            // session()->flash('success', trans('shop::app.checkout.cart.move-to-wishlist-success'));
        } else {
            $response['status'] = 'failed';
            $response['msg'] = trans('shop::app.wishlist.move-error');
            // session()->flash('warning', trans('shop::app.wishlist.move-error'));
        }
        return $response;
        // return redirect()->back();
    }
}