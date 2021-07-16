<div class="form-container">
    <div class="form-header mb-30">
        <span class="checkout-step-heading">{{ __('shop::app.checkout.onepage.summary') }}</span>
    </div>

    <div class="address-summary">
        @if ($billingAddress = $cart->billing_address)
            <div class="billing-address">
                <div class="card-title mb-20">
                    <b>{{ __('shop::app.checkout.onepage.billing-address') }}</b>
                </div>

                <div class="card-content">
                    <ul>
                        <li class="mb-10">
                            {{ $billingAddress->name }}
                        </li>
                        @if($billingAddress->address1 != "")
                            
                            <li class="mb-10"> {{ $billingAddress->address1 }} , <br/>  </li>

                        @endif

                        @if($billingAddress->city != "")
                            
                            <li class="mb-10"> {{ ucfirst(strtolower($billingAddress->city)) }} {{ ($billingAddress->postcode) ? ' - ' .$billingAddress->postcode : '' }} , <br/>  </li>

                        @endif

                        @if($billingAddress->state != "")
                            
                            <li class="mb-10"> {{ $billingAddress->state }} , <br/>  </li>

                        @endif

                        <li class="mb-10">
                            {{ core()->country_name($billingAddress->country) }}.
                        </li>

                        <span class="horizontal-rule mb-15 mt-15"></span>

                        <li class="mb-10">
                            {{ __('shop::app.checkout.onepage.contact') }} : {{ $billingAddress->phone }}
                        </li>
                    </ul>
                </div>
            </div>
        @endif

        @if ($cart->haveStockableItems() && $shippingAddress = $cart->shipping_address)
            <div class="shipping-address">
                <div class="card-title mb-20">
                    <b>{{ __('shop::app.checkout.onepage.shipping-address') }}</b>
                </div>

                <div class="card-content">
                    <ul>
                        <li class="mb-10">
                            {{ $shippingAddress->name }}
                        </li>

                        @if($shippingAddress->address1 != "")
                            
                            <li class="mb-10"> {{ $shippingAddress->address1 }} , <br/>  </li>

                        @endif

                        @if($shippingAddress->city != "")
                            
                            <li class="mb-10"> {{ ucfirst(strtolower($shippingAddress->city)) }} {{ ($shippingAddress->postcode) ? ' - ' .$shippingAddress->postcode : '' }} , <br/>  </li>

                        @endif

                        @if($shippingAddress->state != "")
                            
                            <li class="mb-10"> {{ $shippingAddress->state }} , <br/>  </li>

                        @endif

                        <li class="mb-10">
                            {{ core()->country_name($shippingAddress->country) }} .
                        </li>

                        <span class="horizontal-rule mb-15 mt-15"></span>

                        <li class="mb-10">
                            {{ __('shop::app.checkout.onepage.contact') }} : {{ $shippingAddress->phone }}
                        </li>
                    </ul>
                </div>
            </div>
        @endif

    </div>

    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

    <div class="cart-item-list mt-20">
        @foreach ($cart->items as $item)
            @php
                $productBaseImage = $item->product->getTypeInstance()->getBaseImage($item);
            @endphp
            <div class="checkout_page_tblWrap">
                <table class="theme-bordered-tbl product_tbl">
                    <tbody>
                        <tr>
                            <td>Product</td>
                            <td>Product Name</td>
                            <td>Quantity</td>
                            <td>Price</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="item-image">
                                    <img src="{{ $productBaseImage['medium_image_url'] }}" />
                                </div>
                            </td>
                            <td>
                                {!! view_render_event('bagisto.shop.checkout.name.before', ['item' => $item]) !!}
                                <div class="item-title">
                                    {{ $item->product->name }}
                                </div>
                                {!! view_render_event('bagisto.shop.checkout.name.after', ['item' => $item]) !!}
                            </td>
                            <td>
                                <span class="value">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="value">
                                    {{ core()->currency($item->base_price) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="order-description mt-20">
        <div class="pull-left" style="width: 60%; float: left;">
            @if ($cart->haveStockableItems())
                <div class="shipping">
                    <div class="decorator">
                        <i class="icon shipping-icon"></i>
                    </div>

                    <div class="text">
                        {{ core()->currency($cart->selected_shipping_rate->base_price) }}

                        <div class="info">
                            {{ $cart->selected_shipping_rate->method_title }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="payment">
                <div class="decorator">
                    <i class="icon payment-icon"></i>
                </div>

                <div class="text">
                    {{ core()->getConfigData('sales.paymentmethods.' . $cart->payment->method . '.title') }}
                </div>
            </div>

        </div>

        <div class="pull-right" style="width: 40%; float: left;">
            <slot name="summary-section"></slot>
        </div>
    </div>
</div>