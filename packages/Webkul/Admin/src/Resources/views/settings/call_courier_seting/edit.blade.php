@extends('admin::layouts.content')

@section('page_title')
    Update Call Courier Setting
@stop

@section('content')
    <div class="content">

        <form method="POST" action="{{ route('admin.call-courier-setting.update') }}" @submit.prevent="onSubmit">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                        Update Call Courier Setting
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        Save Setting
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    <accordian :title="'{{ __('admin::app.settings.locales.general') }}'" :active="true">
                        <div slot="body">
                            <input type="hidden" name="setting_id" value="{{ !empty($setting) ? $setting->id : '' }}">
                            <div class="control-group" :class="[errors.has('login_id') ? 'has-error' : '']">
                                <label for="login_id" class="required">Login Id</label>
                                <input type="text" v-validate="'required'" class="control" id="login_id" name="login_id" value="{{ !empty($setting) ? $setting->login_id : '' }}"/>
                                <span class="control-error" v-if="errors.has('login_id')">@{{ errors.first('login_id') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('origin') ? 'has-error' : '']">
                                <label for="origin" class="required">origin</label>
                                <input type="text" v-validate="'required'" class="control" id="origin" name="origin" value="{{ !empty($setting) ? $setting->origin : '' }}"/>
                                <span class="control-error" v-if="errors.has('origin')">@{{ errors.first('origin') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('service_type_id') ? 'has-error' : '']">
                                <label for="service_type_id" class="required">Service Type Id</label>
                                <input type="text" v-validate="'required'" class="control" id="service_type_id" name="service_type_id" value="{{ !empty($setting) ? $setting->service_type_id : '' }}"/>
                                <span class="control-error" v-if="errors.has('service_type_id')">@{{ errors.first('service_type_id') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('sel_origin') ? 'has-error' : '']">
                                <label for="sel_origin" class="required">Sel Origin</label>
                                <input type="text" v-validate="'required'" class="control" id="sel_origin" name="sel_origin" value="{{ !empty($setting) ? $setting->sel_origin : '' }}"/>
                                <span class="control-error" v-if="errors.has('sel_origin')">@{{ errors.first('sel_origin') }}</span>
                            </div>
                            
                            <div class="control-group" :class="[errors.has('shipper_name') ? 'has-error' : '']">
                                <label for="shipper_name" class="required">Shipper Name</label>
                                <input type="text" v-validate="'required'" class="control" id="shipper_name" name="shipper_name" value="{{ !empty($setting) ? $setting->shipper_name : '' }}"/>
                                <span class="control-error" v-if="errors.has('shipper_name')">@{{ errors.first('shipper_name') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('shipper_cell_no') ? 'has-error' : '']">
                                <label for="shipper_cell_no" class="required">Shipper Cell No</label>
                                <input type="text" v-validate="'required'" class="control" id="shipper_cell_no" name="shipper_cell_no" value="{{ !empty($setting) ? $setting->shipper_cell_no : '' }}"/>
                                <span class="control-error" v-if="errors.has('shipper_cell_no')">@{{ errors.first('shipper_cell_no') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('shipper_city') ? 'has-error' : '']">
                                <label for="shipper_city" class="required">Shipper City</label>
                                <select v-validate="'required'" class="control" id="shipper_city" name="shipper_city">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        @php $selected = (!empty($setting) && $setting->shipper_city == $city->city_id) ? 'selected' : ''; @endphp
                                        <option value="{{ $city->city_id }}" {{ $selected }}>{{ $city->city_name }}</option>
                                    @endforeach
                                </select>
                                <span class="control-error" v-if="errors.has('shipper_city')">@{{ errors.first('shipper_city') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('shipper_area') ? 'has-error' : '']">
                                <label for="shipper_area" class="required">Shipper Area</label>
                                <select v-validate="'required'" class="control" id="shipper_area" name="shipper_area"></select>
                                <span class="control-error" v-if="errors.has('shipper_area')">@{{ errors.first('shipper_area') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('shipper_address') ? 'has-error' : '']">
                                <label for="shipper_address" class="required">Shipper Address</label>
                                <input type="text" v-validate="'required'" class="control" id="shipper_address" name="shipper_address" value="{{ !empty($setting) ? $setting->shipper_address : '' }}"/>
                                <span class="control-error" v-if="errors.has('shipper_address')">@{{ errors.first('shipper_address') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('shipper_land_line_no') ? 'has-error' : '']">
                                <label for="shipper_land_line_no" class="required">Shipper Land Line No</label>
                                <input type="text" v-validate="'required'" class="control" id="shipper_land_line_no" name="shipper_land_line_no" value="{{ !empty($setting) ? $setting->shipper_land_line_no : '' }}"/>
                                <span class="control-error" v-if="errors.has('shipper_land_line_no')">@{{ errors.first('shipper_land_line_no') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('shipper_email') ? 'has-error' : '']">
                                <label for="shipper_email" class="required">Shipper Email</label>
                                <input type="email" v-validate="'required'" class="control" id="shipper_email" name="shipper_email" value="{{ !empty($setting) ? $setting->shipper_email : '' }}"/>
                                <span class="control-error" v-if="errors.has('shipper_email')">@{{ errors.first('shipper_email') }}</span>
                            </div>

                        </div>
                    </accordian>

                </div>
            </div>
        </form>
    </div>
@stop