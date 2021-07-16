@if (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts()->count())
    <section class="featured-products">

        <div class="featured-heading">
            {{ __('shop::app.home.featured-products') }}<br/>

             



            <span class="featured-seperator" style="color:lightgrey;">_____</span>

        </div>

        <div class="container">
           
            <div class="featured-grid product-grid-4 row">

            @foreach (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts() as $productFlat)

                @include ('shop::products.list.footer-new-product', ['product' => $productFlat])

            @endforeach

            </div>
        </div>

          <div class="container">
           
            <div class="featured-grid product-grid-4 row">

            @foreach (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts() as $productFlat)

                @include ('shop::products.list.footer-new-product', ['product' => $productFlat])

            @endforeach

            </div>
        </div>
    

    </section>
@endif