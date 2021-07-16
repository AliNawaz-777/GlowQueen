@extends('shop::layouts.master')


@section('page_title')
    {{ $category->meta_title ?? $category->name }}
@stop

@section('seo')
    <meta name="description" content="{{ $category->meta_description }}"/>
    <meta name="keywords" content="{{ $category->meta_keywords }}"/>
@stop



@section('content-wrapper')
    @inject ('productRepository', 'Webkul\Product\Repositories\ProductRepository')

<section id="product" class="product-wrap section-padding">
<div class="container">
    <div class="main">
        {!! view_render_event('bagisto.shop.products.index.before', ['category' => $category]) !!}

        <div class="category-container">

            <!--@if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))-->
            <!--    @include ('shop::products.list.layered-navigation')-->
            <!--@endif-->

            <div class="category-block" @if ($category->display_mode == 'description_only') style="width: 100%" @endif>
              {{--   <div class="hero-image mb-35">
                    @if (!is_null($category->image))
                        <img class="logo" src="{{ $category->image_url }}" />
                    @endif
                </div> --}}

                @if (in_array($category->display_mode, [null, 'description_only', 'products_and_description']))
                    @if ($category->description)
                        <div class="category-description">
                            <div class="site-heading">{!! $category->description !!}</div>
                        </div>
                    @endif
                @endif
                <div class="category-block related-products sale-products">
                @include ('shop::products.list.toolbar_demo')

                @if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))
                    <?php $products = $productRepository->getAll($category->id); ?>
                    <div class="row">
                    @if ($products->count())

                        <!--@include ('shop::products.list.toolbar_demo')-->

                        @inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

                        @if ($toolbarHelper->getCurrentMode() == 'grid')
                                <!--<div class="product-detail">-->
                                    
                                @foreach ($products as $productFlat)
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6">
                                            {{--  @include ('shop::products.list.card', ['product' => $productFlat]) --}}
                                            @include ('shop::products.list.product-card-cats', ['product' => $productFlat])
                                    </div>
                                @endforeach
                                <!--</div>-->
                        @else
                            <!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">-->
                            <!--    <div class="product-detail">-->
                            <!--        <div class="product-grid-3">-->
                                    @foreach ($products as $productFlat)
                                            
                                        {{--  @include ('shop::products.list.card', ['product' => $productFlat]) --}}
                                        @include ('shop::products.list.footer-new-product', ['product' => $productFlat])
                                    @endforeach
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                        @endif

                    @else

                        <div class="product-list empty">
                            <h2>{{ __('shop::app.products.whoops') }}</h2>

                            <p>
                                {{ __('shop::app.products.empty') }}
                            </p>
                        </div>

                    @endif
                    </div>
                    {!! view_render_event('bagisto.shop.products.index.pagination.before', ['category' => $category]) !!}

                    <div class="bottom-toolbar">
                        {{ $products->appends(request()->input())->links() }}
                    </div>

                    {!! view_render_event('bagisto.shop.products.index.pagination.after', ['category' => $category]) !!}
                @endif
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.products.index.after', ['category' => $category]) !!}
    </div>
</div>
</section>
@stop

@push('scripts')
    <script>
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
        });
    </script>
@endpush