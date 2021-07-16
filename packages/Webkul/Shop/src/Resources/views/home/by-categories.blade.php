@if (count(Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen-main')->get()) > 0)
    <section class="featured-products">
   {{--      <div class="featured-heading">
            {{ __('COLLECTIONS') }}<br/>

            <span class="featured-seperator" style="color:lightgrey;">_____</span>
        </div> --}}
        <div class="container">
            <div class="row">  
            @php 
//print_r(Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQuen-main')->get());

            @endphp
                @php 
                    //Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQuen-main')->get() 
                @endphp       
                @foreach (Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQuen-main')->get() as $collection)
                
                    @php
                        //$colll = Webkul\Category\Models\Category::where('parent_id', '=', $collection->id)->get();

                        $colll = Webkul\Category\Models\Category::whereIn('id',  [37,33])->get();
                        
                        for ($i=0; $i < count($colll); $i++) { 
                            $collection_array[$i]['name'] = $colll[$i]['name'];
                            $collection_array[$i]['image'] = $colll[$i]['image'];
                            $collection_array[$i]['slug'] = $colll[$i]['slug'];
                        }

                    @endphp
                    <div class="product-bottom-sec">
                    @foreach (array_reverse($collection_array) as $coll)

                    
                    
                
                {{-- <div class="img-box img2">
                    <a href="#" class="btn btn-theme-white">
                        <span>view detail</span></a>
                </div> --}}
           
                        @include ('shop::products.list.collection-card', ['collections' => $coll])
                    @endforeach    
                     </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
