<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;

/**
 * Customer Reposotory
 *
 * @author    Prashant Singh <prashant.singh852@webkul.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */

class CustomerAddressRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'Webkul\Customer\Contracts\CustomerAddress';
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        Event::fire('customer.addresses.create.before');

        if ( isset($data['default_address']) ) {
            $data['default_address'] = 1;
        } else {
            $data['default_address'] = 0;
        }

        $default_address = $this->findWhere(['customer_id' => $data['customer_id'], 'default_address' => 1])->first();

        if ( isset($default_address->id) && $data['default_address'] ) {
            $default_address->update(['default_address' => 0]);
        }
        // ['customer_id' ,'address1', 'country', 'state', 'city', 'postcode', 'phone', 'default_address']
        $where_array = [];
        $where_array['customer_id'] = $data['customer_id'];
        $where_array['address1'] = $data['address1'];
        $where_array['country'] = $data['country'];
        if(isset($data['state']) && $data['state'] <> '')
        {
            $where_array['state'] = $data['state'];
        }
        $where_array['city'] = $data['city'];
        if(isset($data['postcode']) && $data['postcode'] <> '')
        {
            $where_array['postcode'] = $data['postcode'];
        }

        $where_array['phone'] = $data['phone'];
        $where_array['default_address'] = $data['default_address'];
        
        $get_addres = $this->model->where($where_array)->count();
        $address = 1;

        if(!$get_addres > 0)
        {
            $address = $this->model->create($data);
        }
    
        Event::fire('customer.addresses.create.after', $address);

        return $address;
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $address = $this->find($id);

        Event::fire('customer.addresses.update.before', $id);

        if (isset($data['default_address']) ) {
            $data['default_address'] = 1;
        } else {
            $data['default_address'] = 0;
        }

        $default_address = $this->findWhere(['customer_id' => $address->customer_id, 'default_address' => 1])->first();

        if ( isset($default_address->id) && $data['default_address'] ) {
            if ( $default_address->id != $address->id ) {
                $default_address->update(['default_address' => 0]);
            }
            $address->update($data);
        } else {
            $address->update($data);
        }

        Event::fire('customer.addresses.update.after', $id);

        return $address;
    }       
}