<?php

namespace Webkul\Core\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Webkul\Core\Models\CallCourierSetting;
/**
 * Locale controller
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class CallCourierSettingController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * callCourierRepository object
     *
     * @var array
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $setting = CallCourierSetting::first();
        $cities = \DB::table('call_courier_cities')->get();
        return view($this->_config['view'], compact('setting','cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store_update()
    {
        $data = request()->all();
        
        unset($data['_token']);
     
        $this->validate(request(), [
            'login_id' => 'required',
            'origin' => 'required',
            'service_type_id' => 'required',
            'sel_origin' => 'required',
            'shipper_name' => 'required',
            'shipper_cell_no' => 'required',
            'shipper_area' => 'required',
            'shipper_city' => 'required',
            'shipper_address' => 'required',
            'shipper_land_line_no' => 'required',
            'shipper_email' => 'required',
        ]);
        
        if($data['setting_id'] != "")
        {
            $model = CallCourierSetting::findOrFail($data['setting_id']);
        }
        else
        {
            $model = new CallCourierSetting();
        }

        $model->fill($data);
        $model->save();

        session()->flash('success', trans('Call Courier Setting Updated.'));

        return redirect()->route($this->_config['redirect']);
    }

    public function getAreasByCity()
    {
        $area_id = request()->all()['area_id'];
        $city_id = request()->all()['city_id'];
        $html = '';
        $areas = \DB::table('call_courier_city_areas')->where(['city_id' => $city_id])->get();
        $html .= '<option value="">Select Area</option>';
        foreach($areas as $area)
        {
            $selected = ($area_id != "" && $area->area_id == $area_id) ? 'selected' : '';
            $html .= '<option value='.$area->area_id.' '.$selected.'>'.$area->area_name.'</option>';
        }
    
        return $html;
    }

    public function trackHistory()
    {
        $track_id = request()->all()['tracking_id'];
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://cod.callcourier.com.pk/api/CallCourier/GetTackingHistory?cn=".$track_id
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $response = \json_decode($response,true);
        $err = curl_error($curl);
        curl_close($curl);
        
        return $response;
    }
}