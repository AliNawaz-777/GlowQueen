<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Core\Contracts\CallCourierSetting as CallCourierSettingContract;

class CallCourierSetting extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login_id', 'origin', 'service_type_id','sel_origin','shipper_name','shipper_cell_no','shipper_area','shipper_city','shipper_address','shipper_land_line_no','shipper_email'
    ];
}
