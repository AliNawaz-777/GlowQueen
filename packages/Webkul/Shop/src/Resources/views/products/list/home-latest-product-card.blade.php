{!! view_render_event('bagisto.shop.products.list.home-latest-product-card.before', ['product' => $product]) !!}


<div class="home-latest-products-container item">

      @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

      <?php $productBaseImage = $productImageHelper->getProductBaseImage($product); ?>

	<div class="latest-product-img">
		<a href="{{ route('shop.products.index', $product->url_key) }}" title="{{ $product->name }}">
			<img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'"/>
		</a>
	</div>
	<div class="latest-product-detail">
		<div class="product-details">
			<div class="product-name">
	            <a href="{{ url()->to('/').'/products/' . $product->url_key }}" title="{{ $product->name }}">
	                <span>
	                    {{ $product->name }}
	                </span>
	            </a>
	        </div>
			<h6>@include ('shop::products.price', ['product' => $product])</h6>
			{{-- @include('shop::products.add-button-xs', ['product' => $product]) --}}
		</div>
	</div>
</div>



{!! view_render_event('bagisto.shop.products.list.home-latest-product-card.after', ['product' => $product]) !!}

