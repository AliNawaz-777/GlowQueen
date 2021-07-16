@extends('shop::layouts.master')
@section('page_title')
    {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
@stop
@if (Webkul\Product\Helpers\ProductType::hasVariants($product->type))
    @inject ('configurableOptionHelper', 'Webkul\Product\Helpers\ConfigurableOption')
    @php $variants = $configurableOptionHelper->getConfigurationConfig($product);
        $j = 0;
        $variant_prices = array();
        foreach ($variants['variant_prices'] as $price) 
        {
            $variant_prices[$j] = $price['final_price']['price'];
            $j++;
        }
    @endphp
@endif

@section('seo')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : str_limit(strip_tags($product->description), 120, '') }}"/>
    <meta name="keywords" content="{{ $product->meta_keywords }}"/>
@stop

@section('content-wrapper')
    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}
    <!-- breadcrumbs -->
    <div class="top-box">
        <div class="container">
            <div class="breadcrumbs-wrap">
                <ul class="breadcrumb">                    
                    <li><a href="{{ URL('/') }}">Home</a></li>
                    <li><a href="{{ URL('/categories/'.$product->category_slug) }}">{{ $product->category_name }}</a></li>
                    <li>{{ $product->name }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End -->
    <section class="product-detail section-padding product-detail-wrap">

<div class="container">
        <div class="layouter">
            <product-view>
                <div class="form-container">
                    @csrf()

                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                    <!-- Gallery Section start -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 product-image-group">
                    @include ('shop::products.view.gallery')
                    </div>
                    <!-- Gallery Section end -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 details">

                        <div class="product-heading">
                            <span>{{ $product->name }}</span>
                        </div>
                        <!-- Review Section start -->
                        @inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
                        @php $total = $reviewHelper->getTotalReviews($product); @endphp
                            <div class="product-ratings">
                                @if ($total > 0)
                                <ul class="list-unstyled">
                                    @for ($i = 1; $i <= round($reviewHelper->getAverageRating($product)); $i++)
                                        <li><i class="fa fa-star"></i></li>
                                    @endfor
                                </ul>
                                @endif
                                <span style="margin-left: 0;">{{ $total }} Review(s)</span>
                            </div>
                        <!-- Review Section end -->
                        <!-- Info Section Starts -->
                        <div class="item-info">
                            @if ($product->total_sales > 0)
                            <span><span>{{ $product->total_sales }}</span> SOLD</span>
                            @endif
                            <span style="color:#000" class="item-detail">{!! $product->short_description !!} </span>
                            <br>

                            @inject ('configurableOptionHelper', 'Webkul\Product\Helpers\ConfigurableOption')
                
                            
                            @if (Webkul\Product\Helpers\ProductType::hasVariants($product->type))

                                @php $result = $configurableOptionHelper->getProdPrices(1,$product);@endphp    
                                <span class="org-price">    
                                    <h2 class="prod-price-org" style="color:#ff9f15">
                                        <sup style="color:#ff9f15">{{ $product->symbol }}</sup> 
                                            {{ $result['price'] }} 
                                    </h2>
                                    <h2 class="prod-price-new" style="color:#ff9f15">
                                        
                                    </h2>
                                </span>

                            @else
                                @php $result = $configurableOptionHelper->getProdPrices(0,$product);@endphp 
                                                       
                                @if($result['discount'] > 0)
                                    <span class="org-price">    
                                        <h2 class="prod-price-org" style="color:#ff9f15">
                                            <sup style="color:#ff9f15">{{ $product->symbol }}</sup> 
                                                {{ $result['price'] }} <span class="dis-price-wrap"><sup></span>
                                        </h2>
                                    </span>
                                    <del>{{ $product->symbol . ' ' .$result['org_price']}}-</del> {{$result['discount']}}
                                @else
                                    <span class="org-price">    
                                        <h2 class="prod-price-org" style="color:#ff9f15">
                                            <sup style="color:#ff9f15">{{ $product->symbol }}</sup> 
                                                {{ $result['price'] }} 
                                        </h2>
                                    </span>
                                @endif

                              @endif
                              
                                                    
                        </div>
                        <!-- Info Section ends -->
                        <!-- Stock info starts -->
                        <div class="stock-info">
                            <h3>
                                @if($product->p_qty > 0)
                                    @if ($product->p_qty <= 5 && $product->p_qty != NULL)HURRY, ONLY @endif
                                    <span>
                                         {{ $product->p_qty }} 
                                    </span> 
                                    ITEM(S) LEFT <span>IN STOCK</span>
                                @else 
                                    Stock Not Available!
                                @endif
                            </h3>
                        </div>
                         <!-- Wishlists and compare buttons ends -->
                        
                        <!---->
                        @if($product->total_quantity != 0 && $product->total_quantity > $product->total_order)
                            {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}
                            <!-- Configurable Section start -->
                            @include ('shop::products.view.configurable-options')
                            <!-- Configurable Section end -->
                            <!-- Downloadable Section start -->
                            @include ('shop::products.view.downloadable')
                            <!-- Downloadable Section end -->
                            <!-- Grouped Section start -->
                            @include ('shop::products.view.grouped-products')
                            <!-- Configurable Section end -->
                            <!-- Bundle Section start -->
                            @include ('shop::products.view.bundle-options')
                            <!-- Bundle Section end -->
                            <!-- Attributes Section start -->
                            @include ('shop::products.view.attributes')
                            <!-- Attributes Section end -->
                            <!-- Reviews Section start -->
                            <!--@include ('shop::products.view.reviews')-->
                            <!-- Reviews Section end -->
    
                            <!-- Stock info ends -->
                            <!-- Quantity starts -->
                            {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}
    
                            @if ($product->getTypeInstance()->showQuantityBox())
                                
                                <quantity-changer></quantity-changer>
    
                            @else
                                <input type="hidden" name="quantity" value="1">
                            @endif
                            <!-- Quantity ends -->
                            <!-- Wishtlist and compare buttons starts -->
                            @inject ('wishListHelper', 'Webkul\Customer\Helpers\Wishlist')
                            
                            <div class="whishlist">
                            <a id="add-whishlist-btn" class="add-whishlist {{ (!$wishListHelper->getWishlistProduct($product)) ? 'add-whishlist-btn' : ''  }}" href="javascript:void(0);">
                                    @if ($wishListHelper->getWishlistProduct($product))
                                    <i class="fa fa-heart"></i>
                                    @else
                                    <i class="fa fa-heart-o"></i>
                                    @endif
                                    <span>add to wishlist</span>
                                </a>
                            </div>
                        @endif
                       
                    </div>
                </div>
            </product-view>
        </div>
    </div>
  
   </section>   
    <!-- details start -->
        <div class="brand">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="brand-info">
                            <h1 class="text-orange">SKU:</h1>
                            <span>{{ $product->sku }}</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="brand-info">
                            <h1 class="text-orange">brand:</h1>
                            <span class="border-des">@if ($product->brand) {{ $product->brand }} @else No Brand @endif</span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="brand-info">
                            <h1 class="text-orange">category:</h1>
                            <span class="border-des">@if ($product->category_name) {{ $product->category_name }} @else No Category @endif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- details end -->
    
    <!-- Review -section -->
        <section class="review-tab">
            <div class="container">
                <div class="tab-headings">
                    <ul class="nav nav-tabs">
                        <li><a href="#home" class="tab-a active-a active btn-tab" data-id="tab1" data-toggle="tab">Description</a></li>
                        <li><a href="#menu1" class="tab-a btn-tab tab-2-des" data-id="tab2" data-toggle="tab">Add review</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div data-id="tab1" class="tab tab-active">
                        {!! $product->description !!}
                        @if (Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                        @php $i = 1; @endphp
                        @foreach($variants['attributes'] as $variant)
                        <div class="variant_options">
                            <div class="item-info">
                                <h1>{{ $variant['label'] }}</h1>
                                @foreach($variant['options'] as $option)

                                <span>@if($i !=1), @endif{{ $option['label'] }}</span>
                                @php $i++; @endphp
                                @endforeach
                            </div>
                        </div>
                        @php $i = 1; @endphp
                        @endforeach
                        @endif
                    </div>
                    <div data-id="tab2" class="tab">
                        <section class="review section-padding">

                            <div class="review-layouter">
                                @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')
                    
                                @inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
                    
                                <?php $productBaseImage = $productImageHelper->getProductBaseImage($product); ?>
                    
                                <div class="product-info">
                                    <div class="product-image">
                                        <a href="{{ route('shop.products.index', $product->url_key) }}" title="{{ $product->name }}">
                                            <img src="{{ $productBaseImage['medium_image_url'] }}" />
                                        </a>
                                    </div>
                    
                                    <div class="product-name mt-20">
                                        <a href="{{ url()->to('/').'/products/'.$product->url_key }}" title="{{ $product->name }}">
                                            <span>{{ $product->name }}</span>
                                        </a>
                                    </div>
                    
                                    <div class="product-price mt-10">
                                        @if ($product->getTypeInstance()->haveSpecialPrice())
                                            <span class="pro-price">{{ core()->currency($product->getTypeInstance()->getSpecialPrice()) }}</span>
                                        @else
                                            <span class="pro-price">{{ core()->currency($product->getTypeInstance()->getMinimalPrice()) }}</span>
                                        @endif
                                    </div>
                                </div>
                    
                                <div class="review-form">
                                    <div class="heading mt-10">
                                        <span> {{ __('shop::app.reviews.rating-reviews') }} </span>
                    
                                        @if (core()->getConfigData('catalog.products.review.guest_review') || auth()->guard('customer')->check())
                                            <a href="{{ route('shop.reviews.create', $product->url_key) }}" class="btn btn-lg btn-primary right">
                                                {{ __('shop::app.products.write-review-btn') }}
                                            </a>
                                        @endif
                                    </div>
                    
                                    <div class="ratings-reviews mt-35">
                                        <div class="left-side">
                                            <span class="rate">
                                                {{ $reviewHelper->getAverageRating($product) }}
                                            </span>
                    
                                            <span class="stars">
                                                @for ($i = 1; $i <= $reviewHelper->getAverageRating($product); $i++)
                    
                                                    <span class="icon star-icon"></span>
                    
                                                @endfor
                                            </span>
                    
                                            <div class="total-reviews mt-5">
                                                {{ __('shop::app.reviews.ratingreviews', [
                                                    'rating' => $reviewHelper->getTotalRating($product),
                                                    'review' => $reviewHelper->getTotalReviews($product)])
                                                }}
                                            </div>
                                        </div>
                    
                                        <div class="right-side">
                    
                                            @foreach ($reviewHelper->getPercentageRating($product) as $key => $count)
                                                <div class="rater 5star">
                                                    <div class="rate-number" id={{ $key }}{{ __('shop::app.reviews.id-star')  }}></div>
                                                    <div class="star-name">{{ __('shop::app.reviews.star') }}</div>
                                                    <div class="line-bar">
                                                        <div class="line-value" id="{{ $key }}"></div>
                                                    </div>
                                                    <div class="percentage">
                                                        <span>
                                                            {{ __('shop::app.reviews.percentage', ['percentage' => $count]) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <br/>
                                            @endforeach
                    
                                        </div>
                                    </div>
                    
                                    <div class="rating-reviews">
                                        <div class="reviews">
                    
                                            @foreach ($reviewHelper->getReviews($product)->paginate(10) as $review)
                                                <div class="review">
                                                    <div class="title">
                                                        {{ $review->title }}
                                                    </div>
                    
                                                    <span class="stars">
                                                        @for ($i = 1; $i <= $review->rating; $i++)
                    
                                                            <span class="icon star-icon"></span>
                    
                                                        @endfor
                                                    </span>
                    
                                                    <div class="message">
                                                        {{ $review->comment }}
                                                    </div>
                    
                                                    <div class="reviewer-details">
                                                        <span class="by">
                                                            {{ __('shop::app.products.by', ['name' => $review->name]) }},
                                                        </span>
                    
                                                        <span class="when">
                                                            {{ core()->formatDate($review->created_at, 'F d, Y') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                        </section>
                    </div>
                </div>
            </div>
        </section>
        <!-- End -->
        <!-- Related Section start -->
        @include ('shop::products.view.related-products')
        <!-- Related Section start -->
        <!-- Upsells Section start -->
        @include ('shop::products.view.up-sells')
        <!-- Upsells Section end -->

    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}
@endsection

@push('scripts')

    <script type="text/x-template" id="product-view-template">
        <form method="POST" id="product-form" action="{{ route('cart.add', $product->product_id) }}" @click="onSubmit($event)">

            <input type="hidden" name="is_buy_now" v-model="is_buy_now">

            <slot></slot>

        </form>
    </script>

    <script type="text/x-template" id="quantity-changer-template">
    @inject ('wishListHelper', 'Webkul\Customer\Helpers\Wishlist')
    <div class="qty-btn-wrap">
        <div class="quantity-info" :class="[errors.has(controlName) ? 'has-error' : '']">
            <button type="button" class="circle decrease" @click="decreaseQty()">-</button>

            <input :name="controlName" class="control qty" :value="qty" :v-validate="validations" data-vv-as="&quot;{{ __('shop::app.products.quantity') }}&quot;" readonly>

            <button type="button" class="circle increase" @click="increaseQty()">+</button>

            <span class="control-error" v-if="errors.has(controlName)">@{{ errors.first(controlName) }}</span>
            
        </div>
        <div class="btn-wrap detail-btns">
            <button type="submit" class="btn btn-cart addtocart">add to cart</button>
            <button type="submit" class="btn btn-cart btn-buy buynow">buy now</button>
        
           
        </div>
    </div>
    
    </script>

    <script>

        Vue.component('product-view', {

            template: '#product-view-template',

            inject: ['$validator'],

            data: function() {
                return {
                    is_buy_now: 0,
                }
            },

            methods: {
                onSubmit: function(e) {
                    if (e.target.getAttribute('type') != 'submit')
                        return;

                    e.preventDefault();

                    var this_this = this;

                    this.$validator.validateAll().then(function (result) {
                        if (result) 
                        {
                            this_this.is_buy_now = e.target.classList.contains('buynow') ? 1 : 0;
                            setTimeout(function() {
                                document.getElementById('product-form').submit();
                            }, 0);
                        }
                    });
                }
            }
        });

        Vue.component('quantity-changer', {
            template: '#quantity-changer-template',

            inject: ['$validator'],

            props: {
                controlName: {
                    type: String,
                    default: 'quantity'
                },

                quantity: {
                    type: [Number, String],
                    default: 1
                },

                minQuantity: {
                    type: [Number, String],
                    default: 1
                },

                validations: {
                    type: String,
                    default: 'required|numeric|min_value:1'
                }
            },

            data: function() {
                return {
                    qty: this.quantity
                }
            },

            watch: {
                quantity: function (val) {
                    this.qty = val;

                    this.$emit('onQtyUpdated', this.qty)
                }
            },

            methods: {
                decreaseQty: function() {
                    if (this.qty > this.minQuantity)
                        this.qty = parseInt(this.qty) - 1;

                    this.$emit('onQtyUpdated', this.qty)
                },

                increaseQty: function() {
                    this.qty = parseInt(this.qty) + 1;

                    this.$emit('onQtyUpdated', this.qty)
                }
            }
        });

        $(document).ready(function() {
            var addTOButton = document.getElementsByClassName('add-to-buttons')[0];
            document.getElementById('loader').style.display="none";
            addTOButton.style.display="flex";
        });

        window.onload = function() {
            var thumbList = document.getElementsByClassName('thumb-list')[0];
            var thumbFrame = document.getElementsByClassName('thumb-frame');
            var productHeroImage = document.getElementsByClassName('product-hero-image')[0];

            if (thumbList && productHeroImage) {

                for(let i=0; i < thumbFrame.length ; i++) {
                    thumbFrame[i].style.height = (productHeroImage.offsetHeight/4) + "px";
                    thumbFrame[i].style.width = (productHeroImage.offsetHeight/4)+ "px";
                }

                if (screen.width > 720) {
                    thumbList.style.width = (productHeroImage.offsetHeight/4) + "px";
                    thumbList.style.minWidth = (productHeroImage.offsetHeight/4) + "px";
                    thumbList.style.height = productHeroImage.offsetHeight + "px";
                }
            }

            window.onresize = function() {
                if (thumbList && productHeroImage) {

                    for(let i=0; i < thumbFrame.length; i++) {
                        thumbFrame[i].style.height = (productHeroImage.offsetHeight/4) + "px";
                        thumbFrame[i].style.width = (productHeroImage.offsetHeight/4)+ "px";
                    }

                    if (screen.width > 720) {
                        thumbList.style.width = (productHeroImage.offsetHeight/4) + "px";
                        thumbList.style.minWidth = (productHeroImage.offsetHeight/4) + "px";
                        thumbList.style.height = productHeroImage.offsetHeight + "px";
                    }
                }
            }
        };
    </script>
    <script>
        // tabs
        $(document).ready(function()
        { 
            
            $('.add-whishlist-btn').on('click',function(){
                if(!$(this).hasClass('already'))
                {
                    $.ajax({
                        url: '{{ route('customer.wishlist.ajax-add', $product->product_id) }}',
                        data: {},
                        type: "GET",
                        success: function (response) 
                        {
                            if(response.auth == false)
                            {
                                window.location.href = "{{ route('customer.session.index') }}";
                            }
                            else if(response.status == 'succsess')
                            {
                                $('.add-whishlist-btn').addClass('already');
                                $('.add-whishlist-btn').removeClass('add-whishlist-btn');
                                $('#add-whishlist-btn').html('<i class="fa fa-heart"></i> <span>add to wishlist</span>');
                                $(".alert-wrapper").html("<div class='alert alert-success'><p>" + response.msg +"</p></div>");
                            }
                            else if(response.status == 'failed')
                            {
                                $(".alert-wrapper").html("<div class='alert alert-error'><p>" + response.msg +"</p></div>");
                            }
                            else if(response.status == 'already')
                            {
                                $(".alert-wrapper").html("<div class='alert alert-info'><p>" + response.msg +"</p></div>");
                            }
                            setTimeout(function() {
                                $(".alert-wrapper").fadeOut('fast');
                            }, 1000);
                        }
                    })
                }
            })

            $('.tab-a').click(function(){  
            $(".tab").removeClass('tab-active');
            $(".tab[data-id='"+$(this).attr('data-id')+"']").addClass("tab-active");
            $(".tab-a").removeClass('active-a');
            $(this).parent().find(".tab-a").addClass('active-a');
            });
        });
        
    </script>
    <script>

        window.onload = (function() {
            var percentage = {};
            <?php foreach ($reviewHelper->getPercentageRating($product) as $key => $count) { ?>

                percentage = <?php echo "'$count';"; ?>
                id = <?php echo "'$key';"; ?>
                idNumber = id + 'star';

                document.getElementById(id).style.width = percentage + "%";
                document.getElementById(id).style.height = 4 + "px";
                document.getElementById(idNumber).innerHTML = id ;

            <?php } ?>
        })();

    </script>
@endpush