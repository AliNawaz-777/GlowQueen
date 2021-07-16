{{-- @dd(Webkul\Product\Models\Blog::orderBy('id', 'desc')->where('status','active')->limit(3)->get()); --}}


@if (app('Webkul\Product\Repositories\ProductRepository')->getHomeLatestProducts()->count())
<section id="new-sec" class="new-sec section-padding">
        <div class="container">
            {{-- <h1>{{ __('shop::app.home.new-products') }}</h1> --}}
            <h1 class="sec-title-wrap">Recommended For You</h1>
            <p>
            We care for your skin! Take a round of the highly sought-after beauty products. This self-care is surely going to be iconic.  </p>
            <div class="new-sec-wrap">
                  
            @foreach (app('Webkul\Product\Repositories\ProductRepository')->getHomeLatestProducts() as $productFlat)
            @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

            <?php $productBaseImage = $productImageHelper->getProductBaseImage($productFlat); ?>

                {{-- @include ('shop::products.list.card', ['product' => $productFlat]) --}}

              <div class="new-item">
                <div class="img-holder item1"  style="background-image: url('{{ $productBaseImage['medium_image_url'] }}');">
                        <div><a href="{{ route('shop.products.index', $productFlat->url_key) }}" class="btn btn-theme-white read-btn-des">
                            <span>Shop now</span></a>
                        </div>
                    </div>
                    <h3>{{$productFlat->name}}</h3>
                </div>

                @endforeach

            </div>
            
        </div>


</section>

@endif