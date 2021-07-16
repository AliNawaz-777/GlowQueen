@inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

@php



    $seo = json_decode(core()->getCurrentChannel()->home_seo);

    $og_img = '';

    $og_description = '';

    

    if(isset($product)){

        $images = $productImageHelper->getGalleryImages($product);

        $og_img = $images[0]['original_image_url'];

        $og_description = trim($product->meta_description) != "" ? $product->meta_description : str_limit(strip_tags($product->description), 120, '');

    }

    else if(isset($category)){

        $og_img = $category->image;

        $og_description = $category->meta_description;

    }

    else if (isset($blogData)){

        if(isset($blogData->images))
            $og_img = "";
            if(isset($blogData->images[0]))
               $og_img = asset('storage/'.$blogData->images[0]->path);

        else

            $og_img = 'https://www.glowqueen.pk/storage/slider_images/Default/p7pOaB04xK4nH9cIuv8JmkMcbHBeqW6dde0P87Kc.jpeg';

        if(isset($blogData->meta_description))

        $og_description = trim($blogData->meta_description) != "" ? $blogData->meta_description : str_limit(strip_tags($blogData->description), 120, '');

        else

        $og_description = $page->meta_description;

    }

    else{

        $og_img = 'https://www.glowqueen.pk/storage/slider_images/Default/V8YgSVS6AnxFYsIbVyJVPLDiByAmRiR1mD52SFyL.jpeg';

        $og_description = "GlowQueen.PK.";

    }

@endphp

<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">



<head>
    <meta name="facebook-domain-verification" content="eipvb5k7n6b3p3a2676lvti2qtq7ed" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">



    <title>@yield('page_title')</title>



    

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">

    <meta name="google-site-verification" content="Bsj0Lq_Hfj-_daRu_vM1Hod-K8RXBtqIKP41jXZXdOg" />



    {{-- style --}}

    

    <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">

    <link rel="stylesheet" href="{{ bagisto_asset('css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ bagisto_asset('css/shop.min.css') }}">

    <link rel="stylesheet" href="{{ bagisto_asset('css/custom.css') }}">

    <link rel="stylesheet" href="{{ bagisto_asset('css/font-awesome.min.css') }}">

    {{-- <link rel="stylesheet" href="{{ bagisto_asset('css/owl.carousel.min.css') }}"> --}}

   <link rel="stylesheet" type="text/css" href=" https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

   <link rel="stylesheet" href="{{ asset('vendor/webkul/front/css/glowstyle.css') }}">
   
    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-172000040-1"></script>

    <script>

      window.dataLayer = window.dataLayer || [];

      function gtag(){dataLayer.push(arguments);}

      gtag('js', new Date());

    

      gtag('config', 'UA-172000040-1');

    </script>



    @if ($favicon = core()->getCurrentChannel()->favicon_url)

        <link rel="icon" sizes="16x16" href="{{ $favicon }}" />

    @else

        <link rel="icon" sizes="16x16" href="{{ bagisto_asset('images/favicon.ico') }}" />

    @endif



    @yield('head')



    @section('seo')

        @if (! request()->is('/'))

            <meta name="description" content="{{ core()->getCurrentChannel()->description }}"/>

        @endif

    @show


    @stack('css')

    <meta property="og:title" content="@yield('page_title')">

    <meta property="og:site_name" content="GlowQueen">

    <meta property="og:url" content="{{ url()->full() }}">

    <meta property="og:description" content="{{ $og_description }}">

    <meta property="og:type" content="website">

    <meta property="og:image" content="{{ $og_img }}">

   <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '453023112496317');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=453023112496317&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->



    {!! view_render_event('bagisto.shop.layout.head') !!}



</head>





