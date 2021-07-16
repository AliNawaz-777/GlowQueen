@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.checkout.onepage.title') }}
@stop

@section('content-wrapper')

<div class="container">
    <checkout></checkout>

</div>
@endsection

@push('scripts')
    <script type="text/x-template" id="checkout-template">
        <div id="checkout" class="checkout-process section-padding">
            <div class="col-main">
                <ul class="checkout-steps">
                    <!-- <li class="active" :class="[completed_step >= 0 ? 'active' : '', completed_step > 0 ? 'completed' : '']" @click="navigateToStep(1)">
                        <div class="decorator address-info"></div>
                        <span>{{ __('shop::app.checkout.onepage.information') }}</span>
                    </li> -->

                    <li :class="[current_step == 0 || current_step == 1 ? 'active' : '' || completed_step > 1 ? 'active' : '', completed_step > 1 ? 'completed' : '']" @click="navigateToStep(1)">
                        <div class="decorator address-info"></div>
                        <span>{{ __('shop::app.checkout.onepage.information') }}</span>
                    </li>
                    
                    <div class="line mb-25"></div>

                    @if ($cart->haveStockableItems())
                        <!-- <li :class="[current_step == 1 || completed_step > 1 ? 'active' : '', completed_step > 1 ? 'completed' : '']" @click="navigateToStep(1)">
                            <div class="decorator shipping"></div>
                            <span>{{ __('shop::app.checkout.onepage.shipping') }}</span>
                        </li>

                        <div class="line mb-25"></div> -->
                    @endif

                    <!-- <li :class="[current_step == 1 || completed_step > 1 ? 'active' : '', completed_step > 1 ? 'completed' : '']" @click="navigateToStep(1)">
                        <div class="decorator payment"></div>
                        <span>{{ __('shop::app.checkout.onepage.payment') }}</span>
                    </li>

                    <div class="line mb-25"></div> -->

                    <li :class="[current_step == 3 ? 'active' : '']">
                        <div class="decorator review"></div>
                        <span>{{ __('shop::app.checkout.onepage.complete') }}</span>
                    </li>
                </ul>

                <div class="step-content information" v-show="current_step  == 1" id="address-section">
                    @include('shop::checkout.onepage.customer-info')
                  {{--  {!! $shippingRateGroups['html'] !!}  --}}  

                    <!-- <div class="button-group">
                        <button type="button" class="btn btn-lg btn-primary" @click="validateForm('address-form')" :disabled="disable_button" id="checkout-address-continue-button">
                            {{ __('shop::app.checkout.onepage.continue') }}
                        </button>
                    </div> -->
                </div>

                <div class="step-content shipping" v-show="current_step == 1" id="shipping-section">
                    <shipping-section v-if="current_step == 1" @onShippingMethodSelected="shippingMethodSelected($event)"></shipping-section>
                    <!-- <div class="button-group">
                        <button type="button" class="btn btn-lg btn-primary" @click="validateForm('shipping-form')" :disabled="disable_button" id="checkout-shipping-continue-button">
                            {{ __('shop::app.checkout.onepage.continue') }}
                        </button>
                    </div> -->
                </div>

                <div class="step-content payment" v-show="current_step == 1" id="payment-section">
                    <payment-section v-if="current_step == 1" @onPaymentMethodSelected="paymentMethodSelected($event)"></payment-section>

                    <!-- <div class="button-group">
                        <button type="button" class="btn btn-lg btn-primary" @click="validateForm('payment-form')" :disabled="disable_button" id="checkout-payment-continue-button">
                            {{ __('shop::app.checkout.onepage.continue') }}
                        </button>
                    </div> -->

                    <div class="button-group">
                        <button type="button" class="btn btn-lg btn-primary" @click="validateForm('address-form')" :disabled="disable_button" >
                            {{ __('shop::app.checkout.onepage.continue') }}
                        </button>
                    </div>
                </div>
 

                <div class="step-content review" v-show="current_step == 3" id="summary-section">
                    <review-section v-if="current_step == 3" :key="reviewComponentKey">
                        <div slot="summary-section">
                            <summary-section
                                discount="1"
                                :key="summeryComponentKey"
                                @onApplyCoupon="getOrderSummary"
                                @onRemoveCoupon="getOrderSummary"
                            ></summary-section>
                        </div>
                    </review-section>
                   <div class="onePagebutns">
                    <div class="button-group" v-show="prev_step == 1">
                        <button type="button" class="btn btn-lg btn-primary" @click="backCustomrInfo()" id="">
                            Back
                        </button>
                    </div>
                
                    <div class="button-group">
                        <button type="button" class="btn btn-lg btn-primary" @click="placeOrder()" :disabled="disable_button" id="checkout-place-order-button">
                            {{ __('shop::app.checkout.onepage.place-order') }}
                        </button>
                    </div>
                   </div>
                   
                </div>
            </div>

            <div class="col-right" v-show="current_step != 4">
                <summary-section :key="summeryComponentKey"></summary-section>
            </div>
        </div>
    </script>

    <script>
        var shippingHtml = '';
        var paymentHtml = '';
        var reviewHtml = '';
        var summaryHtml = '';
        var customerAddress = '';
        var customerAddressShipping = '';
        var shippingMethods = '';
        var paymentMethods = '';
        

        @auth('customer')
            @if(auth('customer')->user()->addresses)
                customerAddress = @json(auth('customer')->user()->addresses);
                customerAddress.email = "{{ auth('customer')->user()->email }}";
                customerAddress.first_name = "{{ auth('customer')->user()->first_name }}";
                customerAddress.last_name = "{{ auth('customer')->user()->last_name }}";
                customerAddressShipping = @json(auth('customer')->user()->addresses);
            @endif
        @endauth

        Vue.component('checkout', {
            template: '#checkout-template',
            inject: ['$validator'],

            data: function() {
                return {
                    step_numbers: {
                        'information': 1,
                        'shipping': 1,
                        'payment': 2,
                        'review':3
                    },
                    current_step: 100,
                    prev_step: 0,
                    completed_step: 0,
                    address: {
                        billing: {
                            address_id: 0,
                            address1: [''],
                            use_for_shipping: true,
                            country:"PK"
                        },
                        shipping: {
                            address_id: 0,
                            address1: [''],
                            country:"PK"
                        },
                    },
                    payCheck: 10,
                    addCheck: 10,
                    selected_shipping_method: '',
                    selected_payment_method: '',
                    disable_button: false,
                    new_shipping_address: false,
                    new_billing_address: false,
                    allAddress: {},
                    countryStates: @json(core()->groupedStatesByCountries()),
                    country: @json(core()->countries()),
                    summeryComponentKey: 0,
                    reviewComponentKey: 0,
                    is_customer_exist: 0
                }
            },
            watch: {
                payCheck: function(){
                    if(this.payCheck == 2 && this.addCheck ==2){
                        this.current_step = 1
                        $(".loader").fadeOut(1000);

                    }
                },
                addCheck: function(){
                    if(this.payCheck == 2 && this.addCheck ==2){
                        this.current_step = 1
                        $(".loader").fadeOut(1000);

                    }
                }
            },
            mounted:function() {
                setTimeout(() => {
                    this.getShippingPage();
					
                }, 600);
                this.getPaymentPage();
            },
            created: function() {

                this.getOrderSummary();

                if(! customerAddress) {
                    this.new_shipping_address = true;
                    this.new_billing_address = true;
                } else {
                    this.address.billing.first_name = this.address.shipping.first_name = customerAddress.first_name;
                    this.address.billing.last_name = this.address.shipping.last_name = customerAddress.last_name;
                    this.address.billing.email = this.address.shipping.email = customerAddress.email;

                    if (customerAddress.length < 1) {
                        this.new_shipping_address = true;
                        this.new_billing_address = true;
                    } else {
                        
                        this.allAddress = customerAddress;
                        if(customerAddress.length > 0){
                            this.address.billing.address_id = customerAddress[0].id
                        }
                        if(customerAddressShipping.length > 0){
                            this.address.shipping.address_id = customerAddressShipping[0].id
                        }
 
                        for (var country in this.country) {
                            for (var code in this.allAddress) {
                                if (this.allAddress[code].country) {
                                    if (this.allAddress[code].country == this.country[country].code) {
                                        this.allAddress[code]['country'] = this.country[country].name;
                                    }
                                }
                            }
                        }
                    }
                }
            },
            
            methods: {
              	//change//
				onlyNumber: function($event) {
				
				   let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
				   if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
					  $event.preventDefault();
				   }
				},
                checkPhoneNumber: function(param){
                    var filter = /03[0-9]{2}([0-9])(?!\1{6})[0-9]{6}/;
                    var val_ = this.address[param].phone;
                    $('.input_'+param).next().remove();
                    if (val_.match(filter) == null) 
                    {
                        this.address[param].phone = '';
                        $('.input_'+param).after('<span class="control-error custom-errro">Please enter valid phone number (03001234567).</span>');
                    }
                },
                navigateToStep: function(step) {
                    if (step <= this.completed_step) {
                        this.current_step = step
                        this.completed_step = step - 1;
                    }
                },
                haveStates: function(addressType) {
                    
                    if (this.countryStates[this.address[addressType].country] && this.countryStates[this.address[addressType].country].length)
                        return true;

                    return false;
                },

                validateForm: function(scope) {
                    var this_this = this;
                    this.$validator.validateAll(scope).then(function (result) {
                       
                        if (result) {
                            this_this.saveAddress();
                            // this_this.saveShipping();
                            // this_this.savePayment();
                            // if (scope == 'address-form') {
                            //     this_this.saveShipping();
                            //     this_this.saveAddress();
                            // } else if (scope == 'shipping-form') {
                            //     this_this.saveAddress();
                            //     this_this.saveShipping();
                            // } else if (scope == 'payment-form') {
                            //     this_this.savePayment();
                            // }
                        } else {
                            $('.has-error').eq(0).find('input').focus();
                        }
                    }); 
                },

                isCustomerExist: function() {
                    this.$validator.attach('email', 'required|email');

                    var this_this = this;

                    this.$validator.validate('email', this.address.billing.email)
                        .then(function(isValid) {
                            if (! isValid)
                                return;

                            this_this.$http.post("{{ route('customer.checkout.exist') }}", {email: this_this.address.billing.email})
                                .then(function(response) {
                                    this_this.is_customer_exist = response.data ? 1 : 0;
                                })
                                .catch(function (error) {})

                        })
                },

                loginCustomer: function() {
                    var this_this = this;

                    this_this.$http.post("{{ route('customer.checkout.login') }}", {
                            email: this_this.address.billing.email,
                            password: this_this.address.billing.password
                        })
                        .then(function(response) {
                            if (response.data.success) {
                                window.location.href = "{{ route('shop.checkout.onepage.index') }}";
                            } else {
                                window.flashMessages = [{'type': 'alert-error', 'message': response.data.error }];

                                this_this.$root.addFlashMessages()
                            }
                        })
                        .catch(function (error) {})
                },

                getOrderSummary () {
                    var this_this = this;

                    this.$http.get("{{ route('shop.checkout.summary') }}")
                        .then(function(response) {
                            summaryHtml = Vue.compile(response.data.html)

                            this_this.summeryComponentKey++;
                            this_this.reviewComponentKey++;
                        })
                        .catch(function (error) {})
                },
                getShippingPage: function()
                {
                    var this_this_ = this;
                    this.disable_button = true;
                    this.$http.post('{{ route('shop.checkout.get-shipping') }}',this.address)
                        .then(function(response) {
                            // console.log(response);
                        if (response.data.jump_to_section == 'shipping') {
                            shippingHtml = Vue.compile(response.data.html)
                        }
                        else {
                            paymentHtml = Vue.compile(response.data.html)
                        }
                           

                        this_this_.completed_step = this_this_.step_numbers[response.data.jump_to_section] - 1;
                        // this_this_.current_step = this_this_.step_numbers[response.data.jump_to_section];

                        this_this_.addCheck = 2

                        shippingMethods = response.data.shippingMethods;
                        this.getOrderSummary();
                    })
                    .catch(function (error) {
                        this_this_.disable_button = false;
                        //console.log(error.response);
                        this_this_.handleErrorResponse(error.response, 'shipping-form')
                    })
                },
                getPaymentPage: function()
                {
                    var this_this_ = this;
                    this.disable_button = true;
                    this.$http.post('{{ route('shop.checkout.get-payment-page') }}',this.address)
                    .then(function(response) {
                        this_this_.disable_button = false;

                        paymentHtml = Vue.compile(response.data.html)
                        this_this_.completed_step = this_this_.step_numbers[response.data.jump_to_section] - 1;
                        // this_this_.current_step = this_this_.step_numbers[response.data.jump_to_section];

                        this_this_.payCheck = 2
                        paymentMethods = response.data.paymentMethods;

                        this_this_.getOrderSummary();
                    })
                    .catch(function (error) {

                        this_this_.disable_button = false;
                        this_this_.handleErrorResponse(error.response, 'payment-form')
                    })
                },
                saveAddress: function() {
                    var this_this = this;
                   

                    this.disable_button = true;

                    this.$http.post("{{ route('shop.checkout.save-address') }}", this.address)
                        .then(function(response) {
                            this_this.disable_button = false;

                            if (this_this.step_numbers[response.data.jump_to_section] == 2)
                                shippingHtml = '';// Vue.compile(response.data.html)
                            else
                                paymentHtml = Vue.compile(response.data.html)

                            this_this.completed_step = this_this.step_numbers[response.data.jump_to_section] - 1;
                            this_this.current_step = this_this.step_numbers[response.data.jump_to_section];

                            shippingMethods = response.data.shippingMethods;
                            this_this.saveShipping();
                            this_this.getOrderSummary();
                        })
                        .catch(function (error) {
                            this_this.disable_button = false;
                            this_this.handleErrorResponse(error.response, 'address-form')

                            return 'error';
                            
                        })
                },

                saveShipping: function() {

                    var this_this = this;
                    this.disable_button = true;

                    this.$http.post("{{ route('shop.checkout.save-shipping') }}", {'shipping_method': this.selected_shipping_method})
                        .then(function(response) {
                            this_this.disable_button = false;

                            paymentHtml = Vue.compile(response.data.html)
                            // this_this.completed_step = this_this.step_numbers[response.data.jump_to_section] - 1;
                            // this_this.current_step = this_this.step_numbers[response.data.jump_to_section];

                            //this_this.completed_step = this_this.step_numbers[response.data.jump_to_section] - 1;
                            this_this.current_step = 1;
                            paymentMethods = response.data.paymentMethods;
                            this_this.savePayment();
                            this_this.getOrderSummary();
                        })
                        .catch(function (error) {
                            this_this.disable_button = false;

                            this_this.handleErrorResponse(error.response, 'shipping-form')
                        })
                },

                savePayment: function() {

                    var this_this = this;
                    this.disable_button = true;
                    this.$http.post("{{ route('shop.checkout.save-payment') }}", {'payment': this.selected_payment_method})
                    .then(function(response) {
                        this_this.disable_button = false;

                        reviewHtml = Vue.compile(response.data.html)
                        //this_this.completed_step = this_this.step_numbers[response.data.jump_to_section] - 1;
                        //this_this.current_step = this_this.step_numbers[response.data.jump_to_section];
                        this_this.current_step = 3;
                        this_this.prev_step = 1;
						window.scrollTo(0,0);
                        this_this.getOrderSummary();
                        
                    })
                    .catch(function (error) {
                        this_this.disable_button = false;
                        this_this.handleErrorResponse(error.response, 'payment-form')
                    });
                },

                backCustomrInfo: function()
                {
                    this.current_step = 1;
                    this.prev_step = 0
					window.scrollTo(0,0);
                },

                placeOrder: function() {
                    var this_this = this;

                    this.disable_button = true;

                    this.$http.post("{{ route('shop.checkout.save-order') }}", {'_token': "{{ csrf_token() }}"})
                    .then(function(response) {
                        if (response.data.success) {
                            if (response.data.redirect_url) {
                                window.location.href = response.data.redirect_url;
                            } else {
                                window.location.href = "{{ route('shop.checkout.success') }}";
                            }
                        }
                    })
                    .catch(function (error) {
                        this_this.disable_button = true;

                        window.flashMessages = [{'type': 'alert-error', 'message': "{{ __('shop::app.common.error') }}" }];

                        this_this.$root.addFlashMessages()
                    })
                },

                handleErrorResponse: function(response, scope) {
                    
                    if (response.status == 422) {
                        serverErrors = response.data.errors;
                        this.$root.addServerErrors(scope)
                    } else if (response.status == 403) {
                        if (response.data.redirect_url) {
                            window.location.href = response.data.redirect_url;
                        }
                    }
                },

                shippingMethodSelected: function(shippingMethod) {
                    this.selected_shipping_method = shippingMethod;
                },

                paymentMethodSelected: function(paymentMethod) {
                    this.selected_payment_method = paymentMethod;
                },

                newBillingAddress: function() {
                    this.address.billing.address_id = 0
                    this.new_billing_address = true;
                },

                newShippingAddress: function() {
                    this.address.shipping.address_id = 0
                    this.new_shipping_address = true;
                },

                backToSavedBillingAddress: function() {
                    if(customerAddress.length > 0){
                        this.address.billing.address_id = customerAddress[0].id
                    }
                    this.new_billing_address = false;
                },

                backToSavedShippingAddress: function() {
                    if(customerAddressShipping.length > 0){
                        this.address.shipping.address_id = customerAddressShipping[0].id
                    }
                    this.new_shipping_address = false;
                },
            }
        })

        var shippingTemplateRenderFns = [];

        Vue.component('shipping-section', {
            inject: ['$validator'],

            data: function() {
                return {
                    templateRender: null,
                    selected_shipping_method: '',
                    first_iteration : true,
                }
            },

            staticRenderFns: shippingTemplateRenderFns,

            mounted: function() {
                
                for (method in shippingMethods) {
                    if (this.first_iteration) {
                        for (rate in shippingMethods[method]['rates']) {
                            this.selected_shipping_method = shippingMethods[method]['rates'][rate]['method'];
                            this.first_iteration = false;
                            this.methodSelected();
                        }
                    }
                }

                this.templateRender = shippingHtml.render;
                for (var i in shippingHtml.staticRenderFns) {
                    shippingTemplateRenderFns.push(shippingHtml.staticRenderFns[i]);
                }

                eventBus.$emit('after-checkout-shipping-section-added');
            },

            render: function(h) {
                return h('div', [
                    (this.templateRender ?
                        this.templateRender() :
                        '')
                    ]);
            },

            methods: {
                methodSelected: function() {
                    this.$emit('onShippingMethodSelected', this.selected_shipping_method)

                    eventBus.$emit('after-shipping-method-selected');
                }
            }
        });

        var paymentTemplateRenderFns = [];

        Vue.component('payment-section', {
            inject: ['$validator'],

            data: function() {
                return {
                    templateRender: null,

                    payment: {
                        method: ""
                    },

                    first_iteration : true,
                }
            },

            staticRenderFns: paymentTemplateRenderFns,

            mounted: function() {
                for (method in paymentMethods) {
                    if (this.first_iteration) {
                        this.payment.method = paymentMethods[method]['method'];
                        this.first_iteration = false;
                        this.methodSelected();
                    }
                }

                this.templateRender = paymentHtml.render;
                for (var i in paymentHtml.staticRenderFns) {
                    paymentTemplateRenderFns.push(paymentHtml.staticRenderFns[i]);
                }

                eventBus.$emit('after-checkout-payment-section-added');
            },

            render: function(h) {
                return h('div', [
                    (this.templateRender ?
                        this.templateRender() :
                        '')
                    ]);
            },

            methods: {
                methodSelected: function() {
                    this.$emit('onPaymentMethodSelected', this.payment)

                    eventBus.$emit('after-payment-method-selected');
                }
            }
        })

        var reviewTemplateRenderFns = [];

        Vue.component('review-section', {
            data: function() {
                return {
                    templateRender: null,
                    error_message: ''
                }
            },

            staticRenderFns: reviewTemplateRenderFns,

            render: function(h) {
                return h('div', [
                    (this.templateRender ?
                        this.templateRender() :
                        '')
                    ]);
            },

            mounted: function() {
                this.templateRender = reviewHtml.render;

                for (var i in reviewHtml.staticRenderFns) {
                    // reviewTemplateRenderFns.push(reviewHtml.staticRenderFns[i]);
                    reviewTemplateRenderFns[i] = reviewHtml.staticRenderFns[i];
                }

                this.$forceUpdate();
            }
        });

        var summaryTemplateRenderFns = [];

        Vue.component('summary-section', {
            inject: ['$validator'],

            props: {
                discount: {
                    type: [String, Number],
                    default: 0,
                }
            },

            data: function() {
                return {
                    templateRender: null,
                    coupon_code: null,
                    error_message: null,
                    couponChanged: false,
                    changeCount: 0
                }
            },

            staticRenderFns: summaryTemplateRenderFns,

            mounted: function() {
                this.templateRender = summaryHtml.render;

                for (var i in summaryHtml.staticRenderFns) {
                    // summaryTemplateRenderFns.push(summaryHtml.staticRenderFns[i]);
                    summaryTemplateRenderFns[i] = summaryHtml.staticRenderFns[i];
                }

                this.$forceUpdate();
            },

            render: function(h) {
                return h('div', [
                    (this.templateRender ?
                        this.templateRender() :
                        '')
                    ]);
            },

            methods: {
                onSubmit: function() {
                    var this_this = this;
                    const emptyCouponErrorText = "Please enter a coupon code";
                    axios.post('{{ route('shop.checkout.check.coupons') }}', {code: this_this.coupon_code})
                        .then(function(response) {
                            this_this.$emit('onApplyCoupon');

                            this_this.couponChanged = true;
                        })
                        .catch(function(error) {
                            this_this.couponChanged = true;

                            this_this.error_message = (error.response.data.message === "The given data was invalid.")?
                                emptyCouponErrorText :
                                    (error.response.data.message === "Cannot Apply Coupon")?
                                        "Sorry, this Coupon code is invalid":error.response.data.message;
                        });
                },

                changeCoupon: function() {
                    if (this.couponChanged == true && this.changeCount == 0) {
                        this.changeCount++;

                        this.error_message = null;

                        this.couponChanged = false;
                    } else {
                        this.changeCount = 0;
                    }
                },

                removeCoupon: function () {
                    var this_this = this;

                    axios.post('{{ route('shop.checkout.remove.coupon') }}')
                        .then(function(response) {
                            this_this.$emit('onRemoveCoupon')
                        })
                        .catch(function(error) {
                            window.flashMessages = [{'type' : 'alert-error', 'message' : error.response.data.message}];

                            this_this.$root.addFlashMessages();
                        });
                }
            }
        })

    </script>

@endpush