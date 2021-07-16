{!! view_render_event('bagisto.shop.products.view.stock.before', ['product' => $product]) !!}

@if (isset($product->v_qty) && $product->v_qty != NULL)
<div class="stock-status {{! $product->v_qty > 0 ? '' : 'active' }}">
    {{ $product->v_qty > 0 ? __('shop::app.products.in-stock') : __('shop::app.products.out-of-stock') }}
</div>
@else
<div class="stock-status active">
    {{ $product->haveSufficientQuantity(1) ? __('shop::app.products.in-stock') : __('shop::app.products.out-of-stock') }}
</div>
@endif

{!! view_render_event('bagisto.shop.products.view.stock.after', ['product' => $product]) !!}