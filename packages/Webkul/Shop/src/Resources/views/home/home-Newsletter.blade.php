

<section id="newsletter" class="newsletter">

					<div class="container">

						<div class="newsletter-wrap">

							<div class="customer-sub-sec customer-sec">

								<div class="owl-carousel owl-theme main-slider">

									<?php $testimonials = app('Webkul\Product\Repositories\TestimonailRepository')->getall(); 

									?>



									@foreach($testimonials as $testimonial)

									<?php $testimonial_image = app('Webkul\Product\Repositories\TestimonailImageRepository')->getImages($testimonial->id); 

									

									?>

									

									<div class="item">

										<div class="customer-sec-padding">

											<h1>Customers Love With glowQueen</h1>

											<div class="customer-content">

													<div class="img-holder img-circle">
                                            @if(isset($testimonial_image[0]))
														<img src="{{ asset('storage/'.$testimonial_image[0]->path) }}" alt="" class="img-fluid">
                                        @endif
													</div>

													<div class="block-holder">

														<blockquote>

															<q>{{ $testimonial->short_description }}.</q>

															<cite class="text-capitalize">{{ $testimonial->name }}

															@php

																$date = date('d.m.Y', strtotime($testimonial->created_at));

															@endphp

															<span>{{ $date }}</span></cite>

														</blockquote>	

													</div>

											</div>

										</div>

									</div>

									@endforeach

									

								</div>

							</div>

							<div class="customer-sub-sec subscribe-sec">

								<div class="subscribe-sec-padding">

									<h1>subscribe for newsletter</h1>

									



					<form action="{{ route('shop.subscribe') }}">

                            <div class="control-group" :class="[errors.has('subscriber_email') ? 'has-error' : '']">

                               <input type="email" class="control subscribe-field" name="subscriber_email" placeholder="Email Address" required><br/>



                                <button class="btn btn-theme-orange"><span>subscribe</span></button>

                            </div>

                     </form>



								</div>

							</div>

						</div>

					</div>

				</section>





@push('scripts')



<script type="text/javascript">



	$(document).ready(function(e){

	$('.main-slider').owlCarousel({

		loop:true,

		nav:true,

		items:1,

		autoplay:true,

		autoplayTimeout:3000,

		navText: [

			'<p>PREV</p>',

			'<p>NEXT</p>'

		],

	});

});







</script>

@endpush