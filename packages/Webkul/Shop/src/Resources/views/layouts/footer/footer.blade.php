<footer id="footer" class="footer">

				<div class="container">

					<div class="footer-wrap">

						<div class="footer-content">

							<div class="footer-logo">
								@if ($logo = core()->getCurrentChannel()->logo_url)
								<img src="{{ $logo }}" alt="" class="img-fluid"/>
								@else
								<img src="{{bagisto_asset ('images/logo-01.svg') }}" alt="" class="img-fluid"/>
								@endif

								

								<p>GlowQueen brings you the best quality, life-changing beauty products and makeup palettes from world-famous brands to help you stay on trend.  </p>

								<h3>Follow us</h3>

								<ul>

									<li><a href="https://www.facebook.com/glowqueen.pk" target="_blank"><i class="fa fa-facebook-f"></i></a></li>

									<li><a href="https://www.instagram.com/glowqueen.pk/" target="_blank"><i class="fa fa-instagram"></i></a></li>

									<!--<li>	<a href="https://api.whatsapp.com/send?phone=923012345678&amp;text=I am interested in your Product" target="_blank"><i class="fa fa-whatsapp"></i></a></li>-->

									<!--<li><a href="#"><i class="fa fa-twitter"></i></a></li>-->

									<!--<li><a href="#"><i class="fa fa-linkedin"></i></a></li>-->

								</ul>

							</div>

						</div>

						<div class="footer-content footer-sec-pad">

							<div class="footer-links">

								<h3>useful links</h3>

						<ul>

					@foreach (Webkul\Category\Models\CategoryTranslation::where('name', 'GlowQueen Main Category')->get() as $collection)



                        @php

                        $slug_array = [];

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

						 	 		@php $slug_array[] = $coll['slug']; @endphp

	                                <li>

	                                	<a href="{{ URL('categories/'.$coll['slug']) }}">{{$coll['name']}}</a>

	                                </li>

									@endif

                                @endforeach

								<?php $cms_pages = \DB::table('cms_pages')->get();?>
									@foreach($cms_pages as $sms)
										<li><a href="{{ URL($sms->url_key) }}">{{ str_replace("-"," ",$sms->url_key) }}</a></li>
									@endforeach
                        </ul>



                      @endforeach



							</div>

						</div>











					@if (app('Webkul\Product\Repositories\ProductRepository')->Toprated()->count())



		

					<div class="footer-content footer-sec-pad">

							<div class="footer-products">

								<h3>top rated products</h3>

								<div class="p-img-holder">

							@foreach (app('Webkul\Product\Repositories\ProductRepository')->Toprated() as $productFlat)



				   @inject ('productImageHelper', 'Webkul\Product\Helpers\ProductImage')

				   <?php $productBaseImage = $productImageHelper->getProductBaseImage($productFlat); ?>





								

							<div class="img-box product-1">

				             <a href="{{ route('shop.products.index', $productFlat->url_key) }}">

						<img src="{{ $productBaseImage['medium_image_url'] }}" style="width: 100%;height:100%;">



				             </a>

										

							</div>



								@endforeach

								</div>

							</div>

						</div>

					</div>

				</div>

				<button class="goto-top"  onclick="topFunction()" title="Go to top">

					<p>TOP</p>

				</button>

				<div class="footer-copyright">

					<p>Copyright  © glowQueen.pk.  All Right Reserved {{ date('Y') }}</p>
					
				</div>

			</footer>



			@endif



			