<body @if (core()->getCurrentLocale()->direction == 'rtl') class="rtl" @endif style="scroll-behavior: smooth;">



    {!! view_render_event('bagisto.shop.layout.body.before') !!}



    <div id="app">

        <div class="loader"></div>



        <flash-wrapper ref='flashes'></flash-wrapper>

        {!! view_render_event('bagisto.shop.layout.header.before') !!}



            @include('shop::layouts.header.index')



            {!! view_render_event('bagisto.shop.layout.header.after') !!}



            {!! view_render_event('bagisto.shop.layout.content.before') !!}

            @include("shop::home.slider") 

            @yield('slider')



            {!! view_render_event('bagisto.shop.layout.content.after') !!}



        <div class="main-container-wrapper custom">



            



            <div class="content-container">



                {!! view_render_event('bagisto.shop.layout.content.before') !!}



                @yield('content-wrapper')



                {!! view_render_event('bagisto.shop.layout.content.after') !!}



            </div>



        </div>



        {!! view_render_event('bagisto.shop.layout.footer.before') !!}



        @include('shop::layouts.footer.footer')



        {!! view_render_event('bagisto.shop.layout.footer.after') !!}



        @if (core()->getConfigData('general.content.footer.footer_toggle'))

            <div class="footer">

                <p style="text-align: center;">

                    @if (core()->getConfigData('general.content.footer.footer_content'))

                        {{ core()->getConfigData('general.content.footer.footer_content') }}

                    @else

                        {{ trans('admin::app.footer.copy-right') }}

                    @endif

                </p>

            </div>

        @endif

    </div>



    <script type="text/javascript">

        window.flashMessages = [];



        @if ($success = session('success'))

            window.flashMessages = [{'type': 'alert-success', 'message': "{{ $success }}" }];

        @elseif ($warning = session('warning'))

            window.flashMessages = [{'type': 'alert-warning', 'message': "{{ $warning }}" }];

        @elseif ($error = session('error'))

            window.flashMessages = [{'type': 'alert-error', 'message': "{{ $error }}" }

            ];

        @elseif ($info = session('info'))

            window.flashMessages = [{'type': 'alert-info', 'message': "{{ $info }}" }

            ];

        @endif



        window.serverErrors = [];

        @if(isset($errors))

            @if (count($errors))

                window.serverErrors = @json($errors->getMessages());

            @endif

        @endif

    </script>



    <script type="text/javascript" src="{{ bagisto_asset('js/main.js') }}"></script>

    <script type="text/javascript" src="{{ bagisto_asset('js/slide.js') }}"></script>

    <script type="text/javascript" src="{{ bagisto_asset('js/bootstrap.min.js') }}"></script>

    <!--<script type="text/javascript" src="{{ bagisto_asset('js/main.min.js') }}"></script>-->

    <script>

    var is_buy_now = '';

    $(document).on('click','.buy-now-d', function(e) 

    {

        is_buy_now = 1;

    })

        

    $("form input[type=submit]").click(function() {

        $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");

        $(this).attr("clicked", "true");

    });



        $(document).on("submit", ".addToCart", function (e) {

            

            e.preventDefault();

            var focus_btn = $(this).find("button[type=submit]:focus" );



            var data = $(this).serialize();

            var product_id = $(this).attr('data-generated');

    

            $.ajax({

                url: "{{ route('cart.add', '') }}" + "/" + product_id,

                type: "POST",

                data: data,

                success: function (response) {

                    var result = JSON.parse(response);

                    if (result.status == 'fail') {

                        if (result.return_url != '' || result.return_url != null) {

                            // location.replace(result.return_url);

                            $(".alert-wrapper").html("<div class='alert alert-error'><p>" + result.message +"</p></div>");

                        }

                    } else {

                        $(".alert-wrapper").html("<div class='alert alert-success'><p>" + result.message +"</p></div>");

                        $('.cart-link').attr('href',"{{ route('shop.checkout.cart.index') }}");
                        $(".cart-qty").html(result.cart_data.items.length);

                        var sub_total = parseFloat(result.cart_data.sub_total);

                        $(".dropdown-cart .dropdown-header").html("<p>Cart Subtotal - " + result.currency + ' ' + sub_total.toFixed(2) + "</p>");

                        var items = result.cart_data.items;

                        var myhtml = '';

                        

                        for (i = 0; i < items.length; i++)
                        {

                            console.log(items[i]);

                            var product_url = "{{ URL('products') }}" + "/" + items[i].url_key;

                            myhtml += '<div class="item">';

                            myhtml += '<div class="item-image" >';

                            myhtml += '<a href="' + product_url + '"><img src="' + result.images[i].small_image_url + '" /></a>';

                            myhtml += '</div>';

                            myhtml += '<div class="item-details">';

                            myhtml += '<div class="item-name"><a href="' + product_url + '">' + items[i].name + '</a></div>';

                            if ('attributes' in items[i].additional) {

                                myhtml += '<div class="item-options">';

                                var attributes = items[i].additional.attributes;

                                for (j = 0; j < attributes.length; j++) {

                                    myhtml += '<b>' + attributes[j].attribute_name + ': </b>' + attributes[j].attribute_option + '<br>';

                                }

                                myhtml += '</div>';

                            }

                            var item_price = parseFloat(items[i].base_total);

                            myhtml += '<div class="item-price"><b>' + result.currency + ' ' + item_price.toFixed(2) + '</b></div>';

                            myhtml += '<div class="item-qty">Quantity - ' + items[i].quantity + '</div>';

                            myhtml += '</div>';

                            myhtml += '</div>';

                        }

                        

                        $(".dropdown-content").html(myhtml);

                        $(".dropdown-footer a").removeAttr("disabled");

                        $(".dropdown-footer #custom-view-cart").attr("href","{{ route('shop.checkout.cart.index') }}");

                        $(".dropdown-footer #custom-buy-btn").attr("href","{{ route('shop.checkout.onepage.index') }}");



                        if(focus_btn.hasClass("buy-now-d") || is_buy_now)

                        {

                            setTimeout(() => {

                                window.location.href = "/checkout/onepage";

                            }, 1500);

                        }

                    }

                    

                    setTimeout(() => {

                        $(".alert-wrapper").html("");

                    }, 5000);

                }

            })

        });

        

         $(document).ready(function() {

              

          // lists();

          slideMove();

            

            $(".loader").fadeOut(3000);

            

        });

        var slide_interval = '';

        function slideMove()

        {

            slide_interval =   setInterval(function()

            {

                if($('.dropdown-open .dropdown-open.active').length > 0)

                {

                    $('.slider-right').trigger('click');

                    $('.dropdown-open .dropdown-open').addClass('active');

                    $('.dropdown-open .dropdown-toggle #cart-badge').addClass('active');

                    $('.dropdown-open .dropdown-toggle #cart-list').css('display','block');

                }  

                else

                {

                    $('.slider-right').trigger('click');

                }

                

            }, 5000);

        }

        

        $('body').on('click','.slider-right , .slider-left',function(){

            clearInterval(slide_interval);

            slideMove();

        });



        function lists() {

            var lists = $(".slider-images li");

            // console.log(lists[0])

                if($('ul.slider-images li.show').length > 1) {

                    

                    setTimeout(function(){

                        $('.slider-images li:first').removeClass("show");

                    },50);

                   

                    console.log(" OUT LISTS");

                }

            $.each(lists, function (index, element) {

                $(this) === element;

                

                if ($(this).hasClass("show")){

                    if($('ul.slider-images li.show').length > 1) {

                    setTimeout(function(){

                            $('.slider-images li:first').removeClass("show");

                        },50);

                        console.log("LISTS OUT");

                    }

                    setTimeout(() => {

                        if (index + 1 < lists.length) {

                            check_slider_next($(this));     

                        }

                        else{

                            restart($(this), lists.first());

                        }

                    }, 5000)

                }

            })

        }

        function check_slider_next(list) {

            if($('ul.slider-images li.show').length > 1) {

                setTimeout(function(){

                    $('.slider-images li:first').removeClass("show");

                },60);

                console.log("NEXT INNER");

            }

            list.removeClass("show");

            list.next().addClass("show");

            lists();

        }



        function restart(current, list) {

            current.removeClass("show");

            list.addClass("show");

            lists();

        }



          function topFunction() {

              window.scrollTo({top: 0, behavior: 'smooth'});

                  // document.body.scrollTop = 0;

                  // document.documentElement.scrollTop = 0;

                }





