{{-- @dd(Webkul\Product\Models\Blog::orderBy('id', 'desc')->where('status','active')->limit(3)->get()); --}}

@if ((Webkul\Product\Models\Blog::orderBy('id', 'desc')->where('status','=','confirm')->limit(3)->get())->count())

<section id="new-sec" class="new-sec section-padding">
		<div class="container">
			{{-- <h1>{{ __('shop::app.home.new-products') }}</h1> --}}
			<h1>latest from new</h1>
			<p>
			<span>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</span>
				tempor incididunt ut labore et dolore magna aliqua. </p>
			<div class="new-sec-wrap">
				  @foreach (Webkul\Product\Models\Blog::orderBy('id', 'desc')->where('status','=','confirm')->limit(3)->get() as $productFlat)

				   @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

    <?php $productBaseImage = $productImageHelper->getProductBaseImage($productFlat); ?>

                {{-- @include ('shop::products.list.card', ['product' => $productFlat]) --}}

              <div class="new-item">
				<div class="img-holder item1"  style="background-image: url('{{ $productBaseImage['medium_image_url'] }}');">
						<div><a href="{{ route('shop.blog.detail', $productFlat->url_key) }}" class="btn btn-theme-white read-btn-des">
							<span>read more</span></a>
						</div>
					</div>
					<h3>{{$productFlat->blog_title}}</h3>
				</div>

            @endforeach

			
				{{-- <div class="new-item">
					<div class="img-holder item2">
						<a href="#" class="btn btn-theme-white read-btn-des">
							<span>read more</span></a>
					</div>
					<h3>Blessing - The business of fashion</h3>
				</div>
				<div class="new-item">
					<div class="img-holder item3">
						<a href="#" class="btn btn-theme-white read-btn-des">
							<span>read more</span></a>
					</div>
					<h3>Girls Fashion Accessories - Quality Experiment</h3>
				</div> --}}
			</div>
			
		</div>


</section>

@endif