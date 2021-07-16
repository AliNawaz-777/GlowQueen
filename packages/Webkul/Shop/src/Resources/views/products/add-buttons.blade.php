<div class="cart-wish-wrap">
    <form action="{{ route('cart.add', $product->product_id) }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
        <input type="hidden" name="quantity" value="1">
        <button class="icon cart-icon addtocart" {{ $product->isSaleable() ? '' : 'disabled' }}></button>
    </form>

    @include('shop::products.wishlist')
</div>