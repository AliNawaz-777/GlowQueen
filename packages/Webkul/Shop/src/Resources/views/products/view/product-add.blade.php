{!! view_render_event('bagisto.shop.products.view.product-add.after', ['product' => $product]) !!}


@if ($product->haveSufficientQuantity(1) && $product->v_qty == NULL)
    <div class="add-to-buttons">
        @include ('shop::products.add-to-cart', ['product' => $product])
    
        @include ('shop::products.buy-now')
    </div>
@elseif (isset($product->v_qty) && $product->v_qty != NULL && $product->v_qty > 0)
    <div class="add-to-buttons">
        @include ('shop::products.add-to-cart', ['product' => $product])
    
        @include ('shop::products.buy-now')
    </div>
@endif

{!! view_render_event('bagisto.shop.products.view.product-add.after', ['product' => $product]) !!}