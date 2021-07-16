<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

/**
 * Wishlist Reposotory
 *
 * @author    Prashant Singh <prashant.singh852@webkul.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */

class WishlistRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'Webkul\Customer\Contracts\Wishlist';
    }

    /**
     * @param array $data
     * @return mixed
     */

    public function create(array $data)
    {
        $wishlist = $this->model->create($data);

        return $wishlist;
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */

    public function update(array $data, $id, $attribute = "id")
    {
        $wishlist = $this->find($id);

        $wishlist->update($data);

        return $wishlist;
    }

    /**
     * To retrieve products with wishlist m
     * for a listing resource.
     *
     * @param integer $id
     */
    public function getItemsWithProducts($id) {
        return $this->model->find($id)->item_wishlist;
    }

    /**
     * get customer wishlist Items.
     *
     * @return mixed
     */
    public function getCustomerWhishlist() {
        return $this->model->where([
            'channel_id' => core()->getCurrentChannel()->id,
            'customer_id' => auth()->guard('customer')->user()->id
        ])->paginate(10);
    }


    /**
     * get customer wishlist Items.
     *
     * @return mixed
     */
    public function checkActiveProductWhishlist($product_id) {
      
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
}