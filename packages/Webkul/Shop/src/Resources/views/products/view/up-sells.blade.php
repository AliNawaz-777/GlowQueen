{!! view_render_event('bagisto.shop.products.view.up-sells.after', ['product' => $product]) !!}

<?php
    $productUpSells = $product->up_sells()->get();
?>

@if ($productUpSells->count())
<section id="product" class="product-wrap section-padding">
<div class="container">
    <div class="related-products sale-products attached-products-wrapper">

        <div class="title">
            {{ __('shop::app.products.up-sell-title') }}
            <span class="border-bottom"></span>
        </div>

        <div class="product-detail">

            @foreach ($productUpSells as $up_sell_product)

                {{-- @include ('shop::products.list.card', ['product' => $up_sell_product]) --}}
                @include ('shop::products.list.footer-new-product', ['product' => $up_sell_product])


            @endforeach

        </div>

    </div>
</div>
</section>
@endif

{!! view_render_event('bagisto.shop.products.view.up-sells.after', ['product' => $product]) !!}