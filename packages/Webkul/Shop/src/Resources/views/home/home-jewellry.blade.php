@if (count(Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen Main Category')->get()) > 0)



 <section id="jewelary-item" class="jewelary-item">

                    <div class="container">

                    	@foreach (Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen Main Category')->get() as $collection)

                {{-- @dd($collection) --}}

                    @php

                        $colll = Webkul\Category\Models\Category::where('parent_id', '=', $collection->id)->get();

                        

                        for ($i=0; $i < count($colll); $i++) { 

                            $collection_array[$i]['name'] = $colll[$i]['name'];

                            $collection_array[$i]['image'] = $colll[$i]['image'];

                            $collection_array[$i]['slug'] = $colll[$i]['slug'];

                        }

                    @endphp

@endforeach

                        <div class="j-item-btn">

                            <a href="{{ URL('categories/'.$collection_array[0]['slug']) }}" class="btn btn-theme-orange btn-item">

                                <span>view all new items</span></a>

                        </div>

                      

                    </div>

                    

  </section>



@endif