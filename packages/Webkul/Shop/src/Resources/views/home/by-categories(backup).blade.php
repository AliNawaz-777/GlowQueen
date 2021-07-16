<section class="featured-products">

    <div class="featured-heading">

        {{ __('COLLECTIONS') }}<br/>



        <span class="featured-seperator" style="color:lightgrey;">_____</span>

    </div>

    <div class="container">
        <div class="row">
            <div class="featured-grid product-grid-5">

                <div class="collection-card">

                    <a href="{{ URL('categories/winter-collection') }}">

                        <div class="collection-img">

                            <div style="background-image: url('{{ asset('static-images/1N3A2133-1-scaled.jpg') }}');"></div>

                        </div>

                        <div class="collection-card-text">

                            <h5>Winter Collections {{ date('Y') }}</h5>

                        </div>     

                    </a>

                </div>

            </div>

            <div class="featured-grid product-grid-5">

                <div class="collection-card">

                    <a href="{{ URL('categories/fall-collection') }}">

                        <div class="collection-img">

                            <div style="background-image: url('{{ asset('static-images/1N3A4952-Copy-1.jpg') }}');"></div>

                        </div>

                        <div class="collection-card-text">

                            <h5>Fall Collections {{ date('Y') }}</h5>

                        </div>     

                    </a>

                </div>

            </div>

            <div class="featured-grid product-grid-5">

                <div class="collection-card">

                    <a href="{{ URL('categories/summer-collection') }}">

                        <div class="collection-img">

                            <div style="background-image: url('{{ asset('static-images/649A9675-e1552394509449.jpg') }}');"></div>

                        </div>

                        <div class="collection-card-text">

                            <h5>Summer Collections {{ date('Y') }}</h5>

                        </div>     

                    </a>

                </div>

            </div>
        </div>
    </div>    

</section>