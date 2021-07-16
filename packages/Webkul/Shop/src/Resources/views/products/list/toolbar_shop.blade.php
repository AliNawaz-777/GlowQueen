<div class="top-toolbar mb-35">
    <div class="row">
        <div class="col-12 d-inline-block d-md-none">
			<div class="text-right filter-icons">
				<span class="sort-icon d-inline-block align-top"></span>
				<span class="filter-icon d-inline-block align-top"></span>
			</div>
		</div>
		<div class="col-lg-5 col-12">
			<div class="layered-filter-wrapper">
				<layered-navigation></layered-navigation>
			</div>
        </div>

		<div class="col-lg-6 offset-lg-1 col-12">
            <div class="pager">
        
                <div class="sorter">
                    <label>{{ __('shop::app.products.sort-by') }}</label>
        
                    <select id="sort-filter" onchange="getShopCategoryProducts()">
                        <option value="name,asc">
                            From A-Z
                        </option> 
                        <option value="name,desc">
                            From Z-A
                        </option> 
                        <option value="created_at,desc" selected="selected">
                            Newest First
                        </option> 
                        <option value="created_at,asc">
                            Oldest First
                        </option> 
                        <option value="price,asc">
                            Cheapest First
                        </option> 
                        <option value="price,desc">
                            Expensive First
                        </option>
                    </select>

                </div>
    
                @if(Request::segment(1) != 'categories')
                <div class="limiter">
                    <label>Category</label>
                    
                    <select id="category-drop-down" onchange="getShopCategoryProducts()">
                        <option value=""> Select Category </option>
                        @foreach (Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen Main Category')->get() as $collection)
                            @php
                                $colll = Webkul\Category\Models\Category::where('parent_id', '=', $collection->id)->get();
                                
                                for ($i=0; $i < count($colll); $i++) { 
                                    $collection_array[$i]['id'] = $colll[$i]['id'];
                                    $collection_array[$i]['name'] = $colll[$i]['name'];
                                    $collection_array[$i]['image'] = $colll[$i]['image'];
                                    $collection_array[$i]['slug'] = $colll[$i]['slug'];
                                    $collection_array[$i]['status'] = $colll[$i]['status'];
                                }
                            @endphp
                            @foreach ($collection_array as $coll)
                                @if ($coll['status'] == 1)
                                    <option value="{{ $coll['id'] }}" {{ (request()->get('category') == $coll['id'])  ? 'selected' : '' }}>
                                    {{ $coll['name'] }}
                                    </option>
                                @endif
                            @endforeach
                        @endforeach
        
                    </select>

                </div>
                @endif
        
            </div>
        </div>
    </div>
</div>


<div class="responsive-layred-filter mb-20">
    <layered-navigation></layered-navigation>
</div>