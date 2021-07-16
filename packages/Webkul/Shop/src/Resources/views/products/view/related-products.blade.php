<?php
    $relatedProducts = $product->related_products()->get();
?>


@if ($relatedProducts->count())

<section id="product" class="product-wrap section-padding related-products">
 <div class="container">
    <h1>Related products</h1>
    <div class="owl-carousel owl-theme product-slider">
        @foreach ($relatedProducts as $related_product)
            @include ('shop::products.list.owl-items', ['product' => $related_product])

                {{-- @include ('shop::products.list.card', ['product' => $related_product]) --}}

        @endforeach
    </div>
</div>
</section>
@endif