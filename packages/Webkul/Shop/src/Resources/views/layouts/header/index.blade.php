<div class="header" id="header">

    <div class="container">

        <div class="header-top">

            <div class="left-content">

                <ul class="logo-container">

                    <li>

                        <a href="{{ route('shop.home.index') }}">

                            @if ($logo = core()->getCurrentChannel()->logo_url)

                                <!--<img class="logo" src="{{bagisto_asset ('images/logo-01.svg') }}" />-->

                                <img class="logo" src="{{ $logo }}" />

                            @else

                                <!--<img class="logo" src="{{ bagisto_asset('images/logo-01.svg') }}" />-->

                                <img class="logo" src="{{ $logo }}" />

                            @endif

                        </a>

                    </li>

                </ul>

            </div>



            <?php

                $query = parse_url(\Illuminate\Support\Facades\Request::path(), PHP_URL_QUERY);

                $searchTerm = explode("&", $query);

                foreach($searchTerm as $term)

                {

                    if (strpos($term, 'term') !== false) 

                    {

                        $serachQuery = $term;

                    }

                }

            ?>

        

            <span class="menu-box" >

                    <span id="search" class="icon icon-search custom-search"></span>  

                    <span class="dropdown-toggle cart-responsive">

                        

                        @include('shop::checkout.cart.mini-cart')



                                

                    </span>

                    <span class="icon icon-menu icon-close cross-icon" id="hammenu">

                    </span>

            </span>



            <div class="right-content">

                <ul class="right-content-menu">



                    {!! view_render_event('bagisto.shop.layout.header.currency-item.before') !!}



                    @if (core()->getCurrentChannel()->currencies->count() > 1)

                        <li class="currency-switcher">

                            <span class="dropdown-toggle">

                                {{ core()->getCurrentCurrencyCode() }}



                                <i class="icon arrow-down-icon"></i>

                            </span>



                            <ul class="dropdown-list currency">

                                @foreach (core()->getCurrentChannel()->currencies as $currency)

                                    <li>

                                        @if (isset($serachQuery))

                                            <a href="?{{ $serachQuery }}&currency={{ $currency->code }}">{{ $currency->code }}</a>

                                        @else

                                            <a href="?currency={{ $currency->code }}">{{ $currency->code }}</a>

                                        @endif

                                    </li>

                                @endforeach

                            </ul>

                        </li>

                    @endif



                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}





                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}



                    <li>

                        <span>

                            <a href="{{ URL('/') }}">

                            Home

                            </a>

                        </span>

                    </li>








                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}



                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}



                    <li>

                        <span>

                            <a href="{{ URL('about-us') }}">

                            About Us

                            </a>

                        </span>

                    </li>



                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}





                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}



                    <li>

                        <span>



                            <div class="dropdown-category">

                                <a class="dropbtn-category accordion" href="javascript:void(0)">

                                Categories

                                </a>



                                <div class="dropdown-content-category">

                                    <ul>

                                        @foreach (Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen Main Category')->get() as $collection)

                                            @php

                                                $colll = Webkul\Category\Models\Category::where('parent_id', '=', $collection->id)->get();

                                                for ($i=0; $i < count($colll); $i++) 

                                                { 

                                                    $collection_array[$i]['name'] = $colll[$i]['name'];

                                                    $collection_array[$i]['image'] = $colll[$i]['image'];

                                                    $collection_array[$i]['slug'] = $colll[$i]['slug'];

                                                    $collection_array[$i]['status'] = $colll[$i]['status'];

                                                }

                                            @endphp

                                            @foreach ($collection_array as $coll)

                                                @if ($coll['status'] == 1)

                                                        <li>

                                                            <a href="{{ URL('categories/'.$coll['slug']) }}">{{$coll['name']}}</a>

                                                        </li>

                                                @endif

                                            @endforeach

                                        @endforeach

                                    </ul>

                                </div>



                                <div class="panel">

                                    

                                </div>

                            </div>     

                        </span>

                    </li>

                    




                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}



                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}



                    @guest('customer')

                        <li>

                            <span class="item">

                                <a href="{{ route('customer.session.index') }}">

                                    Login

                                </a>

                            </span>

                        </li>



                        <li>

                            <span class="item">

                                <a href="{{ route('customer.register.index') }}">

                                    Register     

                                </a>

                            </span>

                        </li>

                    @endguest



                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}





                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}



                     



                    @auth('customer')

                        <li>

                            <div>

                                <ul class="dropdown-toggle">

                                    <li style="color: #f49814; font-weight: 700; text-transform: uppercase; font-size: 13px;">

                                        <span class="user-dropdown-d">{{ auth()->guard('customer')->user()->first_name }}</span>

                                        <i class="icon arrow-down-icon"></i>

                                    </li>

                                    <ul class="dropdown-list account customer">

                                        <li>

                                            <ul class="account_dropdown">

                                                <li>

                                                    <a href="{{ route('customer.profile.index') }}">{{ __('shop::app.header.profile') }}</a>

                                                </li>



                                                <li>

                                                    <a href="{{ route('customer.wishlist.index') }}">{{ __('shop::app.header.wishlist') }}</a>

                                                </li>



                                                <li>

                                                    <a href="{{ route('shop.checkout.cart.index') }}">{{ __('shop::app.header.cart') }}</a>

                                                </li>



                                                <li>

                                                    <a href="{{ route('customer.session.destroy') }}">{{ __('shop::app.header.logout') }}</a>

                                                </li>

                                            </ul>

                                        </li>

                                    </ul>

                                </ul>                    

                            </div>

                        </li>

                    @endauth



                    <li>

                        <span class="dropdown-toggle">

                            @include('shop::checkout.cart.mini-cart')

                        </span>

                    </li>



                    {!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}





                    {!! view_render_event('bagisto.shop.layout.header.cart-item.before') !!}



                    <li class="nav-item">

                        <span id="search" class="icon icon-search ">                          

                    </li>



                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}





                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}



        

                    {!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}





                    {!! view_render_event('bagisto.shop.layout.header.cart-item.before') !!}



                

                    {!! view_render_event('bagisto.shop.layout.header.cart-item.after') !!}

                   

                </ul>

            </div>

        </div>



        <div class="search-responsive mt-10" id="search-responsive">

            <form role="search" action="{{ route('shop.search.index') }}" method="get" style="display: inherit;">

                <div class="search-content">

                    <input type="search" name="term" placeholder="Search products here" class="search">

                    <button class="right" style="background: none; border: none; padding: 0px;">

                        <i class="icon icon-search"></i>

                    </button>

                </div>

            </form>

        </div>

        

    </div>

