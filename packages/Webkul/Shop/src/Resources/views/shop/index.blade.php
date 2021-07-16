@extends('shop::layouts.master')


@section('page_title')
    Shop | GlowQueen.PK
@stop

@section('seo')
    {{-- <meta name="description" content="{{ $category->meta_description }}"/>
    <meta name="keywords" content="{{ $category->meta_keywords }}"/> --}}
@stop

@section('content-wrapper')
    @inject ('productRepository', 'Webkul\Product\Repositories\ProductRepository')
<section id="product" class="product-wrap section-padding">
<div class="container">
    <div class="main">
      

        <div class="category-page-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="text-uppercase text-left">
                        Shop
                    </h2>
                </div>
                <div class="col-md-6">
                    <div class="search-form-holder pull-right">
                        <form class="search-form">
                            <div class="form-group mb-0">
                                <input type="text" placeholder="Search" name="search-category" class="input-search-category-d" value="{{ request()->get('search') }}" />
                            </div>
                            <button type="submit" class="search-category-d"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="category-container">

            <!--@if (in_array('products_and_description', [null, 'products_only', 'products_and_description']))-->
            <!--    @include ('shop::products.list.layered-navigation-shop')-->
            <!--@endif-->

            <div class="category-block">

                <div class="category-block related-products sale-products">
                    @include ('shop::products.list.toolbar_shop')
 
                    <div class="row" id="ajax-prod-div">
                        <div id="empty-product" class="product-list">
                        </div>
                        <div id="ajax-products-section" style="text-align:center;width:100%"></div>
                    
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
</section>
@stop

@push('scripts')
    <script>
        var price_range = '';
        $(document).ready(function() {
            $('.responsive-layred-filter').css('display','none');
            $(".sort-icon, .filter-icon").on('click', function(e){
                var currentElement = $(e.currentTarget);
                if (currentElement.hasClass('sort-icon')) {
                    currentElement.removeClass('sort-icon');
                    currentElement.addClass('icon-menu-close-adj');
                    currentElement.next().removeClass();
                    currentElement.next().addClass('icon filter-icon');
                    $('.responsive-layred-filter').css('display','none');
                    $('.pager').css('display','flex');
                    $('.pager').css('justify-content','space-between');
                } else if (currentElement.hasClass('filter-icon')) {
                    currentElement.removeClass('filter-icon');
                    currentElement.addClass('icon-menu-close-adj');
                    currentElement.prev().removeClass();
                    currentElement.prev().addClass('icon sort-icon');
                    $('.pager').css('display','none');
                    $('.responsive-layred-filter').css('display','block');
                    $('.responsive-layred-filter').css('margin-top','10px');
                } else {
                    currentElement.removeClass('icon-menu-close-adj');
                    $('.responsive-layred-filter').css('display','none');
                    $('.pager').css('display','none');
                    if ($(this).index() == 0) {
                        currentElement.addClass('sort-icon');
                    } else {
                        currentElement.addClass('filter-icon');
                    }
                }
            });
            
            $('body').on('click','.search-category-d',function(event)
            {
                event.preventDefault();
                getShopCategoryProducts()
            });

        });

        $(window).on('scroll', function() 
        { 
            if ($(window).scrollTop() >= $('#ajax-prod-div').offset().top + $('#ajax-prod-div').outerHeight() - window.innerHeight) 
            { 
                var last_id = $('#last-show-id').attr('data-id');
                $('#last-show-id').remove();
                if(typeof last_id !== 'undefined'){
                    getShopCategoryProducts( last_id );
                }
            } 
        });

        
</script>
@endpush