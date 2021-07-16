{!! view_render_event('bagisto.shop.products.list.card.before', ['product' => $product]) !!}

<?php if(isset($product->sale) && $product->sale != 0) { ?>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')
    @php $productBaseImage = $productImageHelper->getProductBaseImage($product); @endphp
    <div class="product-detail box-spacing ">
        <a href="{{ route('shop.products.index', $product->url_key) }}">
            <div class="img-wrap">
                <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" class="img-fluid"/>
                <div class="sku-wrapper">{{ $product->sku }}</div>
            </div>
            <div class="box-bottom-wrap">
                <div class="content-wrap">
                    <p>{{ $product->name }}</p>
                </div>
                <div class="price-holder">
                    @inject ('homeController', 'Webkul\Shop\Http\Controllers\HomeController')
                    @if(Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                        {!! $homeController->getProductPrice(1,core()->getBaseCurrency()->symbol,$product) !!}
                    @else 
                        {!! $homeController->getProductPrice(0,core()->getBaseCurrency()->symbol,$product) !!}
                    @endif
                </div>
            </div>
        </a>
        @include('shop::products.add-button-xs', ['product' => $product])
    </div>
</div>

<?php }elseif(isset($product->hot) && $product->hot != 0) { ?>

<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')
    @php $productBaseImage = $productImageHelper->getProductBaseImage($product); @endphp
    <div class="product-detail box-spacing ">
        <a href="{{ route('shop.products.index', $product->url_key) }}">
            <div class="img-wrap">
                <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" class="img-fluid"/>
            </div>
            <div class="box-bottom-wrap">
                <div class="content-wrap">
                    <p>{{ $product->name }}</p>
                </div>
                <div class="price-holder">
                    @inject ('homeController', 'Webkul\Shop\Http\Controllers\HomeController')
                    @if(Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                        {!! $homeController->getProductPrice(1,core()->getBaseCurrency()->symbol,$product) !!}
                    @else 
                        {!! $homeController->getProductPrice(0,core()->getBaseCurrency()->symbol,$product) !!}
                    @endif
                </div>
            </div>
        </a>
        @include('shop::products.add-button-xs', ['product' => $product])
    </div>
</div>
        
 
    <!-- <div class="product-inner-wrap offer-hover-des">
        @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

        @php //$productBaseImage = $productImageHelper->getProductBaseImage($product); @endphp
        <div class="p-item hot-offer">
            <a href="{{ route('shop.products.index', $product->url_key) }}">
            <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">
            <div class="content-overlay"></div>
        </a>
            @include('shop::products.add-button-xs', ['product' => $product])
        </div>
        <h3> {{ $product->name }}</h3>
        @include ('shop::products.price', ['product' => $product])
    </div> -->

<?php } elseif((isset($product->qty) && $product->qty == 0) || ($product->v_qty != null && $product->v_qty == 0)) { ?>
<div class="product-inner-wrap offer-hover-des">
     @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

    <?php $productBaseImage = $productImageHelper->getProductBaseImage($product); ?>
    <div class="p-item">
        
        <a href="{{ route('shop.products.index', $product->url_key) }}" >
        <span class="triangle">
            <span>OUT OF STOCK</span>
        </span>
        <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">
        <div class="content-overlay"></div>
    </a>
        @include('shop::products.add-button-xs', ['product' => $product])
    </div>
    <h3> {{ $product->name }}</h3>
    {{-- <a href="#">PKR 700</a> --}}
    @include ('shop::products.price', ['product' => $product])
</div>

<?php } else { ?>
    
<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')
    @php $productBaseImage = $productImageHelper->getProductBaseImage($product); @endphp
    <div class="product-detail box-spacing ">
        <a href="{{ route('shop.products.index', $product->url_key) }}">
            <div class="img-wrap">
                <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" class="img-fluid"/>
                <div class="sku-wrapper">{{ $product->sku }}</div>
            </div>
            <div class="box-bottom-wrap">
                <div class="content-wrap">
                    <p>{{ $product->name }}</p>
                </div>
                <div class="price-holder">
                    @inject ('homeController', 'Webkul\Shop\Http\Controllers\HomeController')
                    @if(Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                        {!! $homeController->getProductPrice(1,core()->getBaseCurrency()->symbol,$product) !!}
                    @else 
                        {!! $homeController->getProductPrice(0,core()->getBaseCurrency()->symbol,$product) !!}
                    @endif
                </div>
            </div>
        </a>
        @include('shop::products.add-button-xs', ['product' => $product])
    </div>
</div>
{{--
<!-- <div class="product-inner-wrap">
    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

    @php //$productBaseImage = $productImageHelper->getProductBaseImage($product); @endphp

    <div class="p-item">
        <a href="{{ route('shop.products.index', $product->url_key) }}">
            <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" class="img-fluid"/>
            <div class="content-overlay"></div>
        </a>

        @include('shop::products.add-button-xs', ['product' => $product])

        
    </div>
    <h3> {{ $product->name }}</h3>
    @include ('shop::products.price', ['product' => $product])
</div> -->

    --}}

<?php } ?>

{!! view_render_event('bagisto.shop.products.list.card.after', ['product' => $product]) !!}