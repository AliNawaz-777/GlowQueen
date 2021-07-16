 <!-- On-sale -->
<section id="product" class="product-wrap section-padding sale-sec-padding">
    <div class="container">
     <!-- Jewlery-ites-buttons -->
        <div class="jewelary-buttons">
            <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                <div class="btn-wrap progress">
                    <a href="{{ URL('categories/new-arrivals') }}" target="_self" class="sh-button sh-button-large sh-button-icon-right progress-bar progress-bar-striped progress-bar-animated">
                        <span class="sh-button-icon">
                        </span>
                        <span class="sh-button-text">new arrivals</span>
                    </a>
                    <a href="{{ URL('categories/earrings') }}" target="_self" class="earing-btn sh-button sh-button-large sh-button-icon-right progress-bar progress-bar-striped progress-bar-animated">
                        <span class="sh-button-icon icon-2">
                        </span>
                        <span class="sh-button-text">earrings</span>
                    </a>
                    <a href="{{ URL('categories/new-accessories') }}" target="_self" class="stud-btn sh-button sh-button-large sh-button-icon-right progress-bar progress-bar-striped progress-bar-animated">
                        <span class="sh-button-icon icon-3">
                        </span>
                        <span class="sh-button-text">Accessories</span>
                    </a>
                    <a href="{{ URL('categories/necklace') }}" target="_self" class="necklace-btn sh-button sh-button-large sh-button-icon-right progress-bar progress-bar-striped progress-bar-animated">
                        <span class="sh-button-icon icon-4">
                        </span>
                        <span class="sh-button-text">necklace</span>
                    </a>
                    <a href="{{ URL('categories/silver-jewelry') }}" target="_self" class="silver-btn sh-button sh-button-large sh-button-icon-right progress-bar progress-bar-striped progress-bar-animated">
                        <span class="sh-button-icon icon-5">
                        </span>
                        <span class="sh-button-text">silver jewelry</span>
                    </a>
                </div>
            </div>
        </div>
    <!-- End -->
        <h1 class="sec-title-wrap on-sale-d" style="display:none">On Sale</h1>
        <p class="text-detail on-sale-d" style="display: none;"><span>Enjoy the pleasure of money-saving deals. </span>Buy a range of high-quality jewelry at a very low price through exclusive promotions. </p>
           <!-- Related products -->
           <div id="on-sale-section"></div>
        <!-- 
        @if (app('Webkul\Product\Repositories\ProductRepository')->getOnSaleProducts()->count())
        <div class="related-products sale-products">
            <!-- <div class="on_sale_section">
                @foreach (app('Webkul\Product\Repositories\ProductRepository')->getOnSaleProducts() as $productFlat)

                    @include ('shop::products.list.product-card', ['product' => $productFlat])
    
                @endforeach
            </div> 
        </div>
        @else
        <div class="no-products">No Product Found</div>
        @endif
        @if (app('Webkul\Product\Repositories\ProductRepository')->getOnSaleProducts()->count() > 4)
        <div class="load-more">
            <p>Load More</p>
        </div>  
        @endif
        -->
    </div>
</section>
<!-- End -->