</div>



@push('scripts')

    <script>

        $(document).ready(function() 

        {

            $('body').on('click','.user-dropdown-d',  function(e) {

                e.preventDefault();

                setTimeout(() => {

                   // $('.arrow-down-icon').click();

                    //$('.arrow-down-icon').click();

                }, 200);

            });

            $('body').delegate('#search, .icon-menu-close, .icon.icon-menu', 'click', function(e) {

                setTimeout(function(){

					$('.search-content > input').focus();

				},200);

                toggleDropdown(e);

            });



            // $('body').delegate('#menu, .icon-menu-close, .icon.icon-menu', 'click', function(e) {

            //     toggleDropdown(e);

            // });



            function toggleDropdown(e) {

                // alert('hELLO')

                var currentElement = $(e.currentTarget);

                // alert(currentElement.hasClass('icon-search'))

                if (currentElement.hasClass('icon-search')) {

                    currentElement.removeClass('icon-search');

                    currentElement.addClass('icon-menu-close');

                    // $('#search').removeClass('icon-menu-close');

                    // $('#search').addClass('icon-search');

                    $("#search-responsive").css("display", "block");

                    // $('.right-content-menu').slideDown();

                    // $("#search-responsive").slideUp();

                    $("#header-bottom").css("display", "none");

                } else if (currentElement.hasClass('icon-menu')) {

                    currentElement.removeClass('icon-menu');

                    currentElement.addClass('icon-menu-close');

                    // $('#hammenu').removeClass('icon-menu-close');

                    // $('#hammenu').addClass('icon-menu');

                    // $("#search-responsive").css("display", "none");

                    $('.right-content-menu').slideDown();

                    $("#search-responsive").slideUp();

                    // $("#header-bottom").css("display", "block");

                } else {

                    currentElement.removeClass('icon-menu-close');

                    // $("#search-responsive").css("display", "none");

                    

                    $("#header-bottom").css("display", "none");

                    

                    if (currentElement.attr("id") == 'search') {

                        currentElement.addClass('icon-search');

                        $("#search-responsive").slideUp();

                    } else {

                        currentElement.addClass('icon-menu');

                        $('.right-content-menu').slideUp();

                    }

                }

            }

        });

    </script>

@endpush

