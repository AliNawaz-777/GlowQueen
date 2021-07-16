@if (app('Webkul\Product\Repositories\ProductRepository')->getNewProducts()->count())
    <section class="featured-products">

        <span class="list-heading">
            {{ __('shop::app.home.new-products') }}

        </span>

        <div class="product-grid-4">

            @foreach (app('Webkul\Product\Repositories\ProductRepository')->getNewProducts() as $productFlat)

                @include ('shop::products.list.card', ['product' => $productFlat])

            @endforeach

        </div>

    </section>
@endif
