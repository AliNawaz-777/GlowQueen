@if (app('Webkul\Product\Repositories\ProductRepository')->Deal()->count())

<section id="kids-sec" class="kids-sec">
                    <div class="container">

<div class="bottom-side">
                            <h2>deal of the day</h2>
                        </div>
                        <div class="owl-carousel owl-theme kids-slider">
                            @foreach (app('Webkul\Product\Repositories\ProductRepository')->Deal() as $productFlat)
                   

                @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

                 <?php $productBaseImage = $productImageHelper->getProductBaseImage($productFlat); ?>

                      

                                <div class="item">
                                <div class="kids-sec-wrap">
                                    <div class="kids-item">
                                        <div class="k-item">
                                            <img src="{{ $productBaseImage['medium_image_url'] }}" onerror="this.src='{{ asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png') }}'" alt="" class="img-fluid">
                                            <div class="content-overlay"></div>
                                            @include('shop::products.add-button-xs', ['product' => $productFlat])
                                        </div>
                                        <h3>{{$productFlat->name}}</h3>
                                        <a href="javascript:void(0)">{!! $productFlat->getTypeInstance()->getPriceHtml() !!}</a>
                                    </div>
                                </div>
                            </div>  

                                @endforeach



     
  
                          
                        </div>

            </div>
        </section>

        @endif