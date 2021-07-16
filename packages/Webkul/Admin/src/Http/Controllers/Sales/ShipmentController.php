<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\ShipmentRepository;
use Webkul\Core\Models\CallCourierSetting;

/**
 * Sales Shipment controller
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * OrderRepository object
     *
     * @var mixed
     */
    protected $orderRepository;

    /**
     * OrderItemRepository object
     *
     * @var mixed
     */
    protected $orderItemRepository;

    /**
     * ShipmentRepository object
     *
     * @var mixed
     */
    protected $shipmentRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\ShipmentRepository  $shipmentRepository
     * @param  \Webkul\Sales\Repositories\OrderRepository     $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderitemRepository $orderItemRepository
     * @return void
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function create($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order->channel || !$order->canShip()) {
            session()->flash('error', trans('admin::app.sales.shipments.creation-error'));

            return redirect()->back();
        }

        return view($this->_config['view'], compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function store($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order->canShip()) {
            session()->flash('error', trans('admin::app.sales.shipments.order-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'shipment.carrier_title' => 'required',
            'shipment.track_number' => 'required',
            'shipment.source' => 'required',
            'shipment.items.*.*' => 'required|numeric|min:0',
        ]);

        $data = request()->all();
            
        if (! $this->isInventoryValidate($data) ) {
            session()->flash('error', trans('admin::app.sales.shipments.quantity-invalid'));

            return redirect()->back();
        }
        
        $var= $this->shipmentRepository->create(array_merge($data, ['order_id' => $orderId]));

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Shipment']));

        return redirect()->route($this->_config['redirect'], $orderId);
    }

    /**
     * Checks if requested quantity available or not
     *
     * @param array $data
     * @return boolean
     */
    public function isInventoryValidate(&$data)
    {
        if (! isset($data['shipment']['items']))
            return ;

        $valid = false;

        $inventorySourceId = $data['shipment']['source'];
        
        foreach ($data['shipment']['items'] as $itemId => $inventorySource) {
            if ($qty = $inventorySource[$inventorySourceId]) {
                $orderItem = $this->orderItemRepository->find($itemId);

                if ($orderItem->qty_to_ship < $qty)
                    return false;

                if ($orderItem->getTypeInstance()->isComposite()) {
                    foreach ($orderItem->children as $child) {
                        if (! $child->qty_ordered)
                            continue;

                        $finalQty = ($child->qty_ordered / $orderItem->qty_ordered) * $qty;

                        $availableQty = $child->product->inventories()
                            ->where('inventory_source_id', $inventorySourceId)
                            ->sum('qty');

                        if ($child->qty_to_ship < $finalQty || $availableQty < $finalQty)
                            return false;
                    }
                } else {
                    $availableQty = $orderItem->product->inventories()
                            ->where('inventory_source_id', $inventorySourceId)
                            ->sum('qty');

                    if ($orderItem->qty_to_ship < $qty || $availableQty < $qty)
                        return false;
                }

                $valid = true;
            } else {
                unset($data['shipment']['items'][$itemId]);
            }
        }

        return $valid;
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);

        return view($this->_config['view'], compact('shipment'));
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */

    public function CallCourrier($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order->channel || !$order->canShip()) {
            session()->flash('error', trans('admin::app.sales.shipments.creation-error'));

            return redirect()->back();
        }
        
        return view($this->_config['view'], compact('order'));
        
    }
    public function orderNowCallCourier($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);
        
        $weight = 0;
        
        foreach($order->items as $item)
        {
            $weight = $weight + $item->total_weight;
        }
        

        if (! $order->canShip()) {
            session()->flash('error', trans('admin::app.sales.shipments.order-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'shipment.carrier_title' => 'required',
            'shipment.source' => 'required',
            'shipment.description' => 'required',
            'shipment.items.*.*' => 'required|numeric|min:0',
        ]);

        $data = request()->all();
         
        if (! $this->isInventoryValidate($data) ) {
            session()->flash('error', trans('admin::app.sales.shipments.quantity-invalid'));

            return redirect()->back();
        }
        // Code for Save Booking API on Call Courier
        $apiSetting = CallCourierSetting::first();
        $customer = $order->shipping_address;
        $apidata = [];

        $destCityId = \DB::table('call_courier_cities')->where('city_name',$customer->city)->first();
        $destCityId = !empty($destCityId) ? $destCityId->city_id : '';
        
        $apidata['loginId'] = $apiSetting->login_id;
        $apidata['ConsigneeName'] = $customer->first_name.' '.$customer->last_name;
        $apidata['ConsigneeRefNo'] = $order->increment_id;
        $apidata['ConsigneeCellNo'] = $customer->phone;//+923037654534;
        $apidata['Address'] = $customer->address1;
        $apidata['Origin'] = $apiSetting->origin;
        $apidata['DestCityId'] = $destCityId;
        $apidata['ServiceTypeId'] = $apiSetting->service_type_id;
        $apidata['Pcs'] = $order->total_qty_ordered;
        $apidata['Weight'] = $weight;
        $apidata['Description'] = $data['shipment']['description'];
        $apidata['SelOrigin'] = $apiSetting->sel_origin;
        $apidata['CodAmount'] = $order->grand_total;
        $apidata['SpecialHandling'] = "false";
        $apidata['MyBoxId'] = "1";
        $apidata['Holiday'] = "false";
        $apidata['remarks'] = '';
        $apidata['ShipperName'] = $apiSetting->shipper_name;
        $apidata['ShipperCellNo'] = $apiSetting->shipper_cell_no;
        $apidata['ShipperArea'] = $apiSetting->shipper_area;
        $apidata['ShipperCity'] = $apiSetting->shipper_city;
        $apidata['ShipperAddress'] = $apiSetting->shipper_address;
        $apidata['ShipperLandLineNo'] = $apiSetting->shipper_land_line_no;
        $apidata['ShipperEmail'] = $apiSetting->shipper_email;
       
        $query = http_build_query($apidata);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://cod.callcourier.com.pk/API/CallCourier/SaveBooking?".$query
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $response = \json_decode($response,true);
        $err = curl_error($curl);
        curl_close($curl);
     
        if($response['Response'] == "true")
        {
            unset($data['shipment']['description']);
            $data['shipment']['track_number'] = $response['CNNO'];
            $var = $this->shipmentRepository->create(array_merge($data, ['order_id' => $orderId]));
            session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Shipment']));
            return redirect()->route($this->_config['redirect'], $orderId);
        }
        else
        {
            session()->flash('error', trans('Something Went Wrong.'));
            return redirect()->route($this->_config['redirect'], $orderId);
        }
    }
}
