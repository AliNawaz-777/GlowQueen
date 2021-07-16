@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

<?php $cart = cart()->getCart();
    if(isset($cart->items) && !empty($cart->items))	
    {
        foreach($cart->items as $key => $item)
        {   
            $result = cart()->checkProductActive($item->product_id);
            if(empty($result))
            {
               cart()->removeItem($item->id);
            }
        }
    }
?>

@if ($cart)
    <?php $items = $cart->items; ?>

    <div id="cart-badge" class="dropdown-toggle">
        <div style="display: inline-block; cursor: pointer; position: relative">
            <a class="cart-link" href="{{ route('shop.checkout.cart.index') }}">
                <span class="icon"><img src="{{ bagisto_asset('images/cart.svg') }}"></span>
                <span class="cart-qty count my-count"> {{ $cart->items->count() }}</span>
            </a>
        </div>
    </div>

    <div id="cart-list" class="dropdown-list custom-drop-down-list" style="display: none; top: 52px; right: 0px;">
        <div class="dropdown-container">
            <div class="dropdown-cart">
                <div class="dropdown-header">
                    <p class="heading">
                        {{ __('shop::app.checkout.cart.cart-subtotal') }} -

                        {!! view_render_event('bagisto.shop.checkout.cart-mini.subtotal.before', ['cart' => $cart]) !!}

                        <b>{{ core()->currency($cart->base_sub_total) }}</b>

                        {!! view_render_event('bagisto.shop.checkout.cart-mini.subtotal.after', ['cart' => $cart]) !!}
                    </p>
                </div>
                <div class="dropdown-content">
                    @foreach ($items as $item)
                        <div class="item">
                            <div class="item-image" >
                                @php
                                    $images = $item->product->getTypeInstance()->getBaseImage($item);
                                @endphp
                                <a href="{{ url('products/'.core()->getProductSlug($item->product_id)) }}"><img src="{{ $images['small_image_url'] }}" /></a>
                            </div>

                            <div class="item-details">
                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.name.before', ['item' => $item]) !!}

                                <div class="item-name"><a href="{{ url('products/'.core()->getProductSlug($item->product_id)) }}">{{ $item->name }}</a></div>

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.name.after', ['item' => $item]) !!}


                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.options.before', ['item' => $item]) !!}
                                
                                @if (isset($item->additional['attributes']))
                                    <div class="item-options">
                                        
                                        @foreach ($item->additional['attributes'] as $attribute)
                                            <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                        @endforeach

                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.options.after', ['item' => $item]) !!}


                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.price.before', ['item' => $item]) !!}

                                <div class="item-price"><b>{{ core()->currency($item->base_total) }}</b></div>

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.price.after', ['item' => $item]) !!}


                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.quantity.before', ['item' => $item]) !!}

                                <div class="item-qty">Quantity - {{ $item->quantity }}</div>

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.quantity.after', ['item' => $item]) !!}
                            </div>
                        </div>

                    @endforeach
                </div>

                <div class="dropdown-footer">
                    <a class="btn btn-primary" id="custom-view-cart" href="{{ route('shop.checkout.cart.index') }}">{{ __('shop::app.minicart.view-cart') }}</a>

                    <a class="btn btn-primary btn-lg" id="custom-buy-btn" style="color: white;" href="{{ route('shop.checkout.onepage.index') }}">{{ __('shop::app.minicart.checkout') }}</a>
                </div>
            </div>
        </div>
    </div>

@else

    <div id="cart-badge" class="dropdown-toggle">
        <div style="display: inline-block; cursor: pointer; position: relative">
            <a class="cart-link" href="javascript:void(0)">
                <span class="icon"><img src="{{ bagisto_asset('images/cart.svg') }}"></span>
                <span class="cart-qty count my-count">0</span>
            </a>
        </div>
    </div>
    <div id="cart-list" class="dropdown-list custom-drop-down-list" style="display: none; top: 52px; right: 0px;">
        <div class="dropdown-container">
            <div class="dropdown-cart">
                <div class="dropdown-header">
                    <p class="heading"></p>
                </div>

                <div class="dropdown-content">
                    <div class="no-cart">
                        <p>No item(s) in cart</p>
                    </div>
                </div>
                <div class="dropdown-footer">
                    {{-- {{ route('shop.checkout.cart.index') }} --}}
                    <a disabled id="custom-view-cart" class="btn btn-primary" href="javascript:void(0)">{{ __('shop::app.minicart.view-cart') }}</a>
                    {{-- {{ route('shop.checkout.onepage.index') }} --}}
                    <a disabled id="custom-buy-btn" class="btn btn-primary btn-lg" style="color: white;" href="javascript:void(0)">{{ __('shop::app.minicart.checkout') }}</a>
                </div>
            </div>
        </div>
    </div>
@endif


