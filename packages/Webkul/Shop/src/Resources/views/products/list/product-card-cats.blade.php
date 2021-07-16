@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

<?php $productBaseImage = $productImageHelper->getProductBaseImage($product); $currency = DB::table('currencies')->where('code', Session::get('currency'))->selectRaw('symbol')->first(); ?>
<div class="product-detail box-spacing ">
	<a href="{{ route('shop.products.index', $product->url_key) }}">
		<div class="img-wrap">
			<img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">
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
	<div class="btn-holder">
        <form data-generated="{{ $product->product_id }}" class="addToCart" action="{{ route('cart.add', $product->product_id) }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-theme-orange"><span>Add to Cart</span></button>
            
            <button id="buy-now-d" name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" ><span>Buy Now</span></button>
        
        </form>
    </div>
</div>