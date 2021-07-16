{!! view_render_event('bagisto.shop.products.list.card.before', ['product' => $product]) !!}

<div class="product-card">

    @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

    <?php $productBaseImage = $productImageHelper->getProductBaseImage($product); ?>

    @if ($product->new)
        <div class="sticker new">
            {{ __('shop::app.products.new') }}
        </div>
    @endif

    <div class="product-image">
        <a href="{{ route('shop.products.index', $product->url_key) }}" title="{{ $product->name }}">
            <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'"/>
        </a>
    </div>

    <div class="product-information">

        <div class="product-name">
            <a href="{{ url()->to('/').'/products/' . $product->url_key }}" title="{{ $product->name }}">
                <span>
                    {{ $product->name }}
                </span>
            </a>
        </div>

        @include ('shop::products.price', ['product' => $product])

        {{-- @include('shop::products.add-buttons', ['product' => $product]) --}}
    </div>

        <div class="new-item">
            <div class="img-holder item1" style="background-image: url('{{ $productBaseImage['medium_image_url'] }}');">
                <div><a href="{{ route('shop.products.index', $product->url_key) }}" class="btn btn-theme-white read-btn-des">
                    <span> more</span></a>
                </div>
            </div>
            <h3>{{ $product->name }}</h3>
        </div>

        <div class="p-img-holder">
        <div class="img-box product-1" style="background-image: url('{{ $productBaseImage['medium_image_url'] }}')">
            
        </div>
    </div>

</div>

{!! view_render_event('bagisto.shop.products.list.card.after', ['product' => $product]) !!}