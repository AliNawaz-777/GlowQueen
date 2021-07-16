@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')
@if (Webkul\Product\Helpers\ProductType::hasVariants($product->type))
    @inject ('configurableOptionHelper', 'Webkul\Product\Helpers\ConfigurableOption')
    @php $variants = $configurableOptionHelper->getConfigurationConfig($product);
        $j = 0;
        $variant_prices = array();
        foreach ($variants['variant_prices'] as $price) {
            $variant_prices[$j] = $price['final_price']['price'];
            $j++;
        }
    @endphp
@endif
<?php $productBaseImage = $productImageHelper->getProductBaseImage($product); $currency = DB::table('currencies')->where('code', Session::get('currency'))->selectRaw('symbol')->first(); ?>
<div class="sale_content">
    <div class="product-detail box-spacing">
        <a href="{{ route('shop.products.index', $product->url_key) }}">
            <div class="img-wrap">
                <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">
                <div class="sku_wrapper">
                    {{ $product->sku }}
                </div>
            </div>
            <div class="box-bottom-wrap">
                <div class="content-wrap">
                    <p>{{ $product->name }}</p>
                </div>
                <div class="price-holder">
                    <h2>@if (isset($product->special_price) && $product->special_price != NULL && $product->special_price != 0) hy @elseif (Webkul\Product\Helpers\ProductType::hasVariants($product->type)) hello @else hwre @endif</h2>
                    @if (!Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                    @if (isset($product->special_price) && $product->special_price != NULL && $product->special_price != 0)
                    <h3>{{ $currency->symbol }} {{ round($product->price) }} <span>@php echo "-" . round(($product->special_price / $product->price) * 100) . "%";  @endphp</span></h3>
                    @endif
                    @endif
                </div>
            </div>
        </a>
        <div class="btn-holder">
            <form action="{{ route('cart.add', $product->product_id) }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-theme-orange" {{ ! $product->isSaleable() ? 'disabled' : '' }}><span>Add to Cart</span></button>
                <button type="submit" class="btn btn-theme-white" {{ ! $product->isSaleable(1) ? 'disabled' : '' }}><span>Buy Now</span></button>
            </form>
        </div>
    </div>
</div>
