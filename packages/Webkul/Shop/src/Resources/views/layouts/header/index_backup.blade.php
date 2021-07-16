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

                foreach($searchTerm as $term){
                    if (strpos($term, 'term') !== false) {
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
                        {{-- <div class="menu-responsive mt-10" id="menu-responsive">
                            <form role="search" action="{{ route('shop.search.index') }}" method="GET" style="display: inherit;">

                                <div class="search-content">
                                    <input type="search" name="term" placeholder="Search products here" class="search">
                                    <button class="right" style="background: none; border: none; padding: 0px;">
                                        <i class="icon icon-search"></i>
                                    </button>
                                </div>
                            </form>
                                                </div> --}}
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


                    <li>
                        <span>
                            <a href="{{ URL('/shop') }}">
                            SHOP
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

                        {{--  --}}

             
   



                        {{--  --}}

                
                    <div class="dropdown-category">
                            <a class="dropbtn-category accordion" href="javascript:void(0)">
                            Categories
                            </a>



                        <div class="dropdown-content-category">
                            <ul>

                        @foreach (Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen Main Category')->get() as $collection)

                        @php
                        $colll = Webkul\Category\Models\Category::where('parent_id', '=', $collection->id)->get();
                        
                        for ($i=0; $i < count($colll); $i++) { 
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
    
                            </ul>

                    @endforeach
                        
                        </div>

                        <div class="panel">
                            
                        </div>

                    

                   </div>     

                            

                        </span>
                    </li>
                    
                    <li>
                        <span>
                             <a href="{{ URL('/blog') }}">
                                Blog
                            </a>
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

                     

                          
                                {{-- <i class="icon arrow-down-icon"></i> --}}
                    </li>
                    @endguest

               

                    {{--  --}}


                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}


                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}

                     

                  @auth('customer')
                  <li>
                    <div>
                            
                   
                        {{-- <span class="name">{{ __('shop::app.header.account') }}</span> --}}

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

                            
                            {{-- <a class="nav-link icon-circle" href="#"><i class="fa fa-lock"></i></a> --}}



                            {{-- <span class="name">{{ __('shop::app.header.account') }}</span> --}}
                            
                        </span>

                    </li>

                   

                    {{--  @guest('customer')
                            <ul class="dropdown-list account guest">
                                <li>
                                    <div>
                                        <label style="color: #9e9e9e; font-weight: 700; text-transform: uppercase; font-size: 15px;">
                                            {{ __('shop::app.header.title') }}
                                        </label>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <span style="font-size: 12px;">{{ __('shop::app.header.dropdown-text') }}</span>
                                    </div>

                                    <div style="margin-top: 15px;">
                                        <a class="btn btn-primary btn-md" href="{{ route('customer.session.index') }}" style="color: #ffffff">
                                            {{ __('shop::app.header.sign-in') }}
                                        </a>

                                        <a class="btn btn-primary btn-md" href="{{ route('customer.register.index') }}" style="float: right; color: #ffffff">
                                            {{ __('shop::app.header.sign-up') }}
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        @endguest --}}

                       
                    </li>

                    {!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}


                    {!! view_render_event('bagisto.shop.layout.header.cart-item.before') !!}
                    {{--  --}}

                        
                    <li class="nav-item">

                     <span id="search" class="icon icon-search ">
                       {{-- <span id="search"><a class="nav-link" href="#"></a></span> --}}
                          
                    </li>

                  {{--   <li class="nav-item">
                        <a class="nav-link" href="#">
                            <span id="menu" class="icon icon-menu"></span>  
                        </a>
                    </li> --}}

                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}


                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}

                    {{--  @auth('customer')
                            <ul class="dropdown-list account customer">
                                <li>
                                    <div>
                                        <label style="color: #9e9e9e; font-weight: 700; text-transform: uppercase; font-size: 15px;">
                                            {{ auth()->guard('customer')->user()->first_name }}
                                        </label>
                                    </div>

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
                        @endauth --}}


                        {!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}


                    {!! view_render_event('bagisto.shop.layout.header.cart-item.before') !!}

                  {{--   <li class="cart-dropdown-container">

                        @include('shop::checkout.cart.mini-cart')

                    </li> --}}


                    {!! view_render_event('bagisto.shop.layout.header.cart-item.after') !!}
                   
                </ul>

                <!-- <span class="menu-box" >
                    <span id="search" class="icon icon-search custom-search"></span>  
                    <span class="dropdown-toggle cart-responsive">

                        @include('shop::checkout.cart.mini-cart')

                                
                    </span>
                    <span class="icon icon-menu icon-close cross-icon" id="hammenu">
                        {{-- <div class="menu-responsive mt-10" id="menu-responsive">
                            <form role="search" action="{{ route('shop.search.index') }}" method="GET" style="display: inherit;">

                                <div class="search-content">
                                    <input type="search" name="term" placeholder="Search products here" class="search">
                                    <button class="right" style="background: none; border: none; padding: 0px;">
                                        <i class="icon icon-search"></i>
                                    </button>
                                </div>
                            </form>
                                                </div> --}}
                    </span>
                </span> -->
            </div>
        </div>

        {{-- <div class="header-bottom" id="header-bottom">
            @include('shop::layouts.header.nav-menu.navmenu')
        </div> --}}

        <div class="search-responsive mt-10" id="search-responsive">
            <form role="search" action="{{ route('shop.search.index') }}" method="POST" style="display: inherit;">
                <div class="search-content">
                    @csrf
                    <input type="search" name="term" placeholder="Search products here" class="search">
                    <button class="right" style="background: none; border: none; padding: 0px;">
                        <i class="icon icon-search"></i>
                    </button>
                </div>
            </form>
        </div>
        {{-- <div class="menu-responsive mt-10" id="menu-responsive">
            <form role="search" action="{{ route('shop.search.index') }}" method="GET" style="display: inherit;">
                <div class="search-content">
                    <input type="search" name="term" placeholder="Search products here" class="search">
                    <button class="right" style="background: none; border: none; padding: 0px;">
                        <i class="icon icon-search"></i>
                    </button>
                </div>
            </form>
        </div> --}}
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

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