$('body').on('click','.slider-right',function() {

    if($('ul.slider-images li.show').length > 1) {

        setTimeout(function(){

            $('.slider-images li').find('.show:first').removeClass("show");

        },50);

        

    }

});





var acc = document.getElementsByClassName("accordion");

var i;



for (i = 0; i < acc.length; i++) {

  acc[i].addEventListener("click", function() {

    this.classList.toggle("active");

    var panel = this.nextElementSibling;

    if (panel.style.display === "block") {

      panel.style.display = "none";

    } else {

      panel.style.display = "block";

    }

  });

}



    $(document).ready(function(){

        

        $("#close_alert").on('click',function(){

            $(this).parent().parent().hide();

        });

        $('.msg').fadeIn().delay(5000).fadeOut();

            

        getSalesProducts();   

        getNewProducts();

        // getJustforYouProducts();

        getShopCategoryProducts();

    });

    

    function slider_list(list) {

        setTimeout(() => {

            console.log("Hello")

        }, 5000)

    }

    

    function getSalesProducts(id = null){

        $( '<div class="spinner-border remove-loader-d" role="status"><span class="sr-only">Loading...</span></div>' ).insertBefore( ".saleLoader" );

        $(".saleLoader").remove();

        var _token = $("meta[name='csrf-token']").attr("content");



        var sales_exclude = '';

        $('.sales_exclude').each(function(){

            sales_exclude += $(this).val();

            sales_exclude += ',';

        });

        

        $.ajax({

            url: '{{ route("home.sales.products") }}',

            data: {id:id, _token: _token , sales_exclude: sales_exclude},

            type: "GET",

            success: function (response) {

                if(response.length < 1) {

                    $('.on-sale-d').hide();

                } else {

                    $('.on-sale-d').show();

                    $("#on-sale-section").append(response);

                }

            },

            complete: function(){

                $('.remove-loader-d').remove();

            }

        })

    }

    

    function getNewProducts(id = null){

        $( '<div class="spinner-border remove-loader-d" role="status"><span class="sr-only">Loading...</span></div>' ).insertBefore( ".newProductsLoader" );



        $(".newProductsLoader").remove();

        var _token = $("meta[name='csrf-token']").attr("content");

        var newarrival_exclude = '';

        $('.newarrival_exclude').each(function(){

            newarrival_exclude += $(this).val();

            newarrival_exclude += ',';

        });



        

        $.ajax({

            url: '{{ route("home.new.products") }}',

            data: {id:id, _token: _token,newarrival_exclude:newarrival_exclude},

            type: "GET",

            success: function (response) {

                if(response.length < 1) {

                    $('.new-arrival-d').hide();

                } else {

                    $('.new-arrival-d').show();

                    $("#new-products-section").append(response);

                }

            },

            complete: function(){

                $('.remove-loader-d').remove();

            }

        })

    }



    function getShopCategoryProducts(id = null)

    {

        $( '<div class="spinner-border remove-loader-d" role="status"><span class="sr-only">Loading...</span></div>' ).insertBefore( ".newProductsLoader" );



        $(".newProductsLoader").remove();

        var _token = $("meta[name='csrf-token']").attr("content");

        var shop_exclude = '';

        if(id)

        {

            $('.shop_exclude').each(function()

            {

                shop_exclude += $(this).val();

                shop_exclude += ',';

            });

        }

        

        var category_id = $('#category-drop-down').val();

        var sort_filter = $('#sort-filter').val();

        var cat_search = $('.input-search-category-d').val();

        price_range = ( typeof price_range !== 'undefined'  ) ? price_range : '';

        $.ajax({

            url: '{{ route("home.ajax.products") }}',

            data: {id:id, _token: _token,shop_exclude:shop_exclude,category_id:category_id,sort_filter:sort_filter,cat_search:cat_search,price_range:price_range},

            type: "GET",

            success: function (response) 

            {

                if(response.length < 1) 

                {

                    $("#empty-product").show();

                    $("#empty-product").addClass('empty');

                    $("#ajax-products-section").html(' ');

                    $("#empty-product").html('<h2>{{ __('shop::app.products.whoops') }}</h2>');

                } 

                else 

                {

                    $("#empty-product").hide();

                    if(id)

                    {

                        $("#ajax-products-section").append(response);

                    }

                    else

                    {

                        $("#ajax-products-section").html(response);

                    }

                    

                }

            },

            complete: function(){

                $('.remove-loader-d').remove();

            }

        })

    }



    function getJustforYouProducts(id = null){

        

        $( '<div class="spinner-border remove-loader-d" role="status"><span class="sr-only">Loading...</span></div>' ).insertBefore( ".jfuLoader" );



        $(".jfuLoader").hide();

        var _token = $("meta[name='csrf-token']").attr("content");

        var justforyou_exclude = '';

        $('.justforyou_exclude').each(function() {

            justforyou_exclude += $(this).val();

            justforyou_exclude += ',';

        });



        $.ajax({

            url: '{{ route("home.justforyou.products") }}',

            data: {id:id, _token: _token,justforyou_exclude:justforyou_exclude},

            type: "GET",

            success: function (response) {

                if(response.length < 1)

                {

                    $('.just-for-sty').hide();

                }

                else

                {

                    $('.just-for-sty').show();

                    $("#just-for-you-products-section").append(response);

                }                

            },

            complete: function(){

                $('.remove-loader-d').remove();

            }

        })

    }



    </script>





   



    @stack('scripts')



    {!! view_render_event('bagisto.shop.layout.body.after') !!}



    <div class="modal-overlay"></div>


</body>



</html>