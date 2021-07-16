  

@if (app('Webkul\Product\Repositories\ProductRepository')->Kidsproducts()->count())
<section id="kids-sec" class="kids-sec">
                    <div class="container">
                        <div class="top-side">
                            <img src="{{ bagisto_asset('images/corner.svg') }}" class="img-fluid">
                            <h2>Sale Corner</h2>
                        </div>
                        <div class="owl-carousel owl-theme kids-slider">

            @foreach (app('Webkul\Product\Repositories\ProductRepository')->Kidsproducts() as $productFlat)
            @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

            <?php $productBaseImage = $productImageHelper->getProductBaseImage($productFlat); ?>
         {{--    <div class="item">
                <div class="kids-sec-wrap">
                                    <div class="kids-item">
                                        <div class="k-item ">
                                            <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">

                                        </div>
                                        <h3>{{$productFlat->name}}</h3>
                                        <a href="#">{!! $productFlat->getTypeInstance()->getPriceHtml() !!}</a>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="item">
                                <div class="kids-sec-wrap">
                                    <div class="kids-item">
                                        <div class="k-item ">
                                            <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">
                                            <div class="content-overlay"></div>
                                            @include('shop::products.add-button-xs', ['product' => $productFlat])
                                        </div>
                                        <h3>{{$productFlat->name}}</h3>
                                        <a href="javascript:void(0)">{!! $productFlat->getTypeInstance()->getPriceHtml() !!}</a>
                                    </div>
                                </div>
                            </div>

                {{-- @include ('shop::products.list.card', ['product' => $productFlat]) --}}

            @endforeach

                            
                     
                    </div>
                </section>

        @endif

@push('scripts')

<script type="text/javascript">

    $(document).ready(function(e){
    $('.kids-slider').owlCarousel({
        loop:true,
        nav:true,
        autoplay:true,
        autoplayTimeout:5000,
        navText: [
            '<i class="fa fa-angle-left" aria-hidden="true"></i>',
            '<i class="fa fa-angle-right" aria-hidden="true"></i>'
        ],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            767:{
                items:2
            },
            1199:{
                items:4
            },
        }
    });
});
    



</script>


@endpush