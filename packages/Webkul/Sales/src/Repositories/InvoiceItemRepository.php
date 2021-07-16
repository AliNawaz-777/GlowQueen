<?php

namespace Webkul\Sales\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Eloquent\Repository;
use Webkul\Sales\Contracts\InvoiceItem;

/**
 * Invoice Item Reposotory
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */

class InvoiceItemRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return Mixed
     */

    function model()
    {
        return InvoiceItem::class;
    }

    /**
     * @param array $data
     * @return void
     */
    public function updateProductInventory($data)
    {
        if (! $data['product'])
            return;

        $orderedInventory = $data['product']->ordered_inventories()
                ->where('channel_id', $data['invoice']->order->channel->id)
                ->first();

        if ($orderedInventory) {
            if (($orderedQty = $orderedInventory->qty - $data['qty']) < 0)
                $orderedQty = 0;

            $orderedInventory->update(['qty' => $orderedQty]);
        }

        $inventories = $data['product']->inventories()
                ->where('vendor_id', $data['vendor_id'])
                ->whereIn('inventory_source_id', $data['invoice']->order->channel->inventory_sources()->pluck('id'))
                ->orderBy('qty', 'desc')
                ->get();
            
        foreach ($inventories as $key => $inventory) {
            if ($inventory->qty >= $data['qty']) {
                $inventory->update(['qty' => $inventory->qty - $data['qty']]);

                break;
            } else {
                $data['qty'] -= $inventory->qty;

                $inventory->update(['qty' => 0]);
            }
        }

        Event::fire('catalog.product.update.after', $data['product']);
    }
}