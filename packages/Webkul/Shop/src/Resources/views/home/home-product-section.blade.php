	
	@if (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts()->count())

	<section id="product" class="product-wrap section-padding">
		<div class="container">
			 
			<h1>{{ __('shop::app.home.featured-products') }}</h1>
			<p style="display: block;"><span>Every solitaire tells a story of festive sparkle.</span>
				 Get timeless designs with unmatched craftsmanship and high-quality gemstones. </p>
			<div class="product-detail">
				@foreach (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts() as $productFlat)



                @include ('shop::products.list.footer-new-product', ['product' => $productFlat])

            @endforeach
        	</div>
				
		</div>

		<div class="container">
			 
		{{-- 	<div class="product-detail">
				@foreach (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts() as $productFlat)

				

                @include ('shop::products.list.footer-new-product', ['product' => $productFlat])

            @endforeach

        </div> --}}
        
        
    </div>

    	

	</section>

	@endif

