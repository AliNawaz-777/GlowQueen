{!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

<a class="btn-wrap" href="{{ route('shop.products.index', $product->url_key) }}">{!! $product->getTypeInstance()->getPriceHtml() !!}</a>


{!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}