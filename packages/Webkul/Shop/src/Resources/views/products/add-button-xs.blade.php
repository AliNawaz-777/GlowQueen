<!-- <div class="cart-wish-wrap"> -->
<div class="btn-holder">

    <form data-generated="{{$product->product_id}}" action="{{ route('cart.add', $product->product_id) }}" method="POST" class="addToCart">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
        <input type="hidden" name="quantity" value="1">
        <!-- <div class="content-details fadeIn-top fadeIn-left">  -->
            @inject ('wishListHelper', 'Webkul\Customer\Helpers\Wishlist')

            @auth('customer')
                {!! view_render_event('bagisto.shop.products.wishlist.before') !!}
                <!-- <div class="icon-box">
                    <a @if ($wishListHelper->getWishlistProduct($product)) class="add-to-wishlist already" @else class="add-to-wishlist" @endif href="{{ route('customer.wishlist.add', $product->product_id) }}" id="wishlist-changer">
                        <span class="icon"><i class="fa fa-heart"></i></span>
                    </a>
                </div> -->
                {!! view_render_event('bagisto.shop.products.wishlist.after') !!}
            @endauth

                @if(Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                        

                    <button name="add_to_cart" type="submit" class="btn btn-theme-orange" {{ $product->isSaleable() ? '' : 'disabled' }}><span>Add to Cart</span></button>
                    <button name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" {{ $product->isSaleable() ? '' : 'disabled' }}><span>Buy Now</span></button>

                @elseif (isset($product->qty) && $product->qty != NULL)
                    

                    <button name="add_to_cart" type="submit" class="btn btn-theme-orange" {{ $product->isSaleable() ? '' : 'disabled' }}><span>Add to Cart</span></button>
                    <button name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" {{ $product->isSaleable() ? '' : 'disabled' }}><span>Buy Now</span></button>

                    <!-- <div class="icon-box"><button class="icon addtocart" {{ $product->isSaleable() ? '' : 'disabled' }}>
                    
                    <span class="icon addtocart">
                            <i class="fa fa-shopping-cart"></i>
                        </span>
                    
                    </button>
                    </div> -->
                
                @elseif (isset($product->v_qty) && $product->v_qty != NULL)
                    
                
                        <button name="add_to_cart" type="submit" class="btn btn-theme-orange" {{ $product->isSaleable() ? '' : 'disabled' }}><span>Add to Cart</span></button>
                        <button name="add_to_cart" type="submit" class="btn btn-theme-white buy-now-d" {{ $product->isSaleable() ? '' : 'disabled' }}><span>Buy Now</span></button>
                        <!-- <div class="icon-box"><button class="icon addtocart" {{ $product->v_qty != 0 ? '' : 'disabled' }}>
                        
                            <span class="icon addtocart">
                                    <i class="fa fa-shopping-cart"></i>
                                </span>
                        
                        </button>
                        </div> -->
                @endif
                        <!-- <div class="icon-box"><a href="{{ route('shop.products.index', $product->url_key) }}"><span class="icon info"><i class="fa fa-eye"></i></span></a>
                    </div>
                </div> -->
    </form>

    {{-- @include('shop::products.wishlist') --}}
</